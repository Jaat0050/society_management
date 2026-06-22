<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorApiController extends Controller
{
    protected function vendorForUser(Request $request): ?Vendor
    {
        return Vendor::where('society_id', $request->user()->society_id)
            ->where(fn ($q) => $q->where('email', $request->user()->email)->orWhere('phone', $request->user()->phone))
            ->first();
    }

    public function tasks(Request $request)
    {
        $vendor = $this->vendorForUser($request);

        $query = Complaint::where('society_id', $this->societyId());
        if ($vendor) {
            $query->where('assigned_vendor_id', $vendor->id);
        }

        return $this->ok($query->with(['flat', 'logs'])->latest()->paginate(20));
    }

    public function updateTask(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:in_progress,resolved',
            'resolution_notes' => 'nullable|string',
        ]);

        $complaint = Complaint::where('society_id', $this->societyId())->findOrFail($id);
        $complaint->update($data);

        return $this->ok($complaint, 'Task updated');
    }

    public function submitInvoice(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'reference_no' => 'nullable|string',
        ]);

        return $this->ok([
            'invoice' => $data,
            'status' => 'submitted',
            'message' => 'Invoice submitted for accountant review',
        ], 'Invoice submitted', 201);
    }

    public function schedule(Request $request)
    {
        $vendor = $this->vendorForUser($request);

        return $this->ok([
            'contract_end' => $vendor?->contract_end?->format('Y-m-d'),
            'schedule_notes' => $vendor?->schedule_notes,
            'pending_tasks' => Complaint::where('assigned_vendor_id', $vendor?->id)->whereIn('status', ['open', 'in_progress'])->count(),
        ]);
    }

    public function payments(Request $request)
    {
        return $this->ok([
            'message' => 'Payment tracking — contact society accountant',
            'pending_invoices' => 0,
        ]);
    }
}
