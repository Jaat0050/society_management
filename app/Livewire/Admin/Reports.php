<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\Flat;
use App\Models\LedgerEntry;
use App\Models\MaintenanceBill;
use App\Models\Member;
use App\Models\VisitorEntry;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Reports extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $period = '6';

    public function render()
    {
        $sid = $this->societyId();
        $months = (int) $this->period;
        $from = now()->subMonths($months - 1)->startOfMonth();

        $collected = MaintenanceBill::where('society_id', $sid)->where('status', 'paid')
            ->sum(DB::raw('COALESCE(paid_amount, amount)'));
        $pending = MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->sum('amount');
        $overdue = MaintenanceBill::where('society_id', $sid)->where('status', 'overdue')->sum('amount');
        $expenses = Expense::where('society_id', $sid)->where('expense_date', '>=', $from)->sum('amount');
        $income = LedgerEntry::where('society_id', $sid)->where('type', 'income')->where('entry_date', '>=', $from)->sum('amount');
        $residents = Member::where('society_id', $sid)->count();
        $totalFlats = Flat::where('society_id', $sid)->count();
        $occupied = Flat::where('society_id', $sid)->where('status', 'occupied')->count();
        $occupancy = $totalFlats ? round(($occupied / $totalFlats) * 100) : 0;

        $periodMonths = collect(range($months - 1, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $labels = $periodMonths->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->all();

        $monthlyPaid = MaintenanceBill::where('society_id', $sid)->where('status', 'paid')
            ->where('paid_at', '>=', $from)
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as period, SUM(COALESCE(paid_amount, amount)) as total')
            ->groupBy('period')->pluck('total', 'period');

        $collectionData = $periodMonths->map(fn ($m) => (float) ($monthlyPaid[$m] ?? 0))->values()->all();

        $expenseMonthly = Expense::where('society_id', $sid)->where('expense_date', '>=', $from)
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as period, SUM(amount) as total')
            ->groupBy('period')->pluck('total', 'period');

        $expenseData = $periodMonths->map(fn ($m) => (float) ($expenseMonthly[$m] ?? 0))->values()->all();

        $complaints = Complaint::where('society_id', $sid)
            ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $complaintMonthly = Complaint::where('society_id', $sid)->where('created_at', '>=', $from)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, count(*) as total')
            ->groupBy('period')->pluck('total', 'period');

        $complaintTrend = $periodMonths->map(fn ($m) => (int) ($complaintMonthly[$m] ?? 0))->all();

        $visitorMonthly = VisitorEntry::where('society_id', $sid)->where('created_at', '>=', $from)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, count(*) as total')
            ->groupBy('period')->pluck('total', 'period');

        $visitorTrend = $periodMonths->map(fn ($m) => (int) ($visitorMonthly[$m] ?? 0))->all();

        $expenseByCategory = Expense::where('society_id', $sid)->where('expense_date', '>=', $from)
            ->selectRaw('category, SUM(amount) as total')->groupBy('category')->orderByDesc('total')->limit(8)
            ->pluck('total', 'category');

        $billBreakdown = MaintenanceBill::where('society_id', $sid)
            ->selectRaw('status, count(*) as cnt, SUM(amount) as amt')
            ->groupBy('status')
            ->get();

        $chartConfigs = [
            [
                'id' => 'arpt-finance',
                'type' => 'bar',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        ['label' => 'Collected', 'data' => $collectionData, 'backgroundColor' => '#10b981'],
                        ['label' => 'Expenses', 'data' => $expenseData, 'backgroundColor' => '#ef4444'],
                    ],
                ],
            ],
            [
                'id' => 'arpt-complaints-trend',
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [['label' => 'Complaints raised', 'data' => $complaintTrend, 'borderColor' => '#6366f1', 'tension' => 0.3]],
                ],
            ],
            [
                'id' => 'arpt-visitors-trend',
                'type' => 'line',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [['label' => 'Visitors', 'data' => $visitorTrend, 'borderColor' => '#06b6d4', 'tension' => 0.3]],
                ],
            ],
            [
                'id' => 'arpt-complaints-pie',
                'type' => 'doughnut',
                'data' => [
                    'labels' => $complaints->keys()->map(fn ($s) => ucfirst(str_replace('_', ' ', $s)))->values()->all(),
                    'datasets' => [['data' => $complaints->values()->all()]],
                ],
            ],
        ];

        if ($expenseByCategory->isNotEmpty()) {
            $chartConfigs[] = [
                'id' => 'arpt-expense-cat',
                'type' => 'polarArea',
                'data' => [
                    'labels' => $expenseByCategory->keys()->all(),
                    'datasets' => [['data' => $expenseByCategory->values()->map(fn ($v) => (float) $v)->all()]],
                ],
            ];
        }

        return $this->societyAdminLayout('Reports & Analytics')
            ->with(compact(
                'collected', 'pending', 'overdue', 'expenses', 'income', 'residents', 'occupancy',
                'complaints', 'billBreakdown', 'chartConfigs'
            ));
    }
}
