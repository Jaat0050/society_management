<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Settings extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public bool $visitor_approval_required = true;

    public bool $auto_approve_domestic_help = false;

    public bool $auto_approve_delivery = false;

    public string $maintenance_due_day = '10';

    public string $late_fee_percent = '2';

    public string $payment_gateway = 'razorpay';

    public function mount(): void
    {
        $settings = DB::table('society_settings')->where('society_id', $this->societyId())->pluck('value', 'key');

        $this->visitor_approval_required = ($settings['visitor_approval_required'] ?? '1') === '1';
        $this->auto_approve_domestic_help = ($settings['auto_approve_domestic_help'] ?? '0') === '1';
        $this->auto_approve_delivery = ($settings['auto_approve_delivery'] ?? '0') === '1';
        $this->maintenance_due_day = $settings['maintenance_due_day'] ?? '10';
        $this->late_fee_percent = $settings['late_fee_percent'] ?? '2';
        $this->payment_gateway = $settings['payment_gateway'] ?? 'razorpay';
    }

    public function save(): void
    {
        $sid = $this->societyId();
        $pairs = [
            'visitor_approval_required' => $this->visitor_approval_required ? '1' : '0',
            'auto_approve_domestic_help' => $this->auto_approve_domestic_help ? '1' : '0',
            'auto_approve_delivery' => $this->auto_approve_delivery ? '1' : '0',
            'maintenance_due_day' => $this->maintenance_due_day,
            'late_fee_percent' => $this->late_fee_percent,
            'payment_gateway' => $this->payment_gateway,
        ];

        foreach ($pairs as $key => $value) {
            DB::table('society_settings')->updateOrInsert(
                ['society_id' => $sid, 'key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        session()->flash('success', 'Society settings saved.');
    }

    public function render()
    {
        return $this->societyAdminLayout('Society Settings');
    }
}
