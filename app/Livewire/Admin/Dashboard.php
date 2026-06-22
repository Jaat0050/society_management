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

class Dashboard extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public function render()
    {
        $sid = $this->societyId();
        $totalFlats = Flat::where('society_id', $sid)->count();
        $occupied = Flat::where('society_id', $sid)->where('status', 'occupied')->count();

        $stats = [
            'flats' => $totalFlats,
            'occupancy' => $totalFlats ? round(($occupied / $totalFlats) * 100) : 0,
            'residents' => Member::where('society_id', $sid)->count(),
            'pending_bills' => MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->count(),
            'pending_dues' => MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'collected' => MaintenanceBill::where('society_id', $sid)->where('status', 'paid')->sum(DB::raw('COALESCE(paid_amount, amount)')),
            'open_complaints' => Complaint::where('society_id', $sid)->whereIn('status', ['open', 'in_progress'])->count(),
            'visitors_inside' => VisitorEntry::where('society_id', $sid)->where('status', 'checked_in')->count(),
            'visitors_pending' => VisitorEntry::where('society_id', $sid)->where('status', 'pending')->count(),
            'expenses_month' => Expense::where('society_id', $sid)->where('expense_date', '>=', now()->startOfMonth())->sum('amount'),
        ];

        $complaintStats = Complaint::where('society_id', $sid)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $billStatus = MaintenanceBill::where('society_id', $sid)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $monthlyCollection = MaintenanceBill::where('society_id', $sid)
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as period, SUM(COALESCE(paid_amount, amount)) as total')
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $revenueLabels = $months->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->all();
        $revenueData = $months->map(fn ($m) => (float) ($monthlyCollection[$m] ?? 0))->values()->all();

        $visitorWeek = VisitorEntry::where('society_id', $sid)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $dayLabels = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('d M'))->all();
        $dayKeys = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'))->all();
        $visitorData = collect($dayKeys)->map(fn ($d) => (int) ($visitorWeek[$d] ?? 0))->all();

        $expenseByCategory = Expense::where('society_id', $sid)
            ->where('expense_date', '>=', now()->subMonths(3))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'category');

        $recentComplaints = Complaint::where('society_id', $sid)->with(['flat', 'assignedStaff'])->latest()->take(5)->get();

        $chartConfigs = [
            [
                'id' => 'chart-admin-collection',
                'type' => 'line',
                'data' => [
                    'labels' => $revenueLabels,
                    'datasets' => [[
                        'label' => 'Maintenance collected (₹)',
                        'data' => $revenueData,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16,185,129,0.12)',
                        'fill' => true,
                        'tension' => 0.35,
                    ]],
                ],
            ],
            [
                'id' => 'chart-admin-bills',
                'type' => 'doughnut',
                'data' => [
                    'labels' => $billStatus->keys()->map(fn ($s) => ucfirst($s))->values()->all(),
                    'datasets' => [['data' => $billStatus->values()->all(), 'backgroundColor' => ['#f59e0b', '#10b981', '#ef4444']]],
                ],
            ],
            [
                'id' => 'chart-admin-complaints',
                'type' => 'bar',
                'data' => [
                    'labels' => $complaintStats->keys()->map(fn ($s) => ucfirst(str_replace('_', ' ', $s)))->values()->all(),
                    'datasets' => [['label' => 'Complaints', 'data' => $complaintStats->values()->all(), 'backgroundColor' => '#6366f1']],
                ],
                'options' => ['plugins' => ['legend' => ['display' => false]]],
            ],
            [
                'id' => 'chart-admin-visitors',
                'type' => 'bar',
                'data' => [
                    'labels' => $dayLabels,
                    'datasets' => [['label' => 'Gate entries', 'data' => $visitorData, 'backgroundColor' => '#06b6d4']],
                ],
                'options' => ['plugins' => ['legend' => ['display' => false]]],
            ],
        ];

        if ($expenseByCategory->isNotEmpty()) {
            $chartConfigs[] = [
                'id' => 'chart-admin-expenses',
                'type' => 'polarArea',
                'data' => [
                    'labels' => $expenseByCategory->keys()->all(),
                    'datasets' => [['data' => $expenseByCategory->values()->map(fn ($v) => (float) $v)->all()]],
                ],
            ];
        }

        return $this->societyAdminLayout('Society Dashboard')
            ->with(compact('stats', 'recentComplaints', 'complaintStats', 'chartConfigs'));
    }
}
