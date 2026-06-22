<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmenityBooking extends Model
{
    protected $fillable = [
        'amenity_id', 'flat_id', 'user_id', 'booked_from', 'booked_to', 'status', 'notes',
    ];

    protected $casts = [
        'booked_from' => 'datetime',
        'booked_to' => 'datetime',
    ];

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }
}
