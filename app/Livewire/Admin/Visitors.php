<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\VisitorEntry;
use App\Services\VisitorService;
use Livewire\Component;
use Livewire\WithPagination;

class Visitors extends Component
{
    use BelongsToSociety, WithPanelLayout, WithPagination;

    public string $filter = 'all';

    public function approve(int $id): void
    {
        $entry = VisitorEntry::where('society_id', $this->societyId())->findOrFail($id);
        app(VisitorService::class)->approve($entry, auth()->id());
        session()->flash('success', 'Visitor approved.');
    }

    public function reject(int $id): void
    {
        $entry = VisitorEntry::where('society_id', $this->societyId())->findOrFail($id);
        app(VisitorService::class)->reject($entry, 'Rejected by admin', auth()->id());
        session()->flash('success', 'Visitor rejected.');
    }

    public function checkOut(int $id): void
    {
        $entry = VisitorEntry::where('society_id', $this->societyId())->findOrFail($id);
        app(VisitorService::class)->checkOut($entry);
        session()->flash('success', 'Exit recorded.');
    }

    public function render()
    {
        $query = VisitorEntry::where('society_id', $this->societyId())
            ->with(['flat', 'deliveryStaff', 'creator']);

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        $stats = [
            'pending' => VisitorEntry::where('society_id', $this->societyId())->where('status', 'pending')->count(),
            'inside' => VisitorEntry::where('society_id', $this->societyId())->where('status', 'checked_in')->count(),
            'today' => VisitorEntry::where('society_id', $this->societyId())->whereDate('created_at', today())->count(),
        ];

        return $this->societyAdminLayout('Visitor Management')
            ->with([
                'entries' => $query->latest()->paginate(15),
                'stats' => $stats,
            ]);
    }
}
