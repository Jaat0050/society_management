<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberVehicle extends Model
{
    protected $fillable = [
        'society_id', 'member_id', 'parking_slot_id', 'type',
        'vehicle_no', 'make_model', 'sticker_no', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function parkingSlot(): BelongsTo
    {
        return $this->belongsTo(ParkingSlot::class);
    }
}
