<div>
    @include('livewire.partials.alerts')
    <div class="flex flex-wrap gap-2 mb-4">
        <button wire:click="applyPenalties" class="btn-secondary text-sm">Apply Late Fees</button>
        <button wire:click="sendReminders" class="btn-secondary text-sm">Send Due Reminders</button>
        <select wire:model.live="filter" class="form-select max-w-xs"><option value="pending">Pending</option><option value="overdue">Overdue</option><option value="paid">Paid</option><option value="all">All</option></select>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Collected</p><p class="text-xl font-bold text-emerald-600">₹{{ number_format($totals['paid'],0) }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Outstanding</p><p class="text-xl font-bold text-amber-600">₹{{ number_format($totals['pending'],0) }}</p></div>
    </div>
    <div class="form-card overflow-x-auto">
        <table class="w-full text-sm"><thead><tr class="border-b text-slate-500"><th class="py-2 text-left">Flat</th><th>Month</th><th>Amount</th><th>Penalty</th><th>Status</th><th></th></tr></thead>
        <tbody>@foreach($bills as $b)<tr class="border-b"><td class="py-3">{{ $b->flat?->label }}</td><td>{{ $b->bill_month }}</td><td>₹{{ number_format($b->amount,0) }}</td><td>₹{{ number_format($b->penalty_amount,0) }}</td><td>{{ $b->status }}</td>
            <td class="space-x-1">@if($b->status!=='paid')<button wire:click="markPaid({{ $b->id }})" class="text-emerald-600 text-xs">Mark Paid</button><button wire:click="initiateOnline({{ $b->id }})" class="text-indigo-600 text-xs">Pay Online</button>@endif</td></tr>@endforeach</tbody></table>
        {{ $bills->links() }}
    </div>
</div>
