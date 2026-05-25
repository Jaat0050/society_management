<?php

namespace App\Livewire\Concerns;

trait BelongsToSociety
{
    public function societyId(): int
    {
        return (int) auth()->user()->society_id;
    }
}
