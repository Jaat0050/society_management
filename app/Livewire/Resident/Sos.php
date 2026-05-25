<?php

namespace App\Livewire\Resident;

use App\Livewire\Concerns\WithResidentLayout;
use App\Models\SosAlert;
use Livewire\Component;

class Sos extends Component
{
    use WithResidentLayout;

    public string $message = 'Emergency assistance needed';

    public function trigger(): void
    {
        $member = auth()->user()->member;

        SosAlert::create([
            'society_id' => auth()->user()->society_id,
            'member_id' => $member?->id,
            'flat_id' => $member?->flat_id,
            'contact_phone' => auth()->user()->phone,
            'message' => $this->message,
            'status' => 'active',
        ]);

        session()->flash('success', 'SOS alert sent to society security team.');
    }

    public function render()
    {
        return $this->residentLayout('Emergency SOS');
    }
}
