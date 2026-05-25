<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Society extends Model
{
    protected $fillable = [
        'name', 'registration_no', 'address', 'city', 'state',
        'pincode', 'phone', 'email', 'total_flats', 'is_active',
        'subscription_plan_id', 'subscription_status', 'subscription_ends_at',
        'blocked_at', 'block_reason',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'date',
        'blocked_at' => 'datetime',
    ];

    public function isBlocked(): bool
    {
        return $this->blocked_at !== null || ! $this->is_active;
    }

    public function subscriptionPlan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(StaffMember::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function parkingSlots(): HasMany
    {
        return $this->hasMany(ParkingSlot::class);
    }

    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class);
    }

    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function maintenanceBills(): HasMany
    {
        return $this->hasMany(MaintenanceBill::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
