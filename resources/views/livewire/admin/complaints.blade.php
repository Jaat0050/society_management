<div>
    @include('livewire.partials.alerts')
    <button wire:click="$set('showForm', true)" class="btn-primary mb-4">+ Raise Complaint</button>
    @if($showForm)
    <form wire:submit.prevent="create" class="form-card mb-6 grid md:grid-cols-2 gap-4">
        <div class="form-field md:col-span-2"><label class="form-label">Subject</label><input wire:model="subject" class="form-control"></div>
        <div class="form-field"><label class="form-label">Category</label><select wire:model="category" class="form-select"><option value="maintenance">Maintenance</option><option value="security">Security</option><option value="parking">Parking</option><option value="noise">Noise</option><option value="other">Other</option></select></div>
        <div class="form-field"><label class="form-label">Flat</label><select wire:model="flat_id" class="form-select"><option value="">—</option>@foreach($flats as $f)<option value="{{ $f->id }}">{{ $f->label }}</option>@endforeach</select></div>
        <div class="form-field md:col-span-2"><label class="form-label">Description</label><textarea wire:model="description" class="form-control" rows="3"></textarea></div>
        <button type="submit" class="btn-primary">Create</button>
    </form>
    @endif
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 form-card overflow-x-auto">
            <table class="w-full text-sm"><thead><tr class="border-b text-slate-500"><th class="py-2 text-left">Subject</th><th>Flat</th><th>Status</th><th></th></tr></thead>
            <tbody>@foreach($complaints as $c)<tr class="border-b"><td class="py-3">{{ $c->subject }}</td><td>{{ $c->flat?->label }}</td><td>{{ $c->status }}</td><td><button wire:click="select({{ $c->id }})" class="text-indigo-600 text-xs">Manage</button></td></tr>@endforeach</tbody></table>
            {{ $complaints->links() }}
        </div>
        @if($selectedId)
        <form wire:submit.prevent="updateStatus" class="form-card space-y-3">
            <h3 class="font-semibold">Assign & Resolve</h3>
            <select wire:model="status" class="form-select"><option value="open">Open</option><option value="in_progress">In Progress</option><option value="resolved">Resolved</option><option value="closed">Closed</option></select>
            <select wire:model="assigned_staff_id" class="form-select"><option value="">Assign staff</option>@foreach($staff as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
            <select wire:model="assigned_vendor_id" class="form-select"><option value="">Assign vendor</option>@foreach($vendors as $v)<option value="{{ $v->id }}">{{ $v->name }}</option>@endforeach</select>
            <textarea wire:model="resolution_notes" class="form-control" rows="3" placeholder="Resolution notes"></textarea>
            <button type="submit" class="btn-primary w-full">Update</button>
            <div class="border-t pt-3"><p class="text-xs font-semibold text-slate-500 mb-2">History</p>@foreach($logs as $log)<p class="text-xs py-1">{{ $log->created_at->format('d M H:i') }} — {{ $log->status }}: {{ $log->note }}</p>@endforeach</div>
        </form>
        @endif
    </div>
</div>
