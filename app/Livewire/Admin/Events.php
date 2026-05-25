<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Event;
use Livewire\Component;

class Events extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $title = '';

    public string $description = '';

    public string $starts_at = '';

    public string $venue = '';

    public function save(): void
    {
        $this->validate([
            'title' => 'required|max:255',
            'starts_at' => 'required|date',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
        ]);

        Event::create([
            'society_id' => $this->societyId(),
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => $this->starts_at,
            'venue' => $this->venue,
            'is_published' => true,
        ]);

        session()->flash('success', 'Event created.');
        $this->reset(['title', 'description', 'starts_at', 'venue']);
    }

    public function delete(int $id): void
    {
        Event::where('society_id', $this->societyId())->findOrFail($id)->delete();
    }

    public function render()
    {
        return $this->societyAdminLayout('Events')
            ->with(['events' => Event::where('society_id', $this->societyId())->orderBy('starts_at')->get()]);
    }
}
