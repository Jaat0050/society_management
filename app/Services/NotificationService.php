<?php

namespace App\Services;

use App\Models\EmergencyAlert;
use App\Models\Member;
use App\Models\SocietyNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /** Log + simulate send (wire real SMS/email providers later). */
    public function send(SocietyNotification $notification): SocietyNotification
    {
        Log::info('Society notification', [
            'channel' => $notification->channel,
            'title' => $notification->title,
            'group' => $notification->recipient_group,
        ]);

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return $notification;
    }

    public function broadcastEmergency(EmergencyAlert $alert): void
    {
        $members = Member::where('society_id', $alert->society_id)->whereNotNull('email')->get();

        foreach ($members->take(50) as $member) {
            $n = SocietyNotification::create([
                'society_id' => $alert->society_id,
                'channel' => 'email',
                'recipient_group' => 'emergency',
                'title' => '[EMERGENCY] '.$alert->title,
                'body' => $alert->body,
                'status' => 'queued',
            ]);
            $this->send($n);
        }
    }

    public function sendBillReminder(int $societyId, string $title, string $body): SocietyNotification
    {
        $n = SocietyNotification::create([
            'society_id' => $societyId,
            'channel' => 'sms',
            'recipient_group' => 'pending_bills',
            'title' => $title,
            'body' => $body,
            'status' => 'queued',
        ]);

        return $this->send($n);
    }
}
