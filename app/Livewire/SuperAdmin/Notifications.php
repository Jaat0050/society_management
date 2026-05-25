<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Concerns\WithPanelLayout;
use App\Models\GlobalNotification;
use Livewire\Component;

class Notifications extends Component
{
    use WithPanelLayout;

    public string $title = '';

    public string $body = '';

    public function send(): void
    {
        $this->validate(['title' => 'required|max:255', 'body' => 'required']);

        GlobalNotification::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'body' => $this->body,
            'sent_at' => now(),
        ]);

        session()->flash('success', 'Global notification sent to all societies.');
        $this->reset(['title', 'body']);
    }

    public function render()
    {
        return $this->superAdminLayout('Global Notifications')
            ->with(['notifications' => GlobalNotification::latest()->take(20)->get()]);
    }
}
