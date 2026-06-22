<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorEntry extends Model
{
    protected $fillable = [
        'society_id', 'flat_id', 'member_id', 'delivery_staff_id',
        'created_by', 'approved_by', 'pass_code', 'qr_token', 'approval_otp', 'otp_expires_at',
        'visitor_name', 'phone', 'id_proof', 'visit_type',
        'vehicle_no', 'person_count', 'purpose', 'status',
        'rejection_reason', 'approved_at', 'entry_at', 'exit_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'entry_at' => 'datetime',
        'exit_at' => 'datetime',
        'person_count' => 'integer',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function deliveryStaff(): BelongsTo
    {
        return $this->belongsTo(DeliveryStaff::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isInside(): bool
    {
        return $this->status === 'checked_in';
    }

    public function canCheckIn(): bool
    {
        return in_array($this->status, ['approved', 'pending']) && $this->status !== 'rejected';
    }

    public function approvalUrl(): string
    {
        return route('visit.approve', $this->qr_token);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'checked_in' => 'Inside',
            'checked_out' => 'Exited',
            default => $this->status,
        };
    }
}
