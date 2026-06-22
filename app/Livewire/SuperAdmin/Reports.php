<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\MaintenanceBill;
use App\Models\Society;
use App\Models\User;
use App\Models\VisitorEntry;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Reports extends Component
{
    use WithPanelLayout;

    public string $period = '6';

    public function render()
    {
        $months = (int) $this->period;
        $from = now()->subMonths($months - 1)->startOfMonth();

        $collected = MaintenanceBill::where('status', 'paid')->sum(DB::raw('COALESCE(paid_amount, amount)'));
        $pending = MaintenanceBill::whereIn('status', ['pending', 'overdue'])->sum('amount');
        $overdue = MaintenanceBill::where('status', 'overdue')->sum('amount');
        $expenses = Expense::where('expense_date', '>=', $from)->sum('amount');

        $monthly = MaintenanceBill::where('status', 'paid')
            ->where('paid_at', '>=', $from)
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as period, SUM(COALESCE(paid_amount, amount)) as total')
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $periodMonths = collect(range($months - 1, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $collectionLabels = $periodMonths->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->all();
        $collectionData = $periodMonths->map(fn ($m) => (float) ($monthly[$m] ?? 0))->values()->all();

        $expenseMonthly = Expense::where('expense_date', '>=', $from)
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as period, SUM(amount) as total')
            ->groupBy('period')
            ->pluck('total', 'period');

        $expenseData = $periodMonths->map(fn ($m) => (float) ($expenseMonthly[$m] ?? 0))->values()->all();

        $byStatus = Society::selectRaw('subscription_status, count(*) as total')
            ->groupBy('subscription_status')
            ->pluck('total', 'subscription_status');

        $usersByRole = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $topSocieties = MaintenanceBill::where('status', 'paid')
            ->selectRaw('society_id, SUM(COALESCE(paid_amount, amount)) as total')
            ->groupBy('society_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'name' => Society::find($row->society_id)?->name ?? 'Unknown',
                'total' => (float) $row->total,
            ]);

        $complaintTrend = Complaint::where('created_at', '>=', $from)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, count(*) as total')
            ->groupBy('period')
            ->pluck('total', 'period');

        $complaintData = $periodMonths->map(fn ($m) => (int) ($complaintTrend[$m] ?? 0))->all();

        $visitorTrend = VisitorEntry::where('created_at', '>=', $from)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, count(*) as total')
            ->groupBy('period')
            ->pluck('total', 'period');

        $visitorData = $periodMonths->map(fn ($m) => (int) ($visitorTrend[$m] ?? 0))->all();

        $chartConfigs = [
            [
                'id' => 'rpt-collection-expense',
                'type' => 'bar',
                'data' => [
                    'labels' => $collectionLabels,
                    'datasets' => [
                        ['label' => 'Collected', 'data' => $collectionData, 'backgroundColor' => '#10b981'],
                        ['label' => 'Expenses', 'data' => $expenseData, 'backgroundColor' => '#ef4444'],
                    ],
                ],
            ],
            [
                'id' => 'rpt-top-societies',
                'type' => 'bar',
                'data' => [
                    'labels' => $topSocieties->pluck('name')->all(),
                    'datasets' => [['label' => 'Revenue (₹)', 'data' => $topSocieties->pluck('total')->all(), 'backgroundColor' => '#4f46e5']],
                ],
                'options' => ['indexAxis' => 'y', 'plugins' => ['legend' => ['display' => false]]],
            ],
            [
                'id' => 'rpt-society-sub',
                'type' => 'doughnut',
                'data' => [
                    'labels' => $byStatus->keys()->map(fn ($s) => ucfirst($s))->values()->all(),
                    'datasets' => [['data' => $byStatus->values()->all()]],
                ],
            ],
            [
                'id' => 'rpt-complaints-trend',
                'type' => 'line',
                'data' => [
                    'labels' => $collectionLabels,
                    'datasets' => [[
                        'label' => 'New complaints',
                        'data' => $complaintData,
                        'borderColor' => '#f59e0b',
                        'tension' => 0.3,
                    ]],
                ],
            ],
            [
                'id' => 'rpt-visitors-trend',
                'type' => 'line',
                'data' => [
                    'labels' => $collectionLabels,
                    'datasets' => [[
                        'label' => 'Visitor entries',
                        'data' => $visitorData,
                        'borderColor' => '#06b6d4',
                        'tension' => 0.3,
                    ]],
                ],
            ],
            [
                'id' => 'rpt-users-role',
                'type' => 'pie',
                'data' => [
                    'labels' => $usersByRole->keys()->map(fn ($r) => str_replace('_', ' ', ucfirst($r)))->values()->all(),
                    'datasets' => [['data' => $usersByRole->values()->all()]],
                ],
            ],
        ];

        return $this->superAdminLayout('Reports & Analytics')
            ->with(compact(
                'collected', 'pending', 'overdue', 'expenses',
                'byStatus', 'usersByRole', 'topSocieties', 'chartConfigs'
            ));
    }
}
