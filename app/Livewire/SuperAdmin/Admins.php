<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\UserRole;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Admins extends Component
{
    use WithPanelLayout, WithPagination;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public ?int $society_id = null;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:6'],
            'society_id' => 'required|exists:societies,id',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->password = '';
        $this->society_id = $user->society_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $user = User::where('role', UserRole::Admin->value)->findOrFail($this->editingId);
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'society_id' => $data['society_id'],
            ];
            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }
            $user->update($payload);
            session()->flash('success', 'Society admin updated successfully.');
        } else {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => UserRole::Admin->value,
                'society_id' => $data['society_id'],
                'is_active' => true,
            ]);
            session()->flash('success', 'Society admin assigned successfully.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        User::where('role', UserRole::Admin->value)->findOrFail($id)->delete();
        session()->flash('success', 'Society admin deleted.');
    }

    public function toggleActive(int $id): void
    {
        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);
    }

    public function resetForm(): void
    {
        $this->reset(['showForm', 'editingId', 'name', 'email', 'phone', 'password', 'society_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $admins = User::with('society')->where('role', UserRole::Admin->value)->latest()->paginate(10);

        return $this->superAdminLayout('Assign Society Admins')
            ->with(['admins' => $admins, 'societies' => Society::where('is_active', true)->whereNull('blocked_at')->orderBy('name')->get()]);
    }
}
