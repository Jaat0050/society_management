<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceBill extends Model
{
    protected $fillable = [
        'society_id', 'flat_id', 'title', 'bill_month', 'amount', 'penalty_amount',
        'paid_amount', 'due_date', 'status', 'paid_at', 'payment_mode', 'transaction_ref', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    public function totalDue(): float
    {
        return (float) $this->amount + (float) $this->penalty_amount;
    }

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }
}
