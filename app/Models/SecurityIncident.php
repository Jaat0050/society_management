<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityIncident extends Model
{
    protected $fillable = [
        'society_id', 'reported_by', 'title', 'description', 'severity',
        'status', 'location', 'occurred_at', 'resolution',
    ];

    protected $casts = ['occurred_at' => 'datetime'];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
