<?php

namespace App\Livewire\Guard;

use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithGuardLayout;
use App\Models\DeliveryStaff;
use App\Models\Flat;
use App\Models\Member;
use App\Services\VisitorService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class NewEntry extends Component
{
    use HandlesAdminForms, WithGuardLayout;

    public string $visitor_name = '';

    public string $phone = '';

    public ?int $flat_id = null;

    public ?int $member_id = null;

    public ?int $delivery_staff_id = null;

    public string $visit_type = 'guest';

    public string $vehicle_no = '';

    public int $person_count = 1;

    public string $purpose = '';

    public string $id_proof = '';

    public function save(): void
    {
        $this->ensureSocietyContext();
        $this->normalizeNullableIds('flat_id', 'member_id', 'delivery_staff_id');

        $societyId = auth()->user()->society_id;

        $data = $this->validate([
            'visitor_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'flat_id' => ['nullable', 'integer', Rule::exists('flats', 'id')->where('society_id', $societyId)],
            'member_id' => ['nullable', 'integer', Rule::exists('members', 'id')->where('society_id', $societyId)],
            'delivery_staff_id' => ['nullable', 'integer', Rule::exists('delivery_staff', 'id')->where('society_id', $societyId)],
            'visit_type' => 'required|in:guest,delivery,cab,domestic_help,vendor,other',
            'vehicle_no' => 'nullable|string|max:30',
            'person_count' => 'required|integer|min:1|max:20',
            'purpose' => 'nullable|string|max:500',
            'id_proof' => 'nullable|string|max:50',
        ]);

        $service = app(VisitorService::class);

        $entry = $service->createEntry([
            'society_id' => $societyId,
            'flat_id' => $this->flat_id,
            'member_id' => $this->member_id,
            'delivery_staff_id' => $this->delivery_staff_id,
            'created_by' => auth()->id(),
            'visitor_name' => $data['visitor_name'],
            'phone' => $data['phone'] ?? null,
            'visit_type' => $data['visit_type'],
            'vehicle_no' => $data['vehicle_no'] ?: null,
            'person_count' => $data['person_count'],
            'purpose' => $data['purpose'] ?: null,
            'id_proof' => $data['id_proof'] ?: null,
        ]);

        session()->flash('success', "Visitor pass created. Code: {$entry->pass_code} | Status: {$entry->statusLabel()}");

        $this->redirect(route('guard.pass', $entry->id), navigate: true);
    }

    public function render()
    {
        $sid = auth()->user()->society_id;

        return $this->guardLayout('New Gate Entry')
            ->with([
                'flats' => Flat::where('society_id', $sid)->orderBy('flat_no')->get(),
                'members' => Member::where('society_id', $sid)->orderBy('name')->get(),
                'deliveryStaff' => DeliveryStaff::where('society_id', $sid)->where('is_active', true)->orderBy('name')->get(),
            ]);
    }
}
