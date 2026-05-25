<?php

namespace App\Livewire\Resident;

use App\Livewire\Concerns\WithResidentLayout;
use App\Models\Event;
use App\Models\Notice;
use Livewire\Component;

class Notices extends Component
{
    use WithResidentLayout;

    public function render()
    {
        $sid = auth()->user()->society_id;

        return $this->residentLayout('Notices & Events')
            ->with([
                'notices' => Notice::where('society_id', $sid)->whereNotNull('published_at')->latest('published_at')->get(),
                'events' => Event::where('society_id', $sid)->where('is_published', true)->orderBy('starts_at')->get(),
            ]);
    }
}
