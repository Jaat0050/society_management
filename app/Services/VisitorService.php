<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Society;
use App\Models\VisitorEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitorService
{
    public function societyRequiresApproval(int $societyId): bool
    {
        $value = DB::table('society_settings')
            ->where('society_id', $societyId)
            ->where('key', 'visitor_approval_required')
            ->value('value');

        return ($value ?? '1') === '1';
    }

    public function autoApproveDelivery(int $societyId): bool
    {
        $value = DB::table('society_settings')
            ->where('society_id', $societyId)
            ->where('key', 'auto_approve_delivery')
            ->value('value');

        return ($value ?? '0') === '1';
    }

    public function generatePassCode(): string
    {
        do {
            $code = strtoupper(Str::random(4)).rand(1000, 9999);
        } while (VisitorEntry::where('pass_code', $code)->exists());

        return $code;
    }

    public function generateQrToken(): string
    {
        do {
            $token = Str::random(48);
        } while (VisitorEntry::where('qr_token', $token)->exists());

        return $token;
    }

    public function initialStatus(int $societyId, string $visitType): string
    {
        if ($visitType === 'delivery' && $this->autoApproveDelivery($societyId)) {
            return 'approved';
        }

        if (! $this->societyRequiresApproval($societyId)) {
            return 'approved';
        }

        return 'pending';
    }

    public function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function createEntry(array $data): VisitorEntry
    {
        $status = $this->initialStatus($data['society_id'], $data['visit_type']);
        $needsOtp = $status === 'pending' && $this->societyRequiresApproval($data['society_id']);

        $entry = VisitorEntry::create([
            ...$data,
            'pass_code' => $this->generatePassCode(),
            'qr_token' => $this->generateQrToken(),
            'approval_otp' => $needsOtp ? $this->generateOtp() : null,
            'otp_expires_at' => $needsOtp ? now()->addHours(24) : null,
            'status' => $status,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);

        return $entry;
    }

    public function verifyOtp(VisitorEntry $entry, string $otp): bool
    {
        if (! $entry->approval_otp || $entry->otp_expires_at?->isPast()) {
            return false;
        }

        return $entry->approval_otp === trim($otp);
    }

    public function approveByOtp(VisitorEntry $entry, string $otp, ?int $userId = null): bool
    {
        if (! $this->verifyOtp($entry, $otp)) {
            return false;
        }

        $this->approve($entry, $userId);

        return true;
    }

    public function approve(VisitorEntry $entry, ?int $userId = null): VisitorEntry
    {
        $entry->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return $entry->fresh();
    }

    public function reject(VisitorEntry $entry, ?string $reason = null, ?int $userId = null): VisitorEntry
    {
        $entry->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'rejection_reason' => $reason,
        ]);

        return $entry->fresh();
    }

    public function checkIn(VisitorEntry $entry): VisitorEntry
    {
        if (in_array($entry->status, ['rejected', 'checked_in', 'checked_out'])) {
            throw new \RuntimeException('Visitor cannot check in with status: '.$entry->status);
        }

        if ($entry->status === 'pending' && $this->societyRequiresApproval($entry->society_id)) {
            throw new \RuntimeException('Visitor approval is required before entry.');
        }

        $entry->update([
            'status' => 'checked_in',
            'entry_at' => now(),
            'approved_at' => $entry->approved_at ?? now(),
        ]);

        return $entry->fresh();
    }

    public function checkOut(VisitorEntry $entry): VisitorEntry
    {
        $entry->update([
            'status' => 'checked_out',
            'exit_at' => now(),
        ]);

        return $entry->fresh();
    }

    public function findByTokenOrPass(?string $token, ?string $passCode): ?VisitorEntry
    {
        if ($token) {
            return VisitorEntry::where('qr_token', $token)->first();
        }

        if ($passCode) {
            return VisitorEntry::where('pass_code', strtoupper($passCode))->first();
        }

        return null;
    }
}
