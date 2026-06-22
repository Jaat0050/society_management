<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CctvCamera extends Model
{
    protected $fillable = ['society_id', 'name', 'location', 'stream_url', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }
}
