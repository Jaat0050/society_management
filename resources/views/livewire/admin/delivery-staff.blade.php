<div>
    @include('livewire.partials.alerts')
    <button type="button" wire:click="$set('showForm', true)" class="btn-primary mb-4">+ Add Delivery Staff</button>
    @if($showForm)
    <form wire:submit.prevent="save" class="form-card mb-6 space-y-4">
        <h3 class="font-semibold">{{ $editingId ? 'Edit' : 'New' }} Delivery Staff</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="form-field"><label class="form-label field-required">Name</label><input type="text" wire:model="name" class="form-control"></div>
            <div class="form-field"><label class="form-label field-required">Phone</label><input type="tel" wire:model="phone" class="form-control"></div>
            <div class="form-field"><label class="form-label">Company</label><input type="text" wire:model="company" class="form-control" placeholder="Swiggy, Amazon..."></div>
            <div class="form-field"><label class="form-label">Vehicle No</label><input type="text" wire:model="vehicle_no" class="form-control"></div>
            <div class="form-field"><label class="form-label">ID Card</label><input type="text" wire:model="id_card_no" class="form-control"></div>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_verified" class="rounded"> Verified</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active" class="rounded"> Active</label>
        <div class="flex gap-2"><button type="submit" class="btn-primary">Save</button><button type="button" wire:click="cancelForm" class="btn-secondary">Cancel</button></div>
    </form>
    @endif
    <div class="form-card">
        <table class="w-full text-sm">
            <thead><tr class="border-b text-left text-slate-500"><th class="py-2">Name</th><th>Company</th><th>Phone</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($staff as $s)
                <tr class="border-b"><td class="py-3 font-medium">{{ $s->name }}</td><td>{{ $s->company }}</td><td>{{ $s->phone }}</td>
                    <td>@if($s->is_verified)<span class="text-emerald-600 text-xs">Verified</span>@else<span class="text-slate-400 text-xs">—</span>@endif</td>
                    <td class="text-right"><button wire:click="edit({{ $s->id }})" class="text-indigo-600 text-xs mr-2">Edit</button><button wire:click="delete({{ $s->id }})" wire:confirm="Remove?" class="text-red-600 text-xs">Delete</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
