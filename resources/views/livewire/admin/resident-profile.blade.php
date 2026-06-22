<div>
    @include('livewire.partials.alerts')
    <a href="{{ route('admin.residents') }}" wire:navigate class="text-sm text-indigo-600 mb-4 inline-block">← Back to Residents</a>
    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach(['profile'=>'Profile','family'=>'Family','vehicles'=>'Vehicles','documents'=>'Documents'] as $k=>$l)
            <button type="button" wire:click="$set('tab','{{ $k }}')" class="px-4 py-2 rounded-lg text-sm {{ $tab===$k ? 'bg-indigo-600 text-white' : 'bg-slate-100' }}">{{ $l }}</button>
        @endforeach
    </div>
    @if($tab==='profile')
    <form wire:submit.prevent="saveProfile" class="form-card space-y-4 max-w-2xl">
        <div class="grid md:grid-cols-2 gap-4">
            <div class="form-field"><label class="form-label">Name</label><input wire:model="name" class="form-control"></div>
            <div class="form-field"><label class="form-label">Phone</label><input wire:model="phone" class="form-control"></div>
            <div class="form-field"><label class="form-label">Email</label><input wire:model="email" class="form-control"></div>
            <div class="form-field"><label class="form-label">Type</label>
                <select wire:model="member_type" class="form-select"><option value="owner">Owner</option><option value="tenant">Tenant</option><option value="family">Family</option></select>
            </div>
            <div class="form-field"><label class="form-label">Flat</label>
                <select wire:model="flat_id" class="form-select"><option value="">—</option>@foreach($flats as $f)<option value="{{ $f->id }}">{{ $f->label }}</option>@endforeach</select>
            </div>
            <div class="form-field"><label class="form-label">ID Proof Type</label><input wire:model="id_proof_type" class="form-control" placeholder="Aadhaar / PAN"></div>
            <div class="form-field"><label class="form-label">ID Number</label><input wire:model="id_proof_no" class="form-control"></div>
            <div class="form-field"><label class="form-label">Move In</label><input type="date" wire:model="move_in_date" class="form-control"></div>
            <div class="form-field"><label class="form-label">Move Out</label><input type="date" wire:model="move_out_date" class="form-control"></div>
        </div>
        <button type="submit" class="btn-primary">Save Profile</button>
    </form>
    @elseif($tab==='family')
    <form wire:submit.prevent="addFamily" class="form-card mb-4 grid md:grid-cols-4 gap-3 items-end">
        <div class="form-field"><label class="form-label">Name</label><input wire:model="family_name" class="form-control"></div>
        <div class="form-field"><label class="form-label">Relation</label><input wire:model="family_relation" class="form-control" placeholder="Spouse, Child"></div>
        <div class="form-field"><label class="form-label">Phone</label><input wire:model="family_phone" class="form-control"></div>
        <button type="submit" class="btn-primary">Add</button>
    </form>
    <div class="form-card">@forelse($member->family as $f)
        <div class="flex justify-between py-2 border-b"><span>{{ $f->name }} ({{ $f->relation }})</span><button wire:click="deleteFamily({{ $f->id }})" class="text-red-600 text-xs">Remove</button></div>
    @empty<p class="text-slate-500">No family members.</p>@endforelse</div>
    @elseif($tab==='vehicles')
    <form wire:submit.prevent="addVehicle" class="form-card mb-4 flex gap-3 flex-wrap items-end">
        <div class="form-field"><label class="form-label">Vehicle No</label><input wire:model="vehicle_no" class="form-control"></div>
        <div class="form-field"><label class="form-label">Type</label><select wire:model="vehicle_type" class="form-select"><option value="car">Car</option><option value="bike">Bike</option><option value="other">Other</option></select></div>
        <button type="submit" class="btn-primary">Add Vehicle</button>
    </form>
    <div class="form-card">@forelse($member->vehicles as $v)
        <div class="flex justify-between py-2 border-b"><span>{{ $v->vehicle_no }} ({{ $v->type }}) @if($v->sticker_no) · {{ $v->sticker_no }}@endif</span><button wire:click="deleteVehicle({{ $v->id }})" class="text-red-600 text-xs">Remove</button></div>
    @empty<p class="text-slate-500">No vehicles.</p>@endforelse</div>
    @else
    <form wire:submit.prevent="addDocument" class="form-card mb-4 space-y-3 max-w-xl">
        <div class="form-field"><label class="form-label">Title</label><input wire:model="doc_title" class="form-control"></div>
        <div class="form-field"><label class="form-label">Type</label><select wire:model="doc_type" class="form-select"><option value="aadhaar">Aadhaar</option><option value="pan">PAN</option><option value="agreement">Agreement</option><option value="photo">Photo</option><option value="other">Other</option></select></div>
        <div class="form-field"><label class="form-label">File</label><input type="file" wire:model="doc_file" class="form-control"></div>
        <button type="submit" class="btn-primary">Upload Document</button>
    </form>
    <div class="form-card">@forelse($member->documents as $d)
        <div class="flex justify-between py-2 border-b"><span>{{ $d->title }} ({{ $d->doc_type }}) @if($d->file_path)<a href="{{ asset('storage/'.$d->file_path) }}" target="_blank" class="text-indigo-600 text-xs ml-2">View</a>@endif</span><button wire:click="deleteDocument({{ $d->id }})" class="text-red-600 text-xs">Delete</button></div>
    @empty<p class="text-slate-500">No documents.</p>@endforelse</div>
    @endif
</div>
