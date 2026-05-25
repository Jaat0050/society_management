<?php

namespace App\Livewire\Resident;

use App\Livewire\Concerns\WithResidentLayout;
use App\Models\VisitorEntry;
use App\Services\VisitorService;
use Livewire\Component;

class Visitors extends Component
{
    use WithResidentLayout;

    public string $visitor_name = '';

    public string $phone = '';

    public string $visit_type = 'guest';

    public int $person_count = 1;

    public string $purpose = '';

    public function preRegister(): void
    {
        $member = auth()->user()->member;

        $data = $this->validate([
            'visitor_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'visit_type' => 'required|in:guest,delivery,cab,domestic_help,vendor,other',
            'person_count' => 'required|integer|min:1|max:10',
            'purpose' => 'nullable|string|max:500',
        ]);

        $entry = app(VisitorService::class)->createEntry([
            'society_id' => auth()->user()->society_id,
            'flat_id' => $member?->flat_id,
            'member_id' => $member?->id,
            'created_by' => auth()->id(),
            ...$data,
        ]);

        session()->flash('success', "Visitor pass created. Pass: {$entry->pass_code}".($entry->approval_otp ? " | OTP: {$entry->approval_otp}" : ''));
        $this->reset(['visitor_name', 'phone', 'purpose']);
    }

    public function render()
    {
        $member = auth()->user()->member;

        return $this->residentLayout('My Visitors')
            ->with([
                'entries' => $member
                    ? VisitorEntry::where('member_id', $member->id)->orWhere('flat_id', $member->flat_id)->latest()->take(20)->get()
                    : collect(),
            ]);
    }
}
