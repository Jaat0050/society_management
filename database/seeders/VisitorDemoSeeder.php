<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\DeliveryStaff;
use App\Models\Member;
use App\Models\Society;
use App\Models\User;
use App\Services\VisitorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VisitorDemoSeeder extends Seeder
{
    public function run(): void
    {
        $society = Society::where('email', 'info@greenvalley.test')->first()
            ?? Society::orderBy('id')->first();

        if (! $society) {
            $this->command?->warn('No society found. Run DummyDataSeeder first.');

            return;
        }

        if (! User::where('email', 'guard@greenvalley.test')->exists()) {
            User::create([
                'name' => 'Gate Guard Ramesh',
                'email' => 'guard@greenvalley.test',
                'phone' => '9898989898',
                'password' => Hash::make('password'),
                'role' => UserRole::Guard->value,
                'society_id' => $society->id,
                'is_active' => true,
            ]);
        }

        if (! DB::table('society_settings')->where('society_id', $society->id)->where('key', 'auto_approve_delivery')->exists()) {
            DB::table('society_settings')->insert([
                'society_id' => $society->id,
                'key' => 'auto_approve_delivery',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->seedResidentUser($society);

        if (DeliveryStaff::where('society_id', $society->id)->exists()) {
            $this->command?->info('Visitor demo data already present (resident ensured).');

            return;
        }

        $admin = User::where('society_id', $society->id)->where('role', UserRole::Admin->value)->first();
        $guard = User::where('email', 'guard@greenvalley.test')->first();
        $flat = $society->flats()->where('status', 'occupied')->first() ?? $society->flats()->first();
        $member = $flat ? Member::where('flat_id', $flat->id)->first() : null;

        $swiggy = DeliveryStaff::create([
            'society_id' => $society->id,
            'name' => 'Raj Delivery',
            'phone' => '9876500001',
            'company' => 'Swiggy',
            'vehicle_no' => 'MH-02-XY-1234',
            'is_verified' => true,
            'is_active' => true,
        ]);

        DeliveryStaff::create([
            'society_id' => $society->id,
            'name' => 'Amazon Rider',
            'phone' => '9876500002',
            'company' => 'Amazon',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $service = app(VisitorService::class);

        $service->createEntry([
            'society_id' => $society->id,
            'flat_id' => $flat?->id,
            'member_id' => $member?->id,
            'created_by' => $guard?->id,
            'visitor_name' => 'Amit Guest',
            'phone' => '9988776655',
            'visit_type' => 'guest',
            'person_count' => 2,
            'purpose' => 'Family visit',
        ]);

        $approved = $service->createEntry([
            'society_id' => $society->id,
            'flat_id' => $flat?->id,
            'member_id' => $member?->id,
            'created_by' => $guard?->id,
            'visitor_name' => 'Priya Sharma',
            'visit_type' => 'guest',
            'person_count' => 1,
        ]);
        $service->approve($approved, $admin?->id);

        $delivery = $service->createEntry([
            'society_id' => $society->id,
            'flat_id' => $flat?->id,
            'delivery_staff_id' => $swiggy->id,
            'created_by' => $guard?->id,
            'visitor_name' => $swiggy->name,
            'phone' => $swiggy->phone,
            'visit_type' => 'delivery',
            'person_count' => 1,
        ]);
        $service->checkIn($delivery);

        $this->command?->info('Visitor + resident demo data seeded for '.$society->name);
    }

    protected function seedResidentUser(Society $society): void
    {
        if (User::where('email', 'resident@greenvalley.test')->exists()) {
            return;
        }

        $member = Member::where('society_id', $society->id)->where('is_primary', true)->first()
            ?? Member::where('society_id', $society->id)->first();

        $resident = User::create([
            'name' => $member?->name ?? 'Demo Resident',
            'email' => 'resident@greenvalley.test',
            'phone' => '9876512345',
            'password' => Hash::make('password'),
            'role' => UserRole::Resident->value,
            'society_id' => $society->id,
            'is_active' => true,
        ]);

        if ($member) {
            $member->update(['user_id' => $resident->id]);
        }
    }
}
