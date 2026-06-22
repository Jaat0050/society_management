<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Platform Reports & Analytics</h3>
            <p class="text-sm text-slate-500">Cross-society financial & operational insights</p>
        </div>
        <div class="flex items-center gap-3">
            <label class="text-sm text-slate-600">Period:</label>
            <select wire:model.live="period" class="form-select w-40">
                <option value="3">Last 3 months</option>
                <option value="6">Last 6 months</option>
                <option value="12">Last 12 months</option>
            </select>
            <a href="{{ route('super-admin.preview') }}" wire:navigate class="btn-secondary text-sm">Module Tracker</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="form-card !p-5 border-t-4 border-t-emerald-500">
            <p class="text-xs uppercase text-slate-500 font-semibold">Total Collected</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">₹{{ number_format($collected, 0) }}</p>
        </div>
        <div class="form-card !p-5 border-t-4 border-t-amber-500">
            <p class="text-xs uppercase text-slate-500 font-semibold">Pending Dues</p>
            <p class="text-3xl font-black text-amber-600 mt-1">₹{{ number_format($pending, 0) }}</p>
        </div>
        <div class="form-card !p-5 border-t-4 border-t-red-500">
            <p class="text-xs uppercase text-slate-500 font-semibold">Overdue</p>
            <p class="text-3xl font-black text-red-600 mt-1">₹{{ number_format($overdue, 0) }}</p>
        </div>
        <div class="form-card !p-5 border-t-4 border-t-slate-500">
            <p class="text-xs uppercase text-slate-500 font-semibold">Expenses (period)</p>
            <p class="text-3xl font-black mt-1">₹{{ number_format($expenses, 0) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="form-card lg:col-span-2">
            <h3 class="font-semibold mb-1">Collections vs Expenses</h3>
            <p class="text-xs text-slate-500 mb-4">Monthly comparison across platform</p>
            <div class="h-80"><canvas id="rpt-collection-expense" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Top Societies by Revenue</h3>
            <div class="h-80"><canvas id="rpt-top-societies" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Subscription Status</h3>
            <div class="h-72 flex items-center justify-center"><canvas id="rpt-society-sub" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Complaints Trend</h3>
            <div class="h-64"><canvas id="rpt-complaints-trend" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Visitor Entries Trend</h3>
            <div class="h-64"><canvas id="rpt-visitors-trend" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Users by Role</h3>
            <div class="h-64 flex items-center justify-center"><canvas id="rpt-users-role" wire:ignore></canvas></div>
        </div>
    </div>

    <div class="form-card">
        <h3 class="font-semibold mb-4">Society Revenue Leaderboard</h3>
        <table class="w-full text-sm">
            <thead><tr class="border-b text-left text-slate-500"><th class="py-2">#</th><th class="py-2">Society</th><th class="py-2 text-right">Collected</th></tr></thead>
            <tbody>
                @foreach($topSocieties as $i => $s)
                <tr class="border-b border-slate-100">
                    <td class="py-3 text-slate-400">{{ $i + 1 }}</td>
                    <td class="py-3 font-medium">{{ $s['name'] }}</td>
                    <td class="py-3 text-right font-semibold text-emerald-600">₹{{ number_format($s['total'], 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('scripts')
        @include('partials.chart-scripts')
        <script type="application/json" data-chart-config wire:key="sa-reports-{{ $period }}">@json($chartConfigs)</script>
    @endpush
</div>
