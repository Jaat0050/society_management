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

class Dashboard extends Component
{
    use WithPanelLayout;

    public function render()
    {
        $stats = [
            'societies' => Society::count(),
            'active_societies' => Society::where('is_active', true)->whereNull('blocked_at')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'members' => \App\Models\Member::count(),
            'flats' => \App\Models\Flat::count(),
            'staff' => \App\Models\StaffMember::count(),
            'pending_bills' => MaintenanceBill::whereIn('status', ['pending', 'overdue'])->count(),
            'open_complaints' => Complaint::whereIn('status', ['open', 'in_progress'])->count(),
            'collected' => MaintenanceBill::where('status', 'paid')->sum(DB::raw('COALESCE(paid_amount, amount)')),
            'pending_amount' => MaintenanceBill::whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'visitors_today' => VisitorEntry::whereDate('created_at', today())->count(),
            'visitors_inside' => VisitorEntry::where('status', 'checked_in')->count(),
        ];

        $recentSocieties = Society::latest()->take(5)->get();

        $monthlyRevenue = MaintenanceBill::where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as period, SUM(COALESCE(paid_amount, amount)) as total')
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $revenueLabels = $months->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->all();
        $revenueData = $months->map(fn ($m) => (float) ($monthlyRevenue[$m] ?? 0))->values()->all();

        $byStatus = Society::selectRaw('subscription_status, count(*) as total')
            ->groupBy('subscription_status')
            ->pluck('total', 'subscription_status');

        $usersByRole = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $complaintByStatus = Complaint::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $visitorWeek = VisitorEntry::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $dayLabels = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('d M'))->all();
        $dayKeys = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'))->all();
        $visitorData = collect($dayKeys)->map(fn ($d) => (int) ($visitorWeek[$d] ?? 0))->all();

        $chartConfigs = [
            [
                'id' => 'chart-revenue-trend',
                'type' => 'line',
                'data' => [
                    'labels' => $revenueLabels,
                    'datasets' => [[
                        'label' => 'Collections (₹)',
                        'data' => $revenueData,
                        'borderColor' => '#4f46e5',
                        'backgroundColor' => 'rgba(79,70,229,0.1)',
                        'fill' => true,
                        'tension' => 0.35,
                    ]],
                ],
            ],
            [
                'id' => 'chart-society-status',
                'type' => 'doughnut',
                'data' => [
                    'labels' => $byStatus->keys()->map(fn ($s) => ucfirst($s))->values()->all(),
                    'datasets' => [['data' => $byStatus->values()->all(), 'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444', '#6366f1']]],
                ],
            ],
            [
                'id' => 'chart-users-role',
                'type' => 'pie',
                'data' => [
                    'labels' => $usersByRole->keys()->map(fn ($r) => str_replace('_', ' ', ucfirst($r)))->values()->all(),
                    'datasets' => [['data' => $usersByRole->values()->all()]],
                ],
            ],
            [
                'id' => 'chart-complaints',
                'type' => 'bar',
                'data' => [
                    'labels' => $complaintByStatus->keys()->map(fn ($s) => ucfirst(str_replace('_', ' ', $s)))->values()->all(),
                    'datasets' => [['label' => 'Complaints', 'data' => $complaintByStatus->values()->all(), 'backgroundColor' => '#f59e0b']],
                ],
                'options' => ['plugins' => ['legend' => ['display' => false]]],
            ],
            [
                'id' => 'chart-visitors-week',
                'type' => 'bar',
                'data' => [
                    'labels' => $dayLabels,
                    'datasets' => [['label' => 'Visitor entries', 'data' => $visitorData, 'backgroundColor' => '#06b6d4']],
                ],
                'options' => ['plugins' => ['legend' => ['display' => false]]],
            ],
        ];

        return $this->superAdminLayout('Global Dashboard')
            ->with(compact('stats', 'recentSocieties', 'chartConfigs'));
    }
}
