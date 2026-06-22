<div>
    <div class="flex flex-wrap gap-2 mb-4">
        <button wire:click="$set('showForm', true)" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">+ Single Bill</button>
        <button wire:click="generateBulk" wire:confirm="Generate bills for ALL flats?" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm">Bulk Generate</button>
    </div>
    @if($showForm)<form wire:submit.prevent="save" class="bg-white border rounded-xl p-5 mb-4 grid md:grid-cols-3 gap-3">
        <select wire:model="flat_id" class="rounded-lg border-slate-300 text-sm"><option value="">Flat</option>@foreach($flats as $f)<option value="{{ $f->id }}">{{ $f->label }}</option>@endforeach</select>
        <input wire:model="bill_month" placeholder="YYYY-MM" class="rounded-lg border-slate-300 text-sm">
        <input wire:model="amount" type="number" step="0.01" placeholder="Amount" class="rounded-lg border-slate-300 text-sm">
        <input wire:model="due_date" type="date" class="rounded-lg border-slate-300 text-sm">
        <input wire:model="title" class="rounded-lg border-slate-300 text-sm md:col-span-2">
        <button class="bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm">Generate</button>
    </form>@endif
    <div class="bg-white border rounded-xl overflow-hidden"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Flat</th><th class="px-4 py-3">Month</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Status</th></tr></thead>
    <tbody>@foreach($bills as $b)<tr class="border-t"><td class="px-4 py-3">{{ $b->flat?->label }}</td><td class="px-4 py-3">{{ $b->bill_month }}</td><td class="px-4 py-3">₹{{ number_format($b->amount, 2) }}</td><td class="px-4 py-3 capitalize">{{ $b->status }}</td></tr>@endforeach</tbody></table>
    <div class="p-4">{{ $bills->links() }}</div></div>
</div>
