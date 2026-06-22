<?php

namespace App\Livewire\Concerns;

use App\Support\PanelMenu;

trait WithResidentLayout
{
    protected function residentLayout(string $header): mixed
    {
        return view($this->getViewName())
            ->layout('layouts.panel', [
                'title' => $header,
                'header' => $header,
                'panelLabel' => auth()->user()->society?->name ?? 'Resident',
                'menuItems' => PanelMenu::resident(),
            ]);
    }

    protected function getViewName(): string
    {
        $class = preg_replace('/^App\\\\Livewire\\\\/', '', static::class);
        $segments = array_map(fn (string $s) => \Illuminate\Support\Str::kebab($s), explode('\\', $class));

        return 'livewire.'.implode('.', $segments);
    }
}
