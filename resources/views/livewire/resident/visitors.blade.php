<div>
    @include('livewire.partials.alerts')
    <form wire:submit.prevent="preRegister" class="form-card mb-6 space-y-3">
        <h3 class="font-semibold">Pre-register Visitor</h3>
        <input wire:model="visitor_name" class="form-control" placeholder="Visitor name">
        <input wire:model="phone" class="form-control" placeholder="Phone">
        <select wire:model="visit_type" class="form-select"><option value="guest">Guest</option><option value="delivery">Delivery</option><option value="cab">Cab</option></select>
        <button type="submit" class="btn-primary">Create Pass</button>
    </form>
    @foreach($entries as $e)<div class="form-card mb-2 text-sm flex justify-between"><span>{{ $e->visitor_name }} · {{ $e->pass_code }}</span><span>{{ $e->statusLabel() }}</span></div>@endforeach
</div>
