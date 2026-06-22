<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\UserRole;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Admins extends Component
{
    use WithPanelLayout, WithPagination;

    public bool $showForm = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public ?int $society_id = null;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'society_id' => 'required|exists:societies,id',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

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
        $this->reset(['showForm', 'name', 'email', 'phone', 'password', 'society_id']);
    }

    public function toggleActive(int $id): void
    {
        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);
    }

    public function render()
    {
        $admins = User::with('society')->where('role', UserRole::Admin->value)->latest()->paginate(10);

        return $this->superAdminLayout('Assign Society Admins')
            ->with(['admins' => $admins, 'societies' => Society::where('is_active', true)->whereNull('blocked_at')->orderBy('name')->get()]);
    }
}
