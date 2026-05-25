<?php

namespace App\Livewire\Visit;

use App\Models\VisitorEntry;
use App\Services\VisitorService;
use Livewire\Component;

class Approve extends Component
{
    public string $token;

    public ?VisitorEntry $entry = null;

    public string $message = '';

    public string $otp = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->entry = VisitorEntry::with(['society', 'flat', 'member'])
            ->where('qr_token', $token)
            ->first();

        if (! $this->entry) {
            $this->message = 'Invalid or expired visitor pass.';
        }
    }

    public function approveByOtp(): void
    {
        if (! $this->entry || $this->entry->status !== 'pending') {
            return;
        }

        if (! app(VisitorService::class)->approveByOtp($this->entry, $this->otp)) {
            $this->message = 'Invalid or expired OTP.';

            return;
        }

        $this->entry->refresh();
        $this->message = 'Approved via OTP! Visitor can enter at the gate.';
        session()->flash('success', 'Approved with OTP.');
    }

    public function approve(): void
    {
        if (! $this->entry || $this->entry->status !== 'pending') {
            return;
        }

        app(VisitorService::class)->approve($this->entry);
        $this->entry->refresh();
        $this->message = 'Visitor approved! They can enter at the gate.';
        session()->flash('success', 'Approved successfully.');
    }

    public function reject(): void
    {
        if (! $this->entry || $this->entry->status !== 'pending') {
            return;
        }

        app(VisitorService::class)->reject($this->entry, 'Rejected by resident');
        $this->entry->refresh();
        $this->message = 'Visitor request rejected.';
    }

    public function render()
    {
        return view('livewire.visit.approve')
            ->layout('layouts.guest', ['title' => 'Visitor Approval']);
    }
}
