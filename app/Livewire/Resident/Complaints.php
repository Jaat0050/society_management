<?php

namespace App\Livewire\Resident;

use App\Livewire\Concerns\WithResidentLayout;
use App\Models\Complaint;
use App\Models\ComplaintLog;
use Livewire\Component;

class Complaints extends Component
{
    use WithResidentLayout;

    public string $subject = '';

    public string $description = '';

    public string $category = 'maintenance';

    public function submit(): void
    {
        $member = auth()->user()->member;
        $data = $this->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:maintenance,security,parking,noise,other',
        ]);

        $c = Complaint::create([
            ...$data,
            'society_id' => auth()->user()->society_id,
            'flat_id' => $member?->flat_id,
            'user_id' => auth()->id(),
            'priority' => 'medium',
            'status' => 'open',
        ]);

        ComplaintLog::create(['complaint_id' => $c->id, 'user_id' => auth()->id(), 'status' => 'open', 'note' => 'Raised by resident']);

        session()->flash('success', 'Complaint submitted.');
        $this->reset(['subject', 'description']);
    }

    public function render()
    {
        $flatId = auth()->user()->member?->flat_id;

        return $this->residentLayout('My Complaints')
            ->with([
                'complaints' => $flatId
                    ? Complaint::where('flat_id', $flatId)->latest()->get()
                    : collect(),
            ]);
    }
}
