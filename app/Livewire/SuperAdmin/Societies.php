<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Society;
use App\Models\SubscriptionPlan;
use Livewire\Component;
use Livewire\WithPagination;

class Societies extends Component
{
    use WithPanelLayout, WithPagination;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $search = '';

    public string $name = '';

    public string $registration_no = '';

    public string $address = '';

    public string $city = '';

    public string $state = '';

    public string $pincode = '';

    public string $phone = '';

    public string $email = '';

    public ?int $subscription_plan_id = null;

    public string $subscription_status = 'trial';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'registration_no' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'subscription_status' => 'required|in:active,trial,expired,cancelled',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $s = Society::findOrFail($id);
        $this->editingId = $id;
        $this->fill($s->only([
            'name', 'registration_no', 'address', 'city', 'state', 'pincode',
            'phone', 'email', 'subscription_plan_id', 'subscription_status',
        ]));
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            Society::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Society updated.');
        } else {
            Society::create($data);
            session()->flash('success', 'Society created.');
        }

        $this->resetForm();
    }

    public function toggleBlock(int $id): void
    {
        $s = Society::findOrFail($id);
        if ($s->blocked_at) {
            $s->update(['blocked_at' => null, 'block_reason' => null, 'is_active' => true]);
            session()->flash('success', 'Society unblocked.');
        } else {
            $s->update(['blocked_at' => now(), 'block_reason' => 'Blocked by Super Admin', 'is_active' => false]);
            session()->flash('success', 'Society blocked.');
        }
    }

    public function delete(int $id): void
    {
        Society::findOrFail($id)->delete();
        session()->flash('success', 'Society deleted.');
    }

    public function resetForm(): void
    {
        $this->reset(['showForm', 'editingId', 'name', 'registration_no', 'address', 'city', 'state', 'pincode', 'phone', 'email', 'subscription_plan_id']);
        $this->subscription_status = 'trial';
    }

    public function render()
    {
        $societies = Society::with('subscriptionPlan')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('city', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return $this->superAdminLayout('Manage Societies')
            ->with(['societies' => $societies, 'plans' => SubscriptionPlan::where('is_active', true)->get()]);
    }
}
