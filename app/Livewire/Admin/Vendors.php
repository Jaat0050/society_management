<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\HandlesAdminForms;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Vendor;
use Livewire\Component;

class Vendors extends Component
{
    use BelongsToSociety, HandlesAdminForms, WithPanelLayout;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $company = '';

    public string $phone = '';

    public string $email = '';

    public string $service_type = '';

    public ?string $contract_start = null;

    public ?string $contract_end = null;

    public string $contract_value = '';

    public string $schedule_notes = '';

    public function save(): void
    {
        $this->ensureSocietyContext();
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'service_type' => 'required|string|max:100',
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date',
            'contract_value' => 'nullable|numeric|min:0',
            'schedule_notes' => 'nullable|string',
        ]);

        $data['society_id'] = $this->societyId();
        $data['contract_value'] = $data['contract_value'] ?: null;

        if ($this->editingId) {
            Vendor::where('society_id', $this->societyId())->findOrFail($this->editingId)->update($data);
        } else {
            Vendor::create($data);
        }

        session()->flash('success', 'Vendor saved.');
        $this->cancelForm();
    }

    public function edit(int $id): void
    {
        $v = Vendor::where('society_id', $this->societyId())->findOrFail($id);
        $this->editingId = $id;
        $this->fill($v->only(['name', 'company', 'phone', 'email', 'service_type', 'schedule_notes']));
        $this->contract_start = $v->contract_start?->format('Y-m-d');
        $this->contract_end = $v->contract_end?->format('Y-m-d');
        $this->contract_value = (string) ($v->contract_value ?? '');
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['showForm', 'editingId', 'name', 'company', 'phone', 'email', 'service_type', 'contract_start', 'contract_end', 'contract_value', 'schedule_notes']);
    }

    public function delete(int $id): void
    {
        Vendor::where('society_id', $this->societyId())->findOrFail($id)->delete();
        session()->flash('success', 'Vendor removed.');
    }

    public function render()
    {
        return $this->societyAdminLayout('Vendor Management')
            ->with(['vendors' => Vendor::where('society_id', $this->societyId())->orderBy('name')->get()]);
    }
}
