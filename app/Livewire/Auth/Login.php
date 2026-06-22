<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $login = '';

    public string $password = '';

    public bool $remember = false;

    public function authenticate(): void
    {
        $this->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::attempt([$field => $this->login, 'password' => $this->password, 'is_active' => true], $this->remember)) {
            throw ValidationException::withMessages([
                'login' => 'Invalid credentials or inactive account.',
            ]);
        }

        $user = Auth::user();

        if (! $user->canAccessPanel()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'You do not have access to the management panel.',
            ]);
        }

        session()->regenerate();

        $this->redirect(route($user->roleEnum()->panelRoute()), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest', ['title' => 'Login']);
    }
}
