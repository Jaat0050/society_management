<div>
    <div class="mb-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-3">
        <div class="form-field">
            <label class="form-label">Filter by Society</label>
            <select wire:model.live="societyId" class="form-select">
                <option value="">All Societies — platform-wide</option>
                @foreach ($societies as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->city }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label class="form-label">Search in current tab</label>
            <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search..." @disabled($tab === 'overview')>
        </div>
        <div class="flex items-end">
            @if ($selectedSociety)
                <div class="rounded-xl bg-indigo-50 px-4 py-3 text-sm w-full">
                    <p class="font-semibold text-indigo-900">{{ $selectedSociety->name }}</p>
                    <p class="text-indigo-600 mt-1">{{ $selectedSociety->city }} · {{ $selectedSociety->subscription_status }}
                        @if($selectedSociety->blocked_at)<span class="text-red-600 font-semibold"> · Blocked</span>@endif
                    </p>
                </div>
            @else
                <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 w-full">
                    Select a <strong>society</strong> for overview dashboard & full module tracking.
                </div>
            @endif
        </div>
    </div>

    {{-- Quick stats strip --}}
    <div class="mb-4 overflow-x-auto pb-2">
        <div class="flex gap-2 min-w-max">
            @foreach ($tabGroups as $groupName => $tabs)
                @foreach ($tabs as [$key, $label, $icon])
                    @if($key !== 'overview' && isset($counts[$key]))
                    <button type="button" wire:click="setTab('{{ $key }}')"
                        class="shrink-0 rounded-lg border px-3 py-2 text-left text-xs transition {{ $tab === $key ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 bg-white hover:border-indigo-200' }}">
                        <span>{{ $icon }}</span>
                        <span class="block text-slate-500 mt-0.5">{{ $label }}</span>
                        <span class="block font-bold text-slate-900">{{ $counts[$key] }}</span>
                    </button>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Grouped module tabs --}}
    <div class="mb-6 space-y-3">
        @foreach ($tabGroups as $groupName => $tabs)
            @if(count($tabs))
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">{{ $groupName }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tabs as [$key, $label, $icon])
                        <button type="button" wire:click="setTab('{{ $key }}')"
                            class="rounded-full px-3 py-1.5 text-xs font-medium transition {{ $tab === $key ? 'bg-indigo-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                            {{ $icon }} {{ $label }}
                            @if($key !== 'overview' && isset($counts[$key]))<span class="ml-1 opacity-75">({{ $counts[$key] }})</span>@endif
                        </button>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>

    @if ($tab === 'overview')
        @if($societyId || !empty($overview))
            @if(!$societyId)<p class="text-sm text-slate-600 mb-4">Aggregated across <strong>all societies</strong> on the platform.</p>@endif
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Occupancy</p><p class="text-2xl font-bold text-emerald-600">{{ $overview['occupancy'] }}%</p></div>
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Pending Dues</p><p class="text-xl font-bold text-amber-600">₹{{ number_format($overview['pending_dues'],0) }}</p><p class="text-xs">{{ $overview['pending_bills'] }} bills</p></div>
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Collected</p><p class="text-xl font-bold text-emerald-600">₹{{ number_format($overview['collected'],0) }}</p></div>
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Expenses</p><p class="text-xl font-bold text-red-600">₹{{ number_format($overview['expenses_total'],0) }}</p></div>
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Open Complaints</p><p class="text-2xl font-bold">{{ $overview['open_complaints'] }}</p></div>
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Visitors Inside</p><p class="text-2xl font-bold text-indigo-600">{{ $overview['visitors_inside'] }}</p></div>
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Pending Approvals</p><p class="text-2xl font-bold">{{ $overview['visitors_pending'] }}</p></div>
                <div class="form-card !p-4 border-red-200"><p class="text-xs text-red-600">Active SOS</p><p class="text-2xl font-bold text-red-600">{{ $overview['active_sos'] }}</p></div>
                <div class="form-card !p-4"><p class="text-xs text-slate-500">Emergency Alerts</p><p class="text-2xl font-bold">{{ $overview['active_emergency'] }}</p></div>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-card">
                    <h3 class="font-semibold mb-3">Recent Visitors</h3>
                    @forelse($overview['recent_visitors'] as $v)
                        <div class="flex justify-between py-2 border-b text-sm"><span>{{ $v->visitor_name }}</span><span class="text-slate-500">{{ $v->status }} · {{ $v->flat?->label }}</span></div>
                    @empty<p class="text-slate-500 text-sm">No visitor entries.</p>@endforelse
                    <button type="button" wire:click="setTab('visitors')" class="text-indigo-600 text-xs mt-3">View all →</button>
                </div>
                <div class="form-card">
                    <h3 class="font-semibold mb-3">Recent Complaints</h3>
                    @forelse($overview['recent_complaints'] as $c)
                        <div class="flex justify-between py-2 border-b text-sm"><span>{{ $c->subject }}</span><span class="text-slate-500">{{ $c->status }}</span></div>
                    @empty<p class="text-slate-500 text-sm">No complaints.</p>@endforelse
                    <button type="button" wire:click="setTab('complaints')" class="text-indigo-600 text-xs mt-3">View all →</button>
                </div>
            </div>
            <div class="mt-6 form-card">
                <h3 class="font-semibold mb-3">All modules at a glance</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-2 text-sm">
                    @foreach($counts as $key => $cnt)
                        @if($key !== 'overview' && $cnt > 0)
                        <button type="button" wire:click="setTab('{{ $key }}')" class="text-left rounded-lg border border-slate-100 px-3 py-2 hover:bg-indigo-50 hover:border-indigo-200">
                            <span class="font-bold text-indigo-600">{{ $cnt }}</span>
                            <span class="block text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                        </button>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
                <p class="text-lg font-semibold text-slate-700">Loading overview…</p>
            </div>
        @endif
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 capitalize">{{ str_replace('_', ' ', $tab) }}</h3>
                    <p class="text-sm text-slate-500">Read-only · Super Admin preview</p>
                </div>
                <span class="text-sm font-medium text-indigo-600">{{ $counts[$tab] ?? 0 }} records</span>
            </div>
            <div class="overflow-x-auto">
                @include('livewire.super-admin.partials.preview-tables')
            </div>
            <div class="border-t border-slate-100 px-6 py-4">{{ $data->links() }}</div>
        </div>
    @endif
</div>
