<?php

namespace App\Livewire\Resident;

use App\Livewire\Concerns\WithResidentLayout;
use App\Models\Complaint;
use App\Models\EmergencyAlert;
use App\Models\MaintenanceBill;
use App\Models\Notice;
use App\Models\VisitorEntry;
use Livewire\Component;

class Dashboard extends Component
{
    use WithResidentLayout;

    public function render()
    {
        $member = auth()->user()->member;
        $flatId = $member?->flat_id;

        return $this->residentLayout('My Home')
            ->with([
                'member' => $member,
                'pendingBills' => $flatId
                    ? MaintenanceBill::where('flat_id', $flatId)->whereIn('status', ['pending', 'overdue'])->count()
                    : 0,
                'openComplaints' => Complaint::where('flat_id', $flatId)->whereIn('status', ['open', 'in_progress'])->count(),
                'notices' => Notice::where('society_id', auth()->user()->society_id)->whereNotNull('published_at')->latest('published_at')->take(3)->get(),
                'alerts' => EmergencyAlert::where('society_id', auth()->user()->society_id)->where('is_active', true)->latest()->take(2)->get(),
            ]);
    }
}
