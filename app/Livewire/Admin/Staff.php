<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\StaffMember;
use Livewire\Component;

class Staff extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public bool $showForm = false;

    public string $name = '';

    public string $phone = '';

    public string $role = 'guard';

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:guard,cleaner,manager,electrician,plumber,other',
        ]);

        StaffMember::create([...$data, 'society_id' => $this->societyId(), 'is_active' => true]);
        session()->flash('success', 'Staff added.');
        $this->reset(['showForm', 'name', 'phone']);
        $this->role = 'guard';
    }

    public function delete(int $id): void
    {
        StaffMember::where('society_id', $this->societyId())->findOrFail($id)->delete();
    }

    public function render()
    {
        return $this->societyAdminLayout('Staff Management')
            ->with(['staffList' => StaffMember::where('society_id', $this->societyId())->get()]);
    }
}
