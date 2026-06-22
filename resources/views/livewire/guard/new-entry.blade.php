<div>
    @include('livewire.partials.alerts')
    <form wire:submit.prevent="save" class="space-y-4">
        <div class="form-field"><label class="form-label field-required">Visitor Name</label><input type="text" wire:model="visitor_name" class="form-control"></div>
        <div class="form-field"><label class="form-label">Phone</label><input type="tel" wire:model="phone" class="form-control"></div>
        <div class="form-field"><label class="form-label">Visit Type</label>
            <select wire:model="visit_type" class="form-select">
                <option value="guest">Guest</option><option value="delivery">Delivery</option><option value="cab">Cab</option>
                <option value="domestic_help">Domestic Help</option><option value="vendor">Vendor</option><option value="other">Other</option>
            </select>
        </div>
        <div class="form-field"><label class="form-label">Visiting Flat</label>
            <select wire:model="flat_id" class="form-select"><option value="">— Select —</option>@foreach($flats as $f)<option value="{{ $f->id }}">{{ $f->label }}</option>@endforeach</select>
        </div>
        <div class="form-field"><label class="form-label">Host Resident</label>
            <select wire:model="member_id" class="form-select"><option value="">— Optional —</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select>
        </div>
        @if($visit_type === 'delivery')
        <div class="form-field"><label class="form-label">Delivery Staff</label>
            <select wire:model="delivery_staff_id" class="form-select"><option value="">— Select —</option>@foreach($deliveryStaff as $d)<option value="{{ $d->id }}">{{ $d->name }} ({{ $d->company }})</option>@endforeach</select>
        </div>
        @endif
        <div class="grid grid-cols-2 gap-3">
            <div class="form-field"><label class="form-label">Persons</label><input type="number" wire:model="person_count" class="form-control" min="1" max="20"></div>
            <div class="form-field"><label class="form-label">Vehicle No</label><input type="text" wire:model="vehicle_no" class="form-control"></div>
        </div>
        <div class="form-field"><label class="form-label">Purpose</label><input type="text" wire:model="purpose" class="form-control"></div>
        <button type="submit" class="btn-primary w-full !w-full" wire:loading.attr="disabled">Create Pass & QR</button>
    </form>
</div>
