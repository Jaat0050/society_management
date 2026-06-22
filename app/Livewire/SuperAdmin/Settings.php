<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Concerns\WithPanelLayout;
use App\Models\AppSetting;
use Livewire\Component;

class Settings extends Component
{
    use WithPanelLayout;

    public string $app_name = '';

    public string $support_email = '';

    public string $support_phone = '';

    public string $currency = 'INR';

    public function mount(): void
    {
        $this->app_name = AppSetting::get('app_name', config('app.name'));
        $this->support_email = AppSetting::get('support_email', '');
        $this->support_phone = AppSetting::get('support_phone', '');
        $this->currency = AppSetting::get('currency', 'INR');
    }

    public function save(): void
    {
        AppSetting::set('app_name', $this->app_name);
        AppSetting::set('support_email', $this->support_email);
        AppSetting::set('support_phone', $this->support_phone);
        AppSetting::set('currency', $this->currency);

        session()->flash('success', 'App settings saved.');
    }

    public function render()
    {
        return $this->superAdminLayout('App Settings');
    }
}
