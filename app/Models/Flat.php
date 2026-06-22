<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flat extends Model
{
    protected $fillable = [
        'society_id', 'block_id', 'block', 'floor', 'flat_no', 'type', 'area_sqft', 'status',
    ];

    public function blockRelation(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    protected $casts = [
        'area_sqft' => 'decimal:2',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function maintenanceBills(): HasMany
    {
        return $this->hasMany(MaintenanceBill::class);
    }

    public function getLabelAttribute(): string
    {
        $parts = array_filter([$this->block, $this->flat_no]);

        return implode('-', $parts) ?: $this->flat_no;
    }
}
