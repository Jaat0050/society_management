<?php

namespace App\Support;

use App\Models\VisitorEntry;

class VisitorPassPresenter
{
    public static function toArray(VisitorEntry $entry): array
    {
        $entry->loadMissing(['flat', 'member']);

        return [
            'id' => $entry->id,
            'visitor_name' => $entry->visitor_name,
            'phone' => $entry->phone,
            'visit_type' => $entry->visit_type,
            'person_count' => $entry->person_count,
            'purpose' => $entry->purpose,
            'pass_code' => $entry->pass_code,
            'qr_token' => $entry->qr_token,
            'approval_url' => $entry->approvalUrl(),
            'qr_image_url' => url('/visit/qr/'.$entry->qr_token),
            'approval_otp' => $entry->approval_otp,
            'otp_expires_at' => $entry->otp_expires_at?->toIso8601String(),
            'status' => $entry->status,
            'status_label' => $entry->statusLabel(),
            'flat' => $entry->flat?->label ?? null,
            'valid_today' => true,
        ];
    }
}
