<?php

namespace App\Livewire\Resident;

use App\Livewire\Concerns\WithResidentLayout;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use Livewire\Component;

class Amenities extends Component
{
    use WithResidentLayout;

    public ?int $amenity_id = null;

    public string $booked_from = '';

    public string $booked_to = '';

    public function book(): void
    {
        $member = auth()->user()->member;

        $this->validate([
            'amenity_id' => 'required|exists:amenities,id',
            'booked_from' => 'required|date|after:now',
            'booked_to' => 'required|date|after:booked_from',
        ]);

        AmenityBooking::create([
            'amenity_id' => $this->amenity_id,
            'flat_id' => $member?->flat_id,
            'user_id' => auth()->id(),
            'booked_from' => $this->booked_from,
            'booked_to' => $this->booked_to,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Booking request submitted.');
        $this->reset(['amenity_id', 'booked_from', 'booked_to']);
    }

    public function render()
    {
        $sid = auth()->user()->society_id;
        $flatId = auth()->user()->member?->flat_id;

        return $this->residentLayout('Amenity Booking')
            ->with([
                'amenities' => Amenity::where('society_id', $sid)->where('is_active', true)->get(),
                'bookings' => $flatId
                    ? AmenityBooking::where('flat_id', $flatId)->with('amenity')->latest()->get()
                    : collect(),
            ]);
    }
}
