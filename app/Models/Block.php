<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    protected $fillable = ['society_id', 'name', 'total_floors', 'description'];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }
}
