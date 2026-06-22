<?php

namespace App\Livewire\Concerns;

use App\Support\PanelMenu;

trait WithGuardLayout
{
    protected function guardLayout(string $header): mixed
    {
        $class = preg_replace('/^App\\\\Livewire\\\\/', '', static::class);
        $segments = array_map(fn (string $s) => \Illuminate\Support\Str::kebab($s), explode('\\', $class));
        $view = 'livewire.'.implode('.', $segments);

        return view($view)
            ->layout('layouts.guard', [
                'title' => $header,
                'header' => $header,
                'menuItems' => PanelMenu::guard(),
            ]);
    }
}
