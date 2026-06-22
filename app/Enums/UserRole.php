<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Resident = 'resident';
    case Guard = 'guard';
    case Tenant = 'tenant';
    case Staff = 'staff';
    case Committee = 'committee';
    case Accountant = 'accountant';
    case Vendor = 'vendor';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Society Admin',
            self::Resident => 'Resident / Owner',
            self::Tenant => 'Tenant',
            self::Guard => 'Security Guard',
            self::Staff => 'Maintenance Staff',
            self::Committee => 'Committee Member',
            self::Accountant => 'Accountant',
            self::Vendor => 'Vendor',
        };
    }

    public function isMobileRole(): bool
    {
        return in_array($this, [
            self::Resident, self::Tenant, self::Guard, self::Staff,
            self::Committee, self::Accountant, self::Vendor,
        ]);
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function isSocietyAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isGuard(): bool
    {
        return $this === self::Guard;
    }

    public function panelRoute(): string
    {
        return match ($this) {
            self::SuperAdmin => 'super-admin.dashboard',
            self::Admin => 'admin.dashboard',
            self::Guard => 'guard.dashboard',
            self::Resident => 'resident.dashboard',
            default => 'login',
        };
    }
}
