<div>
    @include('livewire.partials.alerts')
    <div class="flex gap-2 mb-4"><button wire:click="$set('tab','slots')" class="px-3 py-1 rounded {{ $tab==='slots'?'bg-indigo-600 text-white':'bg-slate-100' }}">Slots</button><button wire:click="$set('tab','passes')" class="px-3 py-1 rounded {{ $tab==='passes'?'bg-indigo-600 text-white':'bg-slate-100' }}">Passes & Stickers</button></div>
    @if($tab==='slots')
    <form wire:submit.prevent="saveSlot" class="form-card mb-4 flex flex-wrap gap-3 items-end">
        <input wire:model="slot_no" class="form-control" placeholder="Slot P-01">
        <select wire:model="type" class="form-select"><option value="car">Car</option><option value="bike">Bike</option><option value="visitor">Visitor</option></select>
        <select wire:model="flat_id" class="form-select"><option value="">Unassigned</option>@foreach($flats as $f)<option value="{{ $f->id }}">{{ $f->label }}</option>@endforeach</select>
        <button type="submit" class="btn-primary">Add Slot</button>
    </form>
    @foreach($slots as $s)<div class="form-card mb-2 py-2 flex justify-between"><span>{{ $s->slot_no }} ({{ $s->type }}) — {{ $s->flat?->label ?? 'Available' }}</span><button wire:click="deleteSlot({{ $s->id }})" class="text-red-600 text-xs">Delete</button></div>@endforeach
    @else
    <form wire:submit.prevent="issuePass" class="form-card mb-4 grid md:grid-cols-3 gap-3">
        <input wire:model="pass_vehicle" class="form-control" placeholder="Vehicle no">
        <input wire:model="pass_sticker" class="form-control" placeholder="Sticker no">
        <select wire:model="pass_type" class="form-select"><option value="resident">Resident</option><option value="visitor">Visitor</option></select>
        <select wire:model="pass_member_id" class="form-select"><option value="">Member</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select>
        <select wire:model="pass_slot_id" class="form-select"><option value="">Slot</option>@foreach($slots as $s)<option value="{{ $s->id }}">{{ $s->slot_no }}</option>@endforeach</select>
        <button type="submit" class="btn-primary">Issue Pass</button>
    </form>
    @foreach($passes as $p)<div class="form-card mb-2 py-2 text-sm">{{ $p->vehicle_no }} · {{ $p->pass_type }} · Sticker {{ $p->sticker_no ?? '—' }} · Valid till {{ $p->valid_until?->format('d M Y') }}</div>@endforeach
    @endif
</div>
