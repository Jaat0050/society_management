<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'society_id', 'flat_id', 'user_id', 'name', 'email', 'phone',
        'member_type', 'is_primary', 'is_committee', 'move_in_date', 'move_out_date',
        'id_proof_type', 'id_proof_no',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_committee' => 'boolean',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function family(): HasMany
    {
        return $this->hasMany(MemberFamily::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(MemberVehicle::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MemberDocument::class);
    }
}
