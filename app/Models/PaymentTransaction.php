<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'society_id', 'maintenance_bill_id', 'flat_id', 'amount',
        'gateway', 'transaction_id', 'status', 'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(MaintenanceBill::class, 'maintenance_bill_id');
    }
}
