<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocietyNotification extends Model
{
    protected $fillable = [
        'society_id', 'user_id', 'channel', 'recipient_group',
        'title', 'body', 'status', 'sent_at',
    ];

    protected $casts = ['sent_at' => 'datetime'];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }
}
