<div>
    @include('livewire.partials.alerts')
    <button wire:click="$set('showForm', true)" class="btn-primary mb-4">+ Add Vendor</button>
    @if($showForm)
    <form wire:submit.prevent="save" class="form-card mb-6 space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
            <div class="form-field"><label class="form-label">Name</label><input wire:model="name" class="form-control"></div>
            <div class="form-field"><label class="form-label">Company</label><input wire:model="company" class="form-control"></div>
            <div class="form-field"><label class="form-label">Phone</label><input wire:model="phone" class="form-control"></div>
            <div class="form-field"><label class="form-label">Service</label><input wire:model="service_type" class="form-control" placeholder="Plumbing, Lift AMC"></div>
            <div class="form-field"><label class="form-label">Contract Start</label><input type="date" wire:model="contract_start" class="form-control"></div>
            <div class="form-field"><label class="form-label">Contract End</label><input type="date" wire:model="contract_end" class="form-control"></div>
            <div class="form-field"><label class="form-label">Contract Value</label><input type="number" wire:model="contract_value" class="form-control"></div>
        </div>
        <div class="form-field"><label class="form-label">Schedule Notes</label><textarea wire:model="schedule_notes" class="form-control" rows="2"></textarea></div>
        <div class="flex gap-2"><button type="submit" class="btn-primary">Save</button><button type="button" wire:click="cancelForm" class="btn-secondary">Cancel</button></div>
    </form>
    @endif
    <div class="form-card">@foreach($vendors as $v)
        <div class="py-3 border-b flex justify-between"><div><p class="font-medium">{{ $v->name }}</p><p class="text-xs text-slate-500">{{ $v->service_type }} · {{ $v->phone }} · Contract till {{ $v->contract_end?->format('d M Y') ?? '—' }}</p></div>
        <div><button wire:click="edit({{ $v->id }})" class="text-indigo-600 text-xs mr-2">Edit</button><button wire:click="delete({{ $v->id }})" wire:confirm class="text-red-600 text-xs">Delete</button></div></div>
    @endforeach</div>
</div>
