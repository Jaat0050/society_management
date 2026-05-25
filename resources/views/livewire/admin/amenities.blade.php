<div>
    @include('livewire.partials.alerts')
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <form wire:submit.prevent="saveAmenity" class="form-card space-y-3">
            <h3 class="font-semibold">Add Amenity</h3>
            <input wire:model="name" class="form-control" placeholder="Clubhouse / Gym / Guest Room">
            <input type="number" wire:model="hourly_rate" class="form-control" placeholder="Hourly rate">
            <button type="submit" class="btn-primary">Add</button>
        </form>
        <form wire:submit.prevent="createBooking" class="form-card space-y-3">
            <h3 class="font-semibold">Schedule Booking</h3>
            <select wire:model="booking_amenity_id" class="form-select"><option value="">Amenity</option>@foreach($amenities as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select>
            <select wire:model="booking_flat_id" class="form-select"><option value="">Flat</option>@foreach($flats as $f)<option value="{{ $f->id }}">{{ $f->label }}</option>@endforeach</select>
            <input type="datetime-local" wire:model="booked_from" class="form-control">
            <input type="datetime-local" wire:model="booked_to" class="form-control">
            <button type="submit" class="btn-primary">Book Slot</button>
        </form>
    </div>
    <div class="form-card">
        <h3 class="font-semibold mb-3">Bookings</h3>
        @foreach($bookings as $b)
        <div class="flex justify-between py-2 border-b text-sm">
            <span>{{ $b->amenity->name }} — {{ $b->flat?->label }} · {{ $b->booked_from->format('d M H:i') }}</span>
            <span>{{ $b->status }}
                @if($b->status==='pending')<button wire:click="updateBookingStatus({{ $b->id }}, 'approved')" class="text-emerald-600 text-xs ml-2">Approve</button><button wire:click="updateBookingStatus({{ $b->id }}, 'rejected')" class="text-red-600 text-xs">Reject</button>@endif
            </span>
        </div>
        @endforeach
    </div>
</div>
