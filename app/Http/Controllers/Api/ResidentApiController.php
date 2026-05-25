<?php

namespace App\Http\Controllers\Api;

use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\Complaint;
use App\Models\ComplaintLog;
use App\Models\ComplaintRating;
use App\Models\EmergencyAlert;
use App\Models\Event;
use App\Models\MaintenanceBill;
use App\Models\Notice;
use App\Models\PaymentTransaction;
use App\Models\SosAlert;
use App\Models\VisitorEntry;
use App\Support\VisitorPassPresenter;
use App\Services\VisitorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResidentApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $member = $user->member;
        $flatId = $member?->flat_id;
        $sid = $user->society_id;

        return $this->ok([
            'pending_bills' => $flatId ? MaintenanceBill::where('flat_id', $flatId)->whereIn('status', ['pending', 'overdue'])->count() : 0,
            'open_complaints' => Complaint::where('flat_id', $flatId)->whereIn('status', ['open', 'in_progress'])->count(),
            'active_alerts' => EmergencyAlert::where('society_id', $sid)->where('is_active', true)->count(),
            'visitors_pending' => VisitorEntry::where('member_id', $member?->id)->where('status', 'pending')->count(),
        ]);
    }

    public function bills(Request $request)
    {
        $flatId = $request->user()->member?->flat_id;
        if (! $flatId) {
            return $this->ok([]);
        }

        $bills = MaintenanceBill::where('flat_id', $flatId)->orderByDesc('bill_month')->get()
            ->map(fn ($b) => $this->billPayload($b));

        return $this->ok($bills);
    }

    public function paymentHistory(Request $request)
    {
        $flatId = $request->user()->member?->flat_id;

        $txns = PaymentTransaction::where('flat_id', $flatId)
            ->where('status', 'success')
            ->latest()
            ->get();

        return $this->ok($txns);
    }

    public function payBill(Request $request, int $id)
    {
        if (! config('mobile_features.payment_gateway_enabled', false)) {
            return $this->fail('Online payment is temporarily disabled. Please pay at the society office or use bank transfer.', 503);
        }

        return $this->fail('Payment gateway not configured.', 503);
    }

    public function complaints(Request $request)
    {
        $flatId = $request->user()->member?->flat_id;
        $list = Complaint::where('flat_id', $flatId)->with('logs')->latest()->paginate(20);

        return $this->ok($list);
    }

    public function storeComplaint(Request $request)
    {
        $member = $request->user()->member;
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:maintenance,security,parking,noise,other',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $complaint = Complaint::create([
            ...$data,
            'priority' => $data['priority'] ?? 'medium',
            'society_id' => $request->user()->society_id,
            'flat_id' => $member?->flat_id,
            'user_id' => $request->user()->id,
            'status' => 'open',
        ]);

        ComplaintLog::create(['complaint_id' => $complaint->id, 'user_id' => $request->user()->id, 'status' => 'open', 'note' => 'Raised via mobile app']);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('complaints/'.$complaint->id, 'public');
                \DB::table('complaint_media')->insert([
                    'complaint_id' => $complaint->id,
                    'file_path' => $path,
                    'type' => str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return $this->ok($complaint, 'Complaint submitted', 201);
    }

    public function rateComplaint(Request $request, int $id)
    {
        $complaint = Complaint::where('flat_id', $request->user()->member?->flat_id)->findOrFail($id);

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        ComplaintRating::updateOrCreate(
            ['complaint_id' => $complaint->id, 'user_id' => $request->user()->id],
            $data
        );

        return $this->ok(null, 'Thank you for your feedback');
    }

    public function visitors(Request $request)
    {
        $member = $request->user()->member;
        $entries = VisitorEntry::where(fn ($q) => $q->where('member_id', $member?->id)->orWhere('flat_id', $member?->flat_id))
            ->with('flat')
            ->latest()
            ->paginate(20);

        return $this->ok($entries);
    }

    public function preRegisterVisitor(Request $request)
    {
        $member = $request->user()->member;
        $data = $request->validate([
            'visitor_name' => 'required|string',
            'phone' => 'nullable|string',
            'visit_type' => 'required|in:guest,delivery,cab,domestic_help,vendor,other',
            'person_count' => 'nullable|integer|min:1|max:10',
            'purpose' => 'nullable|string',
        ]);

        $entry = app(VisitorService::class)->createEntry([
            'society_id' => $request->user()->society_id,
            'flat_id' => $member?->flat_id,
            'member_id' => $member?->id,
            'created_by' => $request->user()->id,
            'visitor_name' => $data['visitor_name'],
            'phone' => $data['phone'] ?? null,
            'visit_type' => $data['visit_type'],
            'person_count' => $data['person_count'] ?? 1,
            'purpose' => $data['purpose'] ?? null,
        ]);

        $entry->load('flat');

        return $this->ok([
            'entry' => $entry,
            'gate_pass' => VisitorPassPresenter::toArray($entry),
            'approval_url' => $entry->approvalUrl(),
            'otp' => $entry->approval_otp,
        ], 'Gate pass created', 201);
    }

    public function visitorPass(Request $request, int $id)
    {
        $member = $request->user()->member;
        $entry = VisitorEntry::where('society_id', $request->user()->society_id)
            ->where(fn ($q) => $q->where('member_id', $member?->id)->orWhere('flat_id', $member?->flat_id))
            ->with('flat')
            ->findOrFail($id);

        return $this->ok(VisitorPassPresenter::toArray($entry));
    }

    public function approveVisitor(Request $request, int $id)
    {
        $entry = VisitorEntry::where('society_id', $request->user()->society_id)
            ->where(fn ($q) => $q->where('member_id', $request->user()->member?->id)->orWhere('flat_id', $request->user()->member?->flat_id))
            ->findOrFail($id);

        if ($request->filled('otp')) {
            if (! app(VisitorService::class)->approveByOtp($entry, $request->otp)) {
                return $this->fail('Invalid OTP');
            }
        } else {
            app(VisitorService::class)->approve($entry, $request->user()->id);
        }

        return $this->ok($entry->fresh(), 'Visitor approved');
    }

    public function notices(Request $request)
    {
        $list = Notice::where('society_id', $this->societyId())->whereNotNull('published_at')->latest('published_at')->paginate(20);

        return $this->ok($list);
    }

    public function events(Request $request)
    {
        $list = Event::where('society_id', $this->societyId())->where('is_published', true)->orderBy('starts_at')->get();

        return $this->ok($list);
    }

    public function amenities(Request $request)
    {
        return $this->ok(Amenity::where('society_id', $this->societyId())->where('is_active', true)->get());
    }

    public function bookings(Request $request)
    {
        $flatId = $request->user()->member?->flat_id;

        return $this->ok(AmenityBooking::where('flat_id', $flatId)->with('amenity')->latest()->get());
    }

    public function bookAmenity(Request $request)
    {
        $data = $request->validate([
            'amenity_id' => 'required|exists:amenities,id',
            'booked_from' => 'required|date|after:now',
            'booked_to' => 'required|date|after:booked_from',
            'notes' => 'nullable|string',
        ]);

        $booking = AmenityBooking::create([
            ...$data,
            'flat_id' => $request->user()->member?->flat_id,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return $this->ok($booking, 'Booking requested', 201);
    }

    public function cancelBooking(Request $request, int $id)
    {
        $booking = AmenityBooking::where('flat_id', $request->user()->member?->flat_id)->findOrFail($id);
        $booking->update(['status' => 'cancelled']);

        return $this->ok($booking, 'Booking cancelled');
    }

    public function sos(Request $request)
    {
        $member = $request->user()->member;
        $data = $request->validate(['message' => 'nullable|string|max:500']);

        $alert = SosAlert::create([
            'society_id' => $request->user()->society_id,
            'member_id' => $member?->id,
            'flat_id' => $member?->flat_id,
            'contact_phone' => $request->user()->phone,
            'message' => $data['message'] ?? 'Emergency SOS from mobile app',
            'status' => 'active',
        ]);

        return $this->ok($alert, 'SOS sent to security team', 201);
    }

    public function emergencyContacts(Request $request)
    {
        return $this->ok([
            ['label' => 'Society Security', 'phone' => $request->user()->society?->phone ?? '—'],
            ['label' => 'Police', 'phone' => '100'],
            ['label' => 'Ambulance', 'phone' => '108'],
            ['label' => 'Fire', 'phone' => '101'],
        ]);
    }

    protected function billPayload(MaintenanceBill $b): array
    {
        return [
            'id' => $b->id,
            'title' => $b->title,
            'bill_month' => $b->bill_month,
            'amount' => $b->amount,
            'penalty_amount' => $b->penalty_amount,
            'total_due' => $b->totalDue(),
            'status' => $b->status,
            'due_date' => $b->due_date?->format('Y-m-d'),
            'paid_at' => $b->paid_at?->format('Y-m-d'),
            'can_pay_online' => config('mobile_features.payment_gateway_enabled', false),
        ];
    }
}
