<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'society_id', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    public function roleEnum(): UserRole
    {
        return UserRole::from($this->role);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin->value;
    }

    public function isSocietyAdmin(): bool
    {
        return $this->role === UserRole::Admin->value;
    }

    public function isGuard(): bool
    {
        return $this->role === UserRole::Guard->value;
    }

    public function isResident(): bool
    {
        return $this->role === UserRole::Resident->value;
    }

    public function member(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function canAccessPanel(): bool
    {
        return $this->is_active && (
            $this->isSuperAdmin()
            || $this->isSocietyAdmin()
            || $this->isGuard()
            || $this->isResident()
        );
    }
}
