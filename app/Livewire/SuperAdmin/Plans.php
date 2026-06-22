<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Concerns\WithPanelLayout;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Livewire\Component;

class Plans extends Component
{
    use WithPanelLayout;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $price = '0';

    public ?int $max_flats = null;

    public ?int $max_users = null;

    public int $duration_months = 12;

    public bool $is_active = true;

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_flats' => 'nullable|integer|min:1',
            'max_users' => 'nullable|integer|min:1',
            'duration_months' => 'required|integer|min:1',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'price' => $this->price,
            'max_flats' => $this->max_flats,
            'max_users' => $this->max_users,
            'duration_months' => $this->duration_months,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            SubscriptionPlan::findOrFail($this->editingId)->update($data);
        } else {
            SubscriptionPlan::create($data);
        }

        session()->flash('success', 'Plan saved.');
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $p = SubscriptionPlan::findOrFail($id);
        $this->editingId = $id;
        $this->name = $p->name;
        $this->price = (string) $p->price;
        $this->max_flats = $p->max_flats;
        $this->max_users = $p->max_users;
        $this->duration_months = $p->duration_months;
        $this->is_active = $p->is_active;
        $this->showForm = true;
    }

    public function resetForm(): void
    {
        $this->reset(['showForm', 'editingId', 'name', 'price', 'max_flats', 'max_users']);
        $this->duration_months = 12;
        $this->is_active = true;
        $this->price = '0';
    }

    public function render()
    {
        return $this->superAdminLayout('Subscription Plans')
            ->with(['plans' => SubscriptionPlan::orderBy('price')->get()]);
    }
}
