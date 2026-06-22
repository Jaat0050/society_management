<div>
    @include('livewire.partials.alerts')
    <div class="form-card !p-4 mb-4"><p class="text-xs text-slate-500">Total Expenses</p><p class="text-2xl font-bold text-red-600">₹{{ number_format($total, 2) }}</p></div>
    <button wire:click="$set('showForm', true)" class="btn-primary mb-4">+ Record Expense</button>
    @if($showForm)
    <form wire:submit.prevent="save" class="form-card mb-6 grid md:grid-cols-2 gap-4">
        <div class="form-field"><label class="form-label">Category</label><input wire:model="category" class="form-control" placeholder="Electricity, Repairs"></div>
        <div class="form-field"><label class="form-label">Amount</label><input type="number" step="0.01" wire:model="amount" class="form-control"></div>
        <div class="form-field"><label class="form-label">Date</label><input type="date" wire:model="expense_date" class="form-control"></div>
        <div class="form-field"><label class="form-label">Vendor</label><select wire:model="vendor_id" class="form-select"><option value="">—</option>@foreach($vendors as $v)<option value="{{ $v->id }}">{{ $v->name }}</option>@endforeach</select></div>
        <div class="form-field md:col-span-2"><label class="form-label">Description</label><input wire:model="description" class="form-control"></div>
        <button type="submit" class="btn-primary">Save</button>
    </form>
    @endif
    <div class="form-card overflow-x-auto">
        <table class="w-full text-sm"><thead><tr class="border-b text-slate-500"><th class="py-2 text-left">Date</th><th>Category</th><th>Vendor</th><th>Amount</th></tr></thead>
        <tbody>@foreach($expenses as $e)<tr class="border-b"><td class="py-2">{{ $e->expense_date->format('d M Y') }}</td><td>{{ $e->category }}</td><td>{{ $e->vendor?->name ?? '—' }}</td><td>₹{{ number_format($e->amount,2) }}</td></tr>@endforeach</tbody></table>
        {{ $expenses->links() }}
    </div>
</div>
