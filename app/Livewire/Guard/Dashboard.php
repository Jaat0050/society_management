<?php

namespace App\Livewire\Guard;

use App\Livewire\Concerns\WithGuardLayout;
use App\Models\VisitorEntry;
use Livewire\Component;

class Dashboard extends Component
{
    use WithGuardLayout;

    public function render()
    {
        $sid = auth()->user()->society_id;
        $today = now()->toDateString();

        $stats = [
            'pending' => VisitorEntry::where('society_id', $sid)->where('status', 'pending')->count(),
            'inside' => VisitorEntry::where('society_id', $sid)->where('status', 'checked_in')->count(),
            'today_in' => VisitorEntry::where('society_id', $sid)->whereDate('entry_at', $today)->count(),
            'today_out' => VisitorEntry::where('society_id', $sid)->whereDate('exit_at', $today)->count(),
        ];

        $recent = VisitorEntry::where('society_id', $sid)
            ->with(['flat', 'deliveryStaff'])
            ->latest()
            ->take(8)
            ->get();

        return $this->guardLayout('Gate Dashboard')
            ->with(compact('stats', 'recent'));
    }
}
