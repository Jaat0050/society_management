<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SosAlert extends Model
{
    protected $fillable = [
        'society_id', 'member_id', 'flat_id', 'contact_phone',
        'message', 'status', 'acknowledged_at',
    ];

    protected $casts = ['acknowledged_at' => 'datetime'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }
}
