<div>
    <div class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 to-cyan-500 p-6 text-white shadow-lg">
        <h3 class="text-2xl font-bold">Society Analytics Dashboard</h3>
        <p class="text-indigo-100 text-sm mt-1">{{ auth()->user()->society?->name }} — real-time overview</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="form-card !p-4 border-l-4 border-l-emerald-500">
            <p class="text-xs text-slate-500">Occupancy</p>
            <p class="text-2xl font-bold">{{ $stats['occupancy'] }}% <span class="text-sm font-normal text-slate-500">/ {{ $stats['flats'] }} flats</span></p>
        </div>
        <div class="form-card !p-4 border-l-4 border-l-indigo-500">
            <p class="text-xs text-slate-500">Residents</p>
            <p class="text-2xl font-bold">{{ $stats['residents'] }}</p>
        </div>
        <div class="form-card !p-4 border-l-4 border-l-amber-500">
            <p class="text-xs text-slate-500">Pending Dues</p>
            <p class="text-2xl font-bold text-amber-600">₹{{ number_format($stats['pending_dues'],0) }}</p>
            <p class="text-xs text-slate-400">{{ $stats['pending_bills'] }} bills</p>
        </div>
        <div class="form-card !p-4 border-l-4 border-l-emerald-600">
            <p class="text-xs text-slate-500">Collected (all time)</p>
            <p class="text-2xl font-bold text-emerald-600">₹{{ number_format($stats['collected'],0) }}</p>
        </div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Open Complaints</p><p class="text-2xl font-bold text-red-600">{{ $stats['open_complaints'] }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Visitors Inside</p><p class="text-2xl font-bold text-indigo-600">{{ $stats['visitors_inside'] }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Pending Approvals</p><p class="text-2xl font-bold">{{ $stats['visitors_pending'] }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Expenses (this month)</p><p class="text-2xl font-bold text-red-600">₹{{ number_format($stats['expenses_month'],0) }}</p></div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="form-card">
            <h3 class="font-semibold mb-1">Maintenance Collection</h3>
            <p class="text-xs text-slate-500 mb-4">Last 6 months</p>
            <div class="h-72"><canvas id="chart-admin-collection" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Daily Visitor Entries</h3>
            <p class="text-xs text-slate-500 mb-4">Last 7 days at gate</p>
            <div class="h-72"><canvas id="chart-admin-visitors" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Bills by Status</h3>
            <div class="h-64 flex items-center justify-center"><canvas id="chart-admin-bills" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Complaints Breakdown</h3>
            <div class="h-64"><canvas id="chart-admin-complaints" wire:ignore></canvas></div>
        </div>
        @if(collect($chartConfigs)->contains(fn($c) => ($c['id'] ?? '') === 'chart-admin-expenses'))
        <div class="form-card lg:col-span-2">
            <h3 class="font-semibold mb-1">Expenses by Category</h3>
            <p class="text-xs text-slate-500 mb-4">Last 3 months</p>
            <div class="h-64 max-w-lg mx-auto"><canvas id="chart-admin-expenses" wire:ignore></canvas></div>
        </div>
        @endif
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="form-card">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold">Recent Complaints</h3>
                <a href="{{ route('admin.reports') }}" wire:navigate class="text-xs text-indigo-600 font-semibold">Full reports →</a>
            </div>
            @foreach($recentComplaints as $c)
                <div class="text-sm py-2 border-b"><p class="font-medium">{{ $c->subject }}</p><p class="text-xs text-slate-500">{{ $c->flat?->label }} · {{ $c->status }}</p></div>
            @endforeach
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-3">Quick Links</h3>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <a href="{{ route('admin.billing') }}" wire:navigate class="rounded-lg bg-slate-50 px-3 py-2 hover:bg-indigo-50">💰 Billing</a>
                <a href="{{ route('admin.visitors') }}" wire:navigate class="rounded-lg bg-slate-50 px-3 py-2 hover:bg-indigo-50">🚪 Visitors</a>
                <a href="{{ route('admin.accounting') }}" wire:navigate class="rounded-lg bg-slate-50 px-3 py-2 hover:bg-indigo-50">📒 Accounting</a>
                <a href="{{ route('admin.complaints') }}" wire:navigate class="rounded-lg bg-slate-50 px-3 py-2 hover:bg-indigo-50">📋 Complaints</a>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('partials.chart-scripts')
        <script type="application/json" data-chart-config>@json($chartConfigs)</script>
    @endpush
</div>
