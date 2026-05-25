<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Member;
use App\Models\Society;
use App\Models\StaffMember;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MobileAppSeeder extends Seeder
{
    public function run(): void
    {
        $society = Society::where('email', 'info@greenvalley.test')->first() ?? Society::first();
        if (! $society) {
            return;
        }

        $users = [
            ['tenant@greenvalley.test', 'Demo Tenant', UserRole::Tenant->value, null],
            ['staff@greenvalley.test', 'Electric Ali Tech', UserRole::Staff->value, 'electrician'],
            ['committee@greenvalley.test', 'Committee Member', UserRole::Committee->value, null],
            ['accountant@greenvalley.test', 'Society Accountant', UserRole::Accountant->value, null],
            ['vendor@greenvalley.test', 'Lift AMC Vendor', UserRole::Vendor->value, null],
        ];

        foreach ($users as [$email, $name, $role, $staffRole]) {
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'phone' => '9800'.substr(md5($email), 0, 6),
                'password' => Hash::make('password'),
                'role' => $role,
                'society_id' => $society->id,
                'is_active' => true,
            ]);

            if ($role === UserRole::Tenant->value) {
                $flat = $society->flats()->where('status', 'occupied')->skip(1)->first();
                Member::create([
                    'society_id' => $society->id,
                    'flat_id' => $flat?->id,
                    'user_id' => $user->id,
                    'name' => $name,
                    'phone' => $user->phone,
                    'email' => $email,
                    'member_type' => 'tenant',
                ]);
            }

            if ($staffRole) {
                StaffMember::firstOrCreate(
                    ['society_id' => $society->id, 'email' => $email],
                    ['name' => $name, 'phone' => $user->phone, 'role' => $staffRole, 'is_active' => true]
                );
            }

            if ($role === UserRole::Vendor->value) {
                Vendor::firstOrCreate(
                    ['society_id' => $society->id, 'phone' => $user->phone],
                    ['name' => $name, 'email' => $email, 'service_type' => 'Lift AMC', 'is_active' => true]
                );
            }
        }

        $member = Member::where('society_id', $society->id)->where('is_primary', true)->first();
        if ($member && ! $member->user_id) {
            $resident = User::where('email', 'resident@greenvalley.test')->first();
            if ($resident) {
                $member->update(['user_id' => $resident->id]);
            }
        }

        $this->command?->info('Mobile app demo users created (password: password)');
    }
}
