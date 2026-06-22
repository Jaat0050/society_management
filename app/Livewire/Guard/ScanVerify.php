<?php

namespace App\Livewire\Guard;

use App\Livewire\Concerns\WithGuardLayout;
use App\Models\VisitorEntry;
use App\Services\VisitorService;
use Livewire\Component;

class ScanVerify extends Component
{
    use WithGuardLayout;

    public string $lookup = '';

    public ?VisitorEntry $entry = null;

    public string $message = '';

    public function verify(): void
    {
        $this->lookup = trim($this->lookup);
        $this->message = '';
        $this->entry = null;

        if ($this->lookup === '') {
            $this->message = 'Enter pass code or scan QR token.';

            return;
        }

        $service = app(VisitorService::class);
        $token = strlen($this->lookup) > 12 ? $this->lookup : null;
        $pass = strlen($this->lookup) <= 12 ? $this->lookup : null;

        $this->entry = $service->findByTokenOrPass($token, $pass);

        if (! $this->entry) {
            $this->message = 'No visitor pass found.';

            return;
        }

        if ($this->entry->society_id !== auth()->user()->society_id) {
            $this->entry = null;
            $this->message = 'This pass belongs to another society.';

            return;
        }

        $this->message = 'Pass found: '.$this->entry->statusLabel();
    }

    public function checkIn(int $id): void
    {
        try {
            $entry = VisitorEntry::where('society_id', auth()->user()->society_id)->findOrFail($id);
            app(VisitorService::class)->checkIn($entry);
            session()->flash('success', 'Visitor checked IN at gate.');
            $this->verify();
        } catch (\Throwable $e) {
            $this->message = $e->getMessage();
        }
    }

    public function checkOut(int $id): void
    {
        $entry = VisitorEntry::where('society_id', auth()->user()->society_id)->findOrFail($id);
        app(VisitorService::class)->checkOut($entry);
        session()->flash('success', 'Visitor checked OUT.');
        $this->verify();
    }

    public function render()
    {
        return $this->guardLayout('Scan / Verify QR');
    }
}
