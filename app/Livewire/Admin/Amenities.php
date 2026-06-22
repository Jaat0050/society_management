<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\Flat;
use Livewire\Component;

class Amenities extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $name = '';

    public string $hourly_rate = '0';

    public ?int $booking_amenity_id = null;

    public ?int $booking_flat_id = null;

    public string $booked_from = '';

    public string $booked_to = '';

    public string $booking_notes = '';

    public function saveAmenity(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        Amenity::create([...$data, 'society_id' => $this->societyId(), 'is_active' => true]);
        session()->flash('success', 'Amenity added.');
        $this->reset(['name', 'hourly_rate']);
        $this->hourly_rate = '0';
    }

    public function createBooking(): void
    {
        $this->validate([
            'booking_amenity_id' => 'required|exists:amenities,id',
            'booking_flat_id' => 'nullable|exists:flats,id',
            'booked_from' => 'required|date',
            'booked_to' => 'required|date|after:booked_from',
            'booking_notes' => 'nullable|string',
        ]);

        $conflict = AmenityBooking::where('amenity_id', $this->booking_amenity_id)
            ->where('status', '!=', 'rejected')
            ->where(fn ($q) => $q->whereBetween('booked_from', [$this->booked_from, $this->booked_to])
                ->orWhereBetween('booked_to', [$this->booked_from, $this->booked_to]))
            ->exists();

        if ($conflict) {
            session()->flash('error', 'Slot conflict — another booking exists for this time.');

            return;
        }

        AmenityBooking::create([
            'amenity_id' => $this->booking_amenity_id,
            'flat_id' => $this->booking_flat_id,
            'user_id' => auth()->id(),
            'booked_from' => $this->booked_from,
            'booked_to' => $this->booked_to,
            'status' => 'approved',
            'notes' => $this->booking_notes ?: null,
        ]);

        session()->flash('success', 'Booking created.');
        $this->reset(['booking_amenity_id', 'booking_flat_id', 'booked_from', 'booked_to', 'booking_notes']);
    }

    public function updateBookingStatus(int $id, string $status): void
    {
        $booking = AmenityBooking::whereHas('amenity', fn ($q) => $q->where('society_id', $this->societyId()))->findOrFail($id);
        $booking->update(['status' => $status]);
    }

    public function render()
    {
        return $this->societyAdminLayout('Amenity Booking')
            ->with([
                'amenities' => Amenity::where('society_id', $this->societyId())->get(),
                'flats' => Flat::where('society_id', $this->societyId())->get(),
                'bookings' => AmenityBooking::whereHas('amenity', fn ($q) => $q->where('society_id', $this->societyId()))
                    ->with(['amenity', 'flat'])->latest()->take(30)->get(),
            ]);
    }
}
