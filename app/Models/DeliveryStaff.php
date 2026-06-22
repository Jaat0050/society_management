<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryStaff extends Model
{
    protected $table = 'delivery_staff';

    protected $fillable = [
        'society_id', 'name', 'phone', 'company', 'vehicle_no',
        'id_card_no', 'is_verified', 'is_active',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function visitorEntries(): HasMany
    {
        return $this->hasMany(VisitorEntry::class);
    }
}
