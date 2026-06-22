<div>
    <select wire:model.live="filter" class="form-select mb-4">
        <option value="all">All</option><option value="pending">Pending</option><option value="checked_in">Inside</option>
        <option value="checked_out">Exited</option><option value="rejected">Rejected</option>
    </select>
    @foreach($entries as $e)
        <div class="rounded-lg bg-slate-800 border border-slate-700 p-3 mb-2 flex justify-between">
            <div><p class="font-medium">{{ $e->visitor_name }}</p><p class="text-xs text-slate-400">{{ $e->pass_code }} · {{ $e->flat?->label }}</p></div>
            <span class="text-xs self-center px-2 py-1 bg-slate-700 rounded">{{ $e->status }}</span>
        </div>
    @endforeach
    <div class="mt-4">{{ $entries->links() }}</div>
</div>
