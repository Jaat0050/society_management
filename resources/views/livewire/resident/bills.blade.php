<div>
    @include('livewire.partials.alerts')
    @forelse($bills as $b)
    <div class="form-card mb-3 flex justify-between items-center">
        <div><p class="font-medium">{{ $b->title }}</p><p class="text-sm text-slate-500">{{ $b->bill_month }} · Due {{ $b->due_date->format('d M Y') }}</p>
        <p class="text-lg font-bold">₹{{ number_format($b->totalDue(),2) }} @if($b->penalty_amount)<span class="text-xs text-red-600">incl. penalty</span>@endif</p></div>
        @if($b->status !== 'paid')<button wire:click="payOnline({{ $b->id }})" class="btn-primary shrink-0">Pay Online</button>@else<span class="text-emerald-600 text-sm font-semibold">Paid</span>@endif
    </div>
    @empty<p class="text-slate-500">No bills for your flat.</p>@endforelse
</div>
