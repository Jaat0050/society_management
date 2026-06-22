<?php

namespace App\Livewire\Guard;

use App\Livewire\Concerns\WithGuardLayout;
use App\Models\VisitorEntry;
use Livewire\Component;
use Livewire\WithPagination;

class EntryLog extends Component
{
    use WithGuardLayout, WithPagination;

    public string $filter = 'all';

    public function render()
    {
        $query = VisitorEntry::where('society_id', auth()->user()->society_id)
            ->with(['flat', 'deliveryStaff']);

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return $this->guardLayout('Gate Entry Log')
            ->with(['entries' => $query->latest()->paginate(20)]);
    }
}
