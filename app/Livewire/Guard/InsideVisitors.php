<?php

namespace App\Livewire\Guard;

use App\Livewire\Concerns\WithGuardLayout;
use App\Models\VisitorEntry;
use App\Services\VisitorService;
use Livewire\Component;

class InsideVisitors extends Component
{
    use WithGuardLayout;

    public function checkOut(int $id): void
    {
        $entry = VisitorEntry::where('society_id', auth()->user()->society_id)
            ->where('status', 'checked_in')
            ->findOrFail($id);

        app(VisitorService::class)->checkOut($entry);
        session()->flash('success', 'Exit recorded for '.$entry->visitor_name);
    }

    public function render()
    {
        $inside = VisitorEntry::where('society_id', auth()->user()->society_id)
            ->where('status', 'checked_in')
            ->with(['flat', 'deliveryStaff'])
            ->orderBy('entry_at')
            ->get();

        return $this->guardLayout('Visitors Inside')
            ->with(compact('inside'));
    }
}
