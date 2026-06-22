<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = [
        'society_id', 'name', 'company', 'phone', 'email', 'service_type',
        'contract_start', 'contract_end', 'contract_value', 'schedule_notes', 'is_active',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end' => 'date',
        'contract_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_vendor_id');
    }
}
