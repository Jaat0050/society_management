<div class="text-center">
    <div class="rounded-2xl bg-white p-6 inline-block mb-6">
        <img src="{{ $qrUrl }}" alt="QR Code" class="mx-auto w-56 h-56">
    </div>
    <p class="text-3xl font-mono font-bold text-emerald-400 mb-2">{{ $entry->pass_code }}</p>
    <h2 class="text-2xl font-bold mb-1">{{ $entry->visitor_name }}</h2>
    <p class="text-slate-400 mb-4">{{ $entry->flat?->label ?? 'No flat' }} · {{ $entry->statusLabel() }}</p>
    <p class="text-sm text-slate-500 mb-6 break-all">{{ $entry->approvalUrl() }}</p>
    <div class="flex flex-wrap gap-3 justify-center">
        @if(in_array($entry->status, ['approved','pending']))
        <button type="button" wire:click="checkIn" class="btn-primary">Gate Check IN</button>
        @endif
        @if($entry->status === 'checked_in')
        <button type="button" wire:click="checkOut" class="btn-secondary !bg-amber-500 !text-white">Gate Check OUT</button>
        @endif
    </div>
</div>
