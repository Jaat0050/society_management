<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Expense;
use App\Models\LedgerEntry;
use App\Models\MaintenanceBill;
use Livewire\Component;

class Accounting extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $period = '';

    public function mount(): void
    {
        $this->period = now()->format('Y-m');
    }

    public function render()
    {
        $sid = $this->societyId();

        $income = LedgerEntry::where('society_id', $sid)->where('type', 'income')
            ->when($this->period, fn ($q) => $q->where('entry_date', 'like', $this->period.'%'))
            ->sum('amount');

        $expense = LedgerEntry::where('society_id', $sid)->where('type', 'expense')
            ->when($this->period, fn ($q) => $q->where('entry_date', 'like', $this->period.'%'))
            ->sum('amount');

        $pending = MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->sum('amount');
        $paid = MaintenanceBill::where('society_id', $sid)->where('status', 'paid')
            ->when($this->period, fn ($q) => $q->where('bill_month', $this->period))
            ->sum('paid_amount');

        return $this->societyAdminLayout('Accounting & Reports')
            ->with([
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
                'pending' => $pending,
                'paid' => $paid,
                'ledger' => LedgerEntry::where('society_id', $sid)
                    ->when($this->period, fn ($q) => $q->where('entry_date', 'like', $this->period.'%'))
                    ->orderByDesc('entry_date')->take(30)->get(),
                'byCategory' => LedgerEntry::where('society_id', $sid)
                    ->when($this->period, fn ($q) => $q->where('entry_date', 'like', $this->period.'%'))
                    ->selectRaw('type, category, SUM(amount) as total')
                    ->groupBy('type', 'category')->get(),
            ]);
    }
}
