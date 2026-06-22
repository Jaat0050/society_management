<div>
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="rounded-xl bg-amber-900/40 border border-amber-600/50 p-4"><p class="text-xs text-amber-300">Pending</p><p class="text-3xl font-bold">{{ $stats['pending'] }}</p></div>
        <div class="rounded-xl bg-emerald-900/40 border border-emerald-600/50 p-4"><p class="text-xs text-emerald-300">Inside Now</p><p class="text-3xl font-bold">{{ $stats['inside'] }}</p></div>
        <div class="rounded-xl bg-slate-800 border border-slate-700 p-4"><p class="text-xs text-slate-400">Entries Today</p><p class="text-2xl font-bold">{{ $stats['today_in'] }}</p></div>
        <div class="rounded-xl bg-slate-800 border border-slate-700 p-4"><p class="text-xs text-slate-400">Exits Today</p><p class="text-2xl font-bold">{{ $stats['today_out'] }}</p></div>
    </div>
    <div class="rounded-xl bg-slate-800 border border-slate-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-700 font-semibold">Recent Activity</div>
        @forelse($recent as $v)
            <div class="px-4 py-3 border-t border-slate-700 flex justify-between items-center">
                <div>
                    <p class="font-medium">{{ $v->visitor_name }}</p>
                    <p class="text-xs text-slate-400">{{ $v->flat?->label ?? '—' }} · {{ $v->visit_type }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-slate-700">{{ $v->statusLabel() }}</span>
            </div>
        @empty
            <p class="p-6 text-center text-slate-500">No entries yet</p>
        @endforelse
    </div>
</div>
