<div>
    @include('livewire.partials.alerts')
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Pending</p><p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Inside</p><p class="text-2xl font-bold text-emerald-600">{{ $stats['inside'] }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Today</p><p class="text-2xl font-bold">{{ $stats['today'] }}</p></div>
    </div>
    <select wire:model.live="filter" class="form-select mb-4 max-w-xs">
        <option value="all">All statuses</option>
        <option value="pending">Pending</option><option value="approved">Approved</option>
        <option value="checked_in">Checked In</option><option value="checked_out">Checked Out</option>
        <option value="rejected">Rejected</option>
    </select>
    <div class="form-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b text-left text-slate-500"><th class="py-2">Visitor</th><th>Flat</th><th>Type</th><th>Pass</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($entries as $e)
                <tr class="border-b border-slate-100">
                    <td class="py-3 font-medium">{{ $e->visitor_name }}<br><span class="text-xs text-slate-400">{{ $e->phone }}</span></td>
                    <td>{{ $e->flat?->label ?? '—' }}</td>
                    <td>{{ $e->visit_type }}</td>
                    <td class="font-mono text-xs">{{ $e->pass_code }}</td>
                    <td><span class="px-2 py-0.5 rounded text-xs bg-slate-100">{{ $e->status }}</span></td>
                    <td class="text-right space-x-1">
                        @if($e->status === 'pending')
                            <button type="button" wire:click="approve({{ $e->id }})" class="text-emerald-600 text-xs font-semibold">Approve</button>
                            <button type="button" wire:click="reject({{ $e->id }})" class="text-red-600 text-xs font-semibold">Reject</button>
                        @endif
                        @if($e->status === 'checked_in')
                            <button type="button" wire:click="checkOut({{ $e->id }})" class="text-amber-600 text-xs font-semibold">Exit</button>
                        @endif
                        <a href="{{ route('visit.qr', $e->qr_token) }}" target="_blank" class="text-indigo-600 text-xs">QR</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $entries->links() }}</div>
    </div>
</div>
