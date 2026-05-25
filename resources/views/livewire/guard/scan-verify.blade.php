<div>
    <div class="form-field mb-4">
        <label class="form-label">Pass Code or QR Token</label>
        <input type="text" wire:model="lookup" class="form-control" placeholder="Enter pass code e.g. ABCD1234">
        <button type="button" wire:click="verify" class="btn-primary w-full !w-full mt-3">Verify</button>
    </div>
    @if($message)<p class="text-sm mb-4 {{ $entry ? 'text-emerald-400' : 'text-amber-400' }}">{{ $message }}</p>@endif
    @if($entry)
    <div class="rounded-xl bg-slate-800 border border-slate-600 p-5 space-y-3">
        <p class="text-xl font-bold">{{ $entry->visitor_name }}</p>
        <p class="text-sm text-slate-400">Pass: <span class="text-white font-mono">{{ $entry->pass_code }}</span></p>
        <p class="text-sm">Flat: {{ $entry->flat?->label ?? '—' }} | {{ $entry->statusLabel() }}</p>
        <div class="flex flex-wrap gap-2 pt-2">
            @if(in_array($entry->status, ['approved','pending']))
            <button type="button" wire:click="checkIn({{ $entry->id }})" class="btn-primary">Check IN</button>
            @endif
            @if($entry->status === 'checked_in')
            <button type="button" wire:click="checkOut({{ $entry->id }})" class="btn-secondary !bg-amber-600 !text-white !border-amber-500">Check OUT</button>
            @endif
            <a href="{{ route('guard.pass', $entry->id) }}" wire:navigate class="btn-secondary">View Pass</a>
        </div>
    </div>
    @endif
</div>
