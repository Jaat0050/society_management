<?php

namespace App\Livewire\Guard;

use App\Livewire\Concerns\WithGuardLayout;
use App\Models\VisitorEntry;
use App\Services\VisitorService;
use Livewire\Component;

class PassDetail extends Component
{
    use WithGuardLayout;

    public VisitorEntry $entry;

    public function mount(int $id): void
    {
        $this->entry = VisitorEntry::where('society_id', auth()->user()->society_id)
            ->with(['flat', 'member', 'deliveryStaff'])
            ->findOrFail($id);
    }

    public function checkIn(): void
    {
        try {
            app(VisitorService::class)->checkIn($this->entry);
            $this->entry->refresh();
            session()->flash('success', 'Checked IN successfully.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function checkOut(): void
    {
        app(VisitorService::class)->checkOut($this->entry);
        $this->entry->refresh();
        session()->flash('success', 'Checked OUT successfully.');
    }

    public function render()
    {
        return $this->guardLayout('Visitor Pass')
            ->with(['qrUrl' => route('visit.qr', $this->entry->qr_token)]);
    }
}
