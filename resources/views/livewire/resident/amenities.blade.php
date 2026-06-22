<div>
    @include('livewire.partials.alerts')
    <form wire:submit.prevent="book" class="form-card mb-6 space-y-3">
        <select wire:model="amenity_id" class="form-select">@foreach($amenities as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select>
        <input type="datetime-local" wire:model="booked_from" class="form-control">
        <input type="datetime-local" wire:model="booked_to" class="form-control">
        <button type="submit" class="btn-primary">Request Booking</button>
    </form>
    @foreach($bookings as $b)<div class="form-card mb-2 text-sm">{{ $b->amenity->name }} — {{ $b->booked_from->format('d M H:i') }} · {{ $b->status }}</div>@endforeach
</div>
