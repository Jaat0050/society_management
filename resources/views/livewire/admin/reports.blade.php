<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold">Society Reports & Analytics</h3>
            <p class="text-sm text-slate-500">{{ auth()->user()->society?->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <label class="text-sm text-slate-600">Period:</label>
            <select wire:model.live="period" class="form-select w-40">
                <option value="3">Last 3 months</option>
                <option value="6">Last 6 months</option>
                <option value="12">Last 12 months</option>
            </select>
            <a href="{{ route('admin.export.ledger') }}" class="btn-secondary text-sm">Export Ledger CSV</a>
            <a href="{{ route('admin.export.bills') }}" class="btn-secondary text-sm">Export Bills</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="form-card !p-4 text-center"><p class="text-xs text-slate-500">Collected</p><p class="text-xl font-bold text-emerald-600">₹{{ number_format($collected,0) }}</p></div>
        <div class="form-card !p-4 text-center"><p class="text-xs text-slate-500">Pending</p><p class="text-xl font-bold text-amber-600">₹{{ number_format($pending,0) }}</p></div>
        <div class="form-card !p-4 text-center"><p class="text-xs text-slate-500">Overdue</p><p class="text-xl font-bold text-red-600">₹{{ number_format($overdue,0) }}</p></div>
        <div class="form-card !p-4 text-center"><p class="text-xs text-slate-500">Expenses</p><p class="text-xl font-bold">₹{{ number_format($expenses,0) }}</p></div>
        <div class="form-card !p-4 text-center"><p class="text-xs text-slate-500">Net (period)</p><p class="text-xl font-bold {{ ($collected - $expenses) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">₹{{ number_format($collected - $expenses, 0) }}</p></div>
        <div class="form-card !p-4 text-center"><p class="text-xs text-slate-500">Occupancy</p><p class="text-xl font-bold">{{ $occupancy }}%</p><p class="text-xs">{{ $residents }} residents</p></div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="form-card lg:col-span-2">
            <h3 class="font-semibold mb-1">Collections vs Expenses</h3>
            <div class="h-80"><canvas id="arpt-finance" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Complaints Raised</h3>
            <div class="h-64"><canvas id="arpt-complaints-trend" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Visitor Volume</h3>
            <div class="h-64"><canvas id="arpt-visitors-trend" wire:ignore></canvas></div>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-1">Complaints by Status</h3>
            <div class="h-64 flex items-center justify-center"><canvas id="arpt-complaints-pie" wire:ignore></canvas></div>
        </div>
        @if(collect($chartConfigs)->contains(fn($c) => ($c['id'] ?? '') === 'arpt-expense-cat'))
        <div class="form-card">
            <h3 class="font-semibold mb-1">Expense Categories</h3>
            <div class="h-64 flex items-center justify-center"><canvas id="arpt-expense-cat" wire:ignore></canvas></div>
        </div>
        @endif
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="form-card">
            <h3 class="font-semibold mb-4">Maintenance Bills Summary</h3>
            <table class="w-full text-sm">
                <thead><tr class="border-b text-slate-500"><th class="py-2 text-left">Status</th><th class="py-2 text-right">Count</th><th class="py-2 text-right">Amount</th></tr></thead>
                <tbody>
                    @foreach($billBreakdown as $row)
                    <tr class="border-b"><td class="py-2 capitalize">{{ $row->status }}</td><td class="py-2 text-right">{{ $row->cnt }}</td><td class="py-2 text-right font-medium">₹{{ number_format($row->amt, 0) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="form-card">
            <h3 class="font-semibold mb-4">Complaint Status</h3>
            <ul class="space-y-2">
                @foreach($complaints as $status => $count)
                <li class="flex justify-between text-sm py-2 border-b">
                    <span class="capitalize">{{ str_replace('_', ' ', $status) }}</span>
                    <span class="font-bold">{{ $count }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    @push('scripts')
        @include('partials.chart-scripts')
        <script type="application/json" data-chart-config wire:key="admin-reports-{{ $period }}">@json($chartConfigs)</script>
    @endpush
</div>
