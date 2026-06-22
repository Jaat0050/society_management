<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Flat;
use App\Models\Member;
use App\Models\ParkingPass;
use App\Models\ParkingSlot;
use Livewire\Component;

class Parking extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $tab = 'slots';

    public string $slot_no = '';

    public string $type = 'car';

    public ?int $flat_id = null;

    public string $pass_vehicle = '';

    public string $pass_sticker = '';

    public string $pass_type = 'resident';

    public ?int $pass_member_id = null;

    public ?int $pass_slot_id = null;

    public function saveSlot(): void
    {
        $data = $this->validate([
            'slot_no' => 'required|string|max:20',
            'type' => 'required|in:car,bike,visitor',
            'flat_id' => 'nullable|exists:flats,id',
        ]);

        ParkingSlot::create([
            ...$data,
            'society_id' => $this->societyId(),
            'status' => $data['flat_id'] ? 'occupied' : 'available',
        ]);
        session()->flash('success', 'Parking slot added.');
        $this->reset(['slot_no', 'flat_id']);
    }

    public function issuePass(): void
    {
        $data = $this->validate([
            'pass_vehicle' => 'required|string|max:30',
            'pass_sticker' => 'nullable|string|max:30',
            'pass_type' => 'required|in:resident,visitor',
            'pass_member_id' => 'nullable|exists:members,id',
            'pass_slot_id' => 'nullable|exists:parking_slots,id',
        ]);

        ParkingPass::create([
            'society_id' => $this->societyId(),
            'parking_slot_id' => $this->pass_slot_id,
            'member_id' => $this->pass_member_id,
            'vehicle_no' => $data['pass_vehicle'],
            'sticker_no' => $data['pass_sticker'] ?: null,
            'pass_type' => $data['pass_type'],
            'valid_from' => now(),
            'valid_until' => now()->addYear(),
        ]);

        session()->flash('success', 'Parking pass issued.');
        $this->reset(['pass_vehicle', 'pass_sticker', 'pass_member_id', 'pass_slot_id']);
    }

    public function deleteSlot(int $id): void
    {
        ParkingSlot::where('society_id', $this->societyId())->findOrFail($id)->delete();
    }

    public function render()
    {
        $sid = $this->societyId();

        return $this->societyAdminLayout('Parking Management')
            ->with([
                'slots' => ParkingSlot::where('society_id', $sid)->with('flat')->get(),
                'passes' => ParkingPass::where('society_id', $sid)->with(['member', 'parkingSlot'])->latest()->get(),
                'flats' => Flat::where('society_id', $sid)->get(),
                'members' => Member::where('society_id', $sid)->orderBy('name')->get(),
            ]);
    }
}
