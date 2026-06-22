<div class="space-y-3">
    @forelse($inside as $v)
        <div class="rounded-xl bg-slate-800 border border-emerald-700/50 p-4 flex justify-between items-center">
            <div>
                <p class="font-bold">{{ $v->visitor_name }}</p>
                <p class="text-xs text-slate-400">{{ $v->flat?->label }} · In since {{ $v->entry_at?->format('h:i A') }}</p>
            </div>
            <button type="button" wire:click="checkOut({{ $v->id }})" class="btn-secondary !bg-red-600/80 !text-white shrink-0">Exit</button>
        </div>
    @empty
        <p class="text-center text-slate-500 py-10">No visitors inside currently.</p>
    @endforelse
</div>
