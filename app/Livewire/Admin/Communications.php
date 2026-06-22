<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\BelongsToSociety;
use App\Livewire\Concerns\WithPanelLayout;
use App\Models\EmergencyAlert;
use App\Models\SocietyNotification;
use App\Services\NotificationService;
use Livewire\Component;

class Communications extends Component
{
    use BelongsToSociety, WithPanelLayout;

    public string $tab = 'notify';

    public string $channel = 'email';

    public string $recipient_group = 'all_residents';

    public string $title = '';

    public string $body = '';

    public string $alert_title = '';

    public string $alert_body = '';

    public string $severity = 'warning';

    public function sendNotification(): void
    {
        $data = $this->validate([
            'channel' => 'required|in:sms,email,push',
            'recipient_group' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $n = SocietyNotification::create([
            ...$data,
            'society_id' => $this->societyId(),
            'user_id' => auth()->id(),
        ]);

        app(NotificationService::class)->send($n);
        session()->flash('success', 'Notification queued and sent (logged).');
        $this->reset(['title', 'body']);
    }

    public function sendEmergency(): void
    {
        $data = $this->validate([
            'alert_title' => 'required|string|max:255',
            'alert_body' => 'required|string',
            'severity' => 'required|in:info,warning,critical',
        ]);

        $alert = EmergencyAlert::create([
            'society_id' => $this->societyId(),
            'created_by' => auth()->id(),
            'title' => $data['alert_title'],
            'body' => $data['alert_body'],
            'severity' => $data['severity'],
            'is_active' => true,
        ]);

        app(NotificationService::class)->broadcastEmergency($alert);
        session()->flash('success', 'Emergency alert broadcast.');
        $this->reset(['alert_title', 'alert_body']);
    }

    public function render()
    {
        $sid = $this->societyId();

        return $this->societyAdminLayout('Communication')
            ->with([
                'notifications' => SocietyNotification::where('society_id', $sid)->latest()->take(15)->get(),
                'alerts' => EmergencyAlert::where('society_id', $sid)->latest()->take(10)->get(),
            ]);
    }
}
