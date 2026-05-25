<?php

namespace App\Livewire\Concerns;

use App\Support\PanelMenu;
use Illuminate\Support\Str;

trait WithPanelLayout
{
    protected function superAdminLayout(string $header): mixed
    {
        return view($this->getViewName())
            ->layout('layouts.panel', [
                'title' => $header,
                'header' => $header,
                'panelLabel' => 'Super Admin',
                'menuItems' => PanelMenu::superAdmin(),
            ]);
    }

    protected function societyAdminLayout(string $header): mixed
    {
        return view($this->getViewName())
            ->layout('layouts.panel', [
                'title' => $header,
                'header' => $header,
                'panelLabel' => auth()->user()->society?->name ?? 'Society Admin',
                'menuItems' => PanelMenu::societyAdmin(),
            ]);
    }

    protected function getViewName(): string
    {
        $class = preg_replace('/^App\\\\Livewire\\\\/', '', static::class);
        $segments = array_map(fn (string $segment) => Str::kebab($segment), explode('\\', $class));
        $name = implode('.', $segments);

        return 'livewire.'.$name;
    }
}
