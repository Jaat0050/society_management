<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\DeliveryStaff as DeliveryStaffModel;
use Illuminate\Validation\Rule;
use Livewire\Component;

class DeliveryStaff extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $phone = '';

    public string $company = '';

    public string $vehicle_no = '';

    public string $id_card_no = '';

    public bool $is_verified = false;

    public bool $is_active = true;

    public function save(): void
    {
        $this->ensureSocietyContext();
        $societyId = $this->societyId();

        $data = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'nullable|string|max:100',
            'vehicle_no' => 'nullable|string|max:30',
            'id_card_no' => 'nullable|string|max:50',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data['society_id'] = $societyId;

        if ($this->editingId) {
            DeliveryStaffModel::where('society_id', $societyId)->findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Delivery staff updated.');
        } else {
            DeliveryStaffModel::create($data);
            session()->flash('success', 'Delivery staff registered.');
        }

        $this->cancelForm();
    }

    public function edit(int $id): void
    {
        $s = DeliveryStaffModel::where('society_id', $this->societyId())->findOrFail($id);
        $this->editingId = $id;
        $this->fill($s->only(['name', 'phone', 'company', 'vehicle_no', 'id_card_no', 'is_verified', 'is_active']));
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        DeliveryStaffModel::where('society_id', $this->societyId())->findOrFail($id)->delete();
        session()->flash('success', 'Removed.');
    }

    public function cancelForm(): void
    {
        $this->reset(['showForm', 'editingId', 'name', 'phone', 'company', 'vehicle_no', 'id_card_no']);
        $this->is_verified = false;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return $this->societyAdminLayout('Delivery Staff')
            ->with([
                'staff' => DeliveryStaffModel::where('society_id', $this->societyId())->orderBy('name')->get(),
            ]);
    }
}
