<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Complaint;
use App\Models\ComplaintLog;
use App\Models\Flat;
use App\Models\StaffMember;
use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class Complaints extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout, WithPagination;

    public bool $showForm = false;

    public ?int $selectedId = null;

    public string $subject = '';

    public string $description = '';

    public string $category = 'maintenance';

    public string $priority = 'medium';

    public ?int $flat_id = null;

    public string $status = 'in_progress';

    public string $resolution_notes = '';

    public ?int $assigned_staff_id = null;

    public ?int $assigned_vendor_id = null;

    public function create(): void
    {
        $this->ensureSocietyContext();
        $data = $this->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:maintenance,security,parking,noise,other',
            'priority' => 'required|in:low,medium,high',
            'flat_id' => 'nullable|exists:flats,id',
        ]);

        $c = Complaint::create([
            ...$data,
            'society_id' => $this->societyId(),
            'user_id' => auth()->id(),
            'status' => 'open',
        ]);

        ComplaintLog::create(['complaint_id' => $c->id, 'user_id' => auth()->id(), 'status' => 'open', 'note' => 'Complaint created']);

        session()->flash('success', 'Complaint created.');
        $this->showForm = false;
        $this->reset(['subject', 'description', 'flat_id']);
    }

    public function select(int $id): void
    {
        $c = Complaint::where('society_id', $this->societyId())->findOrFail($id);
        $this->selectedId = $id;
        $this->status = $c->status;
        $this->resolution_notes = $c->resolution_notes ?? '';
        $this->assigned_staff_id = $c->assigned_staff_id;
        $this->assigned_vendor_id = $c->assigned_vendor_id;
    }

    public function updateStatus(): void
    {
        $c = Complaint::where('society_id', $this->societyId())->findOrFail($this->selectedId);

        $c->update([
            'status' => $this->status,
            'resolution_notes' => $this->resolution_notes,
            'assigned_staff_id' => $this->assigned_staff_id,
            'assigned_vendor_id' => $this->assigned_vendor_id,
            'resolved_at' => in_array($this->status, ['resolved', 'closed']) ? now() : null,
        ]);

        ComplaintLog::create([
            'complaint_id' => $c->id,
            'user_id' => auth()->id(),
            'status' => $this->status,
            'note' => $this->resolution_notes,
        ]);

        session()->flash('success', 'Complaint updated.');
        $this->selectedId = null;
    }

    public function render()
    {
        $sid = $this->societyId();

        return $this->societyAdminLayout('Complaint Management')
            ->with([
                'complaints' => Complaint::where('society_id', $sid)->with(['flat', 'assignedStaff', 'assignedVendor'])->latest()->paginate(10),
                'flats' => Flat::where('society_id', $sid)->get(),
                'staff' => StaffMember::where('society_id', $sid)->where('is_active', true)->get(),
                'vendors' => Vendor::where('society_id', $sid)->where('is_active', true)->get(),
                'logs' => $this->selectedId
                    ? ComplaintLog::where('complaint_id', $this->selectedId)->with('user')->latest()->get()
                    : collect(),
            ]);
    }
}
