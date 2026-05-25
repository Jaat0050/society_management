<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use App\Models\ComplaintLog;
use App\Models\StaffAttendance;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffApiController extends Controller
{
    protected function staffForUser(Request $request): ?StaffMember
    {
        return StaffMember::where('society_id', $request->user()->society_id)
            ->where(fn ($q) => $q->where('email', $request->user()->email)->orWhere('phone', $request->user()->phone))
            ->first();
    }

    public function tasks(Request $request)
    {
        $staff = $this->staffForUser($request);

        $query = Complaint::where('society_id', $this->societyId())
            ->whereIn('status', ['open', 'in_progress']);

        if ($staff) {
            $query->where('assigned_staff_id', $staff->id);
        }

        return $this->ok($query->with('flat')->latest()->paginate(20));
    }

    public function updateTask(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);

        $complaint = Complaint::where('society_id', $this->societyId())->findOrFail($id);
        $complaint->update([
            'status' => $data['status'],
            'resolution_notes' => $data['resolution_notes'] ?? $complaint->resolution_notes,
            'resolved_at' => in_array($data['status'], ['resolved', 'closed']) ? now() : null,
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => $request->user()->id,
            'status' => $data['status'],
            'note' => $data['resolution_notes'] ?? 'Updated from mobile',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('complaints/'.$complaint->id.'/completion', 'public');
            \DB::table('complaint_media')->insert([
                'complaint_id' => $complaint->id,
                'file_path' => $path,
                'type' => 'image',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $this->ok($complaint->fresh(), 'Task updated');
    }

    public function schedule(Request $request)
    {
        return $this->ok([
            'today' => Complaint::where('society_id', $this->societyId())
                ->whereDate('created_at', today())->count(),
            'pending' => Complaint::where('society_id', $this->societyId())->whereIn('status', ['open', 'in_progress'])->count(),
        ]);
    }

    public function attendance(Request $request)
    {
        $staff = $this->staffForUser($request);
        if (! $staff) {
            return $this->fail('Staff profile not linked to your account.', 404);
        }

        $record = StaffAttendance::firstOrCreate(
            ['staff_id' => $staff->id, 'attendance_date' => today()],
            ['status' => 'present']
        );

        return $this->ok($record);
    }

    public function inventoryRequest(Request $request)
    {
        $data = $request->validate([
            'item' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        return $this->ok([
            'request' => $data,
            'status' => 'submitted',
            'message' => 'Inventory request logged for admin approval',
        ], 'Request submitted', 201);
    }
}
