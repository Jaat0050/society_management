<?php

namespace App\Http\Controllers\Api;

use App\Models\DeliveryStaff;
use App\Models\Flat;
use App\Models\Member;
use App\Models\MemberVehicle;
use App\Models\SecurityIncident;
use App\Models\SosAlert;
use App\Models\StaffAttendance;
use App\Models\StaffMember;
use App\Models\VisitorEntry;
use App\Support\VisitorPassPresenter;
use App\Services\VisitorService;
use Illuminate\Http\Request;

class GuardApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $sid = $this->societyId();

        return $this->ok([
            'pending' => VisitorEntry::where('society_id', $sid)->where('status', 'pending')->count(),
            'inside' => VisitorEntry::where('society_id', $sid)->where('status', 'checked_in')->count(),
            'active_sos' => SosAlert::where('society_id', $sid)->where('status', 'active')->count(),
        ]);
    }

    public function newEntry(Request $request)
    {
        $sid = $this->societyId();
        $data = $request->validate([
            'visitor_name' => 'required|string',
            'phone' => 'nullable|string',
            'flat_id' => 'nullable|exists:flats,id',
            'visit_type' => 'required|in:guest,delivery,cab,domestic_help,vendor,other',
            'delivery_staff_id' => 'nullable|integer',
            'vehicle_no' => 'nullable|string',
            'person_count' => 'nullable|integer|min:1|max:20',
            'purpose' => 'nullable|string',
        ]);

        $entry = app(VisitorService::class)->createEntry([
            'society_id' => $sid,
            'flat_id' => $data['flat_id'] ?? null,
            'delivery_staff_id' => $data['delivery_staff_id'] ?? null,
            'created_by' => $request->user()->id,
            'visitor_name' => $data['visitor_name'],
            'phone' => $data['phone'] ?? null,
            'visit_type' => $data['visit_type'],
            'vehicle_no' => $data['vehicle_no'] ?? null,
            'person_count' => $data['person_count'] ?? 1,
            'purpose' => $data['purpose'] ?? null,
        ]);

        $entry->load('flat');

        return $this->ok([
            'entry' => $entry,
            'gate_pass' => VisitorPassPresenter::toArray($entry),
            'qr_url' => url('/visit/qr/'.$entry->qr_token),
            'approval_url' => $entry->approvalUrl(),
        ], 'Gate pass created', 201);
    }

    public function verify(Request $request)
    {
        $lookup = trim($request->input('lookup', ''));
        $service = app(VisitorService::class);
        $token = strlen($lookup) > 12 ? $lookup : null;
        $pass = strlen($lookup) <= 12 ? $lookup : null;

        $entry = $service->findByTokenOrPass($token, $pass);

        if (! $entry || $entry->society_id !== $this->societyId()) {
            return $this->fail('Pass not found', 404);
        }

        if ($request->filled('otp') && ! $service->verifyOtp($entry, $request->otp)) {
            return $this->fail('Invalid OTP', 422);
        }

        return $this->ok($entry->load(['flat', 'deliveryStaff']));
    }

    public function checkIn(Request $request, int $id)
    {
        try {
            $entry = VisitorEntry::where('society_id', $this->societyId())->findOrFail($id);
            app(VisitorService::class)->checkIn($entry);

            return $this->ok($entry->fresh(), 'Checked IN');
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage(), 422);
        }
    }

    public function checkOut(Request $request, int $id)
    {
        $entry = VisitorEntry::where('society_id', $this->societyId())->findOrFail($id);
        app(VisitorService::class)->checkOut($entry);

        return $this->ok($entry->fresh(), 'Checked OUT');
    }

    public function inside(Request $request)
    {
        $list = VisitorEntry::where('society_id', $this->societyId())
            ->where('status', 'checked_in')
            ->with('flat')
            ->get();

        return $this->ok($list);
    }

    public function log(Request $request)
    {
        $list = VisitorEntry::where('society_id', $this->societyId())
            ->with(['flat', 'deliveryStaff'])
            ->latest()
            ->paginate(30);

        return $this->ok($list);
    }

    public function deliveryStaff(Request $request)
    {
        return $this->ok(DeliveryStaff::where('society_id', $this->societyId())->where('is_active', true)->get());
    }

    public function flats(Request $request)
    {
        return $this->ok(Flat::where('society_id', $this->societyId())->orderBy('flat_no')->get(['id', 'block', 'flat_no', 'status']));
    }

    public function sosAlerts(Request $request)
    {
        return $this->ok(SosAlert::where('society_id', $this->societyId())->with(['flat', 'member'])->latest()->paginate(20));
    }

    public function resolveSos(Request $request, int $id)
    {
        $alert = SosAlert::where('society_id', $this->societyId())->findOrFail($id);
        $alert->update(['status' => 'resolved', 'acknowledged_at' => now()]);

        return $this->ok($alert, 'SOS resolved');
    }

    public function reportIncident(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'nullable|string',
        ]);

        $incident = SecurityIncident::create([
            ...$data,
            'society_id' => $this->societyId(),
            'reported_by' => $request->user()->id,
            'occurred_at' => now(),
        ]);

        return $this->ok($incident, 'Incident reported', 201);
    }

    public function verifyVehicle(Request $request)
    {
        $request->validate(['vehicle_no' => 'required|string']);
        $vehicle = MemberVehicle::where('society_id', $this->societyId())
            ->where('vehicle_no', 'like', '%'.$request->vehicle_no.'%')
            ->with('member')
            ->first();

        return $this->ok($vehicle ?? ['found' => false]);
    }

    public function markAttendance(Request $request)
    {
        $data = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|in:present,absent,leave,half_day',
        ]);

        StaffAttendance::updateOrCreate(
            ['staff_id' => $data['staff_id'], 'attendance_date' => today()],
            ['status' => $data['status']]
        );

        return $this->ok(null, 'Attendance recorded');
    }

    public function staffList(Request $request)
    {
        return $this->ok(StaffMember::where('society_id', $this->societyId())->where('is_active', true)->get(['id', 'name', 'role']));
    }
}
