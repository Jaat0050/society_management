<div>
    <div class="flex flex-wrap gap-3 mb-6 items-center">
        <input type="month" wire:model.live="period" class="form-select max-w-xs">
        <a href="{{ route('admin.export.ledger') }}" class="btn-secondary text-sm">Export CSV</a>
        <a href="{{ route('admin.export.bills') }}" class="btn-secondary text-sm">Export Bills</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Income</p><p class="text-xl font-bold text-emerald-600">₹{{ number_format($income,0) }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Expenses</p><p class="text-xl font-bold text-red-600">₹{{ number_format($expense,0) }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Balance</p><p class="text-xl font-bold">₹{{ number_format($balance,0) }}</p></div>
        <div class="form-card !p-4"><p class="text-xs text-slate-500">Pending Dues</p><p class="text-xl font-bold text-amber-600">₹{{ number_format($pending,0) }}</p></div>
    </div>
    <div class="grid md:grid-cols-2 gap-6">
        <div class="form-card"><h3 class="font-semibold mb-3">By Category</h3>
            @foreach($byCategory as $c)<p class="text-sm py-1 flex justify-between"><span>{{ $c->type }} / {{ $c->category }}</span><span>₹{{ number_format($c->total,0) }}</span></p>@endforeach
        </div>
        <div class="form-card"><h3 class="font-semibold mb-3">Recent Ledger</h3>
            @foreach($ledger as $l)<p class="text-sm py-1 border-b flex justify-between"><span>{{ $l->entry_date->format('d M') }} {{ $l->description }}</span><span class="{{ $l->type==='income'?'text-emerald-600':'text-red-600' }}">₹{{ number_format($l->amount,0) }}</span></p>@endforeach
        </div>
    </div>
</div>
