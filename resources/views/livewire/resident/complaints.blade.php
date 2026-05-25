<div>
    @include('livewire.partials.alerts')
    <form wire:submit.prevent="submit" class="form-card mb-6 space-y-3">
        <h3 class="font-semibold">Raise Complaint</h3>
        <input wire:model="subject" class="form-control" placeholder="Subject">
        <select wire:model="category" class="form-select"><option value="maintenance">Maintenance</option><option value="security">Security</option><option value="parking">Parking</option><option value="noise">Noise</option></select>
        <textarea wire:model="description" class="form-control" rows="3"></textarea>
        <button type="submit" class="btn-primary">Submit</button>
    </form>
    @foreach($complaints as $c)<div class="form-card mb-2 py-2"><strong>{{ $c->subject }}</strong><p class="text-xs text-slate-500">{{ $c->status }} · {{ $c->created_at->format('d M Y') }}</p></div>@endforeach
</div>
