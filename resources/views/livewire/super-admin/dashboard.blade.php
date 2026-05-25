<div>
    <div class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-500 p-8 text-white shadow-xl">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-100">Super Admin</p>
        <h3 class="mt-2 text-3xl font-bold">Platform Analytics Dashboard</h3>
        <p class="mt-2 text-indigo-50 max-w-2xl">Live metrics across all societies — collections, visitors, complaints & subscriptions.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
        @foreach ([
            ['Total Societies', $stats['societies'], '🏢', ''],
            ['Active', $stats['active_societies'], '✅', 'text-emerald-600'],
            ['Collected', '₹'.number_format($stats['collected']/1000, 0).'K', '💰', 'text-emerald-600'],
            ['Pending Dues', '₹'.number_format($stats['pending_amount']/1000, 0).'K', '⏳', 'text-amber-600'],
            ['Visitors Today', $stats['visitors_today'], '🚪', 'text-indigo-600'],
            ['Inside Gate', $stats['visitors_inside'], '🚶', 'text-cyan-600'],
            ['Open Complaints', $stats['open_complaints'], '📋', 'text-red-600'],
            ['Members', $stats['members'], '👥', ''],
            ['Flats', $stats['flats'], '🏠', ''],
        ] as [$label, $value, $icon, $color])
        <div class="rounded-2xl border border-white bg-white p-4 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <p class="text-xs font-semibold uppercase text-slate-500">{{ $label }}</p>
                <span class="text-lg">{{ $icon }}</span>
            </div>
            <p class="mt-2 text-2xl font-black {{ $color ?: 'text-slate-900' }}">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="form-card">
            <h3 class="font-semibold text-slate-800 mb-1">Maintenance Collections Trend</h3>
            <p class="text-xs text-slate-500 mb-4">Last 6 months — all societies</p>
            <div class="h-72"><canvas id="chart-revenue-trend" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold text-slate-800 mb-1">Visitor Gate Activity</h3>
            <p class="text-xs text-slate-500 mb-4">Entries per day (last 7 days)</p>
            <div class="h-72"><canvas id="chart-visitors-week" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold text-slate-800 mb-1">Societies by Subscription</h3>
            <div class="h-64 flex items-center justify-center"><canvas id="chart-society-status" wire:ignore class="max-h-64"></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold text-slate-800 mb-1">Platform Users by Role</h3>
            <div class="h-64 flex items-center justify-center"><canvas id="chart-users-role" wire:ignore class="max-h-64"></canvas></div>
        </div>
        <div class="form-card lg:col-span-2">
            <h3 class="font-semibold text-slate-800 mb-1">Complaints by Status</h3>
            <div class="h-56"><canvas id="chart-complaints" wire:ignore></canvas></div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b px-6 py-5">
            <div><h3 class="font-bold">Recent Societies</h3><p class="text-sm text-slate-500">Latest onboarded</p></div>
            <div class="flex gap-2">
                <a href="{{ route('super-admin.preview') }}" wire:navigate class="rounded-full bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700">Module Tracker</a>
                <a href="{{ route('super-admin.reports') }}" wire:navigate class="rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-600">Full Reports</a>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>
                <th class="text-left px-5 py-3">Name</th><th class="px-5 py-3 text-left">City</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Plan</th>
            </tr></thead>
            <tbody>
                @forelse ($recentSocieties as $society)
                <tr class="border-t hover:bg-slate-50">
                    <td class="px-5 py-4 font-semibold">{{ $society->name }}</td>
                    <td class="px-5 py-4">{{ $society->city }}</td>
                    <td class="px-5 py-4">@if($society->isBlocked())<span class="text-red-600 text-xs font-semibold">Blocked</span>@else<span class="text-emerald-600 text-xs font-semibold">Active</span>@endif</td>
                    <td class="px-5 py-4 capitalize">{{ $society->subscription_status }}</td>
                </tr>
                @empty<tr><td colspan="4" class="py-8 text-center text-slate-500">No societies</td></tr>@endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
        @include('partials.chart-scripts')
        <script type="application/json" data-chart-config>@json($chartConfigs)</script>
    @endpush
</div>
