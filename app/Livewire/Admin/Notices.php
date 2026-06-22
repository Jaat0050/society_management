<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Notice;
use Livewire\Component;

class Notices extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $title = '';

    public string $body = '';

    public bool $is_pinned = false;

    public function publish(): void
    {
        $this->validate(['title' => 'required|max:255', 'body' => 'required']);

        Notice::create([
            'society_id' => $this->societyId(),
            'user_id' => auth()->id(),
            'title' => $this->title,
            'body' => $this->body,
            'is_pinned' => $this->is_pinned,
            'published_at' => now(),
        ]);

        session()->flash('success', 'Notice published.');
        $this->reset(['title', 'body', 'is_pinned']);
    }

    public function delete(int $id): void
    {
        Notice::where('society_id', $this->societyId())->findOrFail($id)->delete();
    }

    public function render()
    {
        return $this->societyAdminLayout('Notice Board')
            ->with(['notices' => Notice::where('society_id', $this->societyId())->latest()->get()]);
    }
}
