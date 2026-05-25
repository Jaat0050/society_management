<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\AppSetting;
use App\Models\Block;
use App\Models\Complaint;
use App\Models\DeliveryStaff;
use App\Models\Event;
use App\Models\Flat;
use App\Models\GlobalNotification;
use App\Models\MaintenanceBill;
use App\Models\Member;
use App\Models\Notice;
use App\Models\ParkingSlot;
use App\Models\Society;
use App\Models\StaffMember;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\VisitorService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Subscription Plans ──────────────────────────────────────
        $freePlan = SubscriptionPlan::create([
            'name' => 'Free Trial',
            'slug' => 'free-trial',
            'price' => 0,
            'max_flats' => 50,
            'max_users' => 200,
            'duration_months' => 1,
            'is_active' => true,
            'features' => 'Basic dashboard, 50 flats',
        ]);

        $standardPlan = SubscriptionPlan::create([
            'name' => 'Standard',
            'slug' => 'standard',
            'price' => 4999,
            'max_flats' => 200,
            'max_users' => 1000,
            'duration_months' => 12,
            'is_active' => true,
            'features' => 'All modules, reports, 200 flats',
        ]);

        $premiumPlan = SubscriptionPlan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 9999,
            'max_flats' => 500,
            'max_users' => 5000,
            'duration_months' => 12,
            'is_active' => true,
            'features' => 'Unlimited modules, priority support',
        ]);

        // ── Super Admin ─────────────────────────────────────────────
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@society.test',
            'phone' => '9999999999',
            'password' => Hash::make('password'),
            'role' => UserRole::SuperAdmin->value,
            'is_active' => true,
        ]);

        // ── App Settings ────────────────────────────────────────────
        AppSetting::insert([
            ['key' => 'app_name', 'value' => 'Society Manager', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'support_email', 'value' => 'support@society.test', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'support_phone', 'value' => '1800-123-456', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'INR', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
        ]);

        GlobalNotification::create([
            'user_id' => $superAdmin->id,
            'title' => 'Welcome to Society Manager',
            'body' => 'Platform is live. All society admins can now manage their societies from the admin panel.',
            'sent_at' => now()->subDays(2),
        ]);

        // ── Societies ─────────────────────────────────────────────────
        $societiesData = [
            [
                'name' => 'Green Valley Residency',
                'registration_no' => 'SOC-MH-2024-001',
                'address' => '123 Main Road, Andheri West',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400053',
                'phone' => '02226789012',
                'email' => 'info@greenvalley.test',
                'plan' => $standardPlan,
                'status' => 'active',
                'admin_email' => 'admin@greenvalley.test',
            ],
            [
                'name' => 'Sunrise Apartments',
                'registration_no' => 'SOC-DL-2024-002',
                'address' => '45 Ring Road, Dwarka Sector 10',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'pincode' => '110075',
                'phone' => '01145678901',
                'email' => 'contact@sunrise.test',
                'plan' => $freePlan,
                'status' => 'trial',
                'admin_email' => 'admin@sunrise.test',
            ],
            [
                'name' => 'Palm Grove Society',
                'registration_no' => 'SOC-KA-2024-003',
                'address' => '78 Whitefield Main Road',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'pincode' => '560066',
                'phone' => '08023456789',
                'email' => 'admin@palmgrove.test',
                'plan' => $premiumPlan,
                'status' => 'active',
                'admin_email' => 'admin@palmgrove.test',
            ],
        ];

        $residentNames = [
            ['Rahul Sharma', 'rahul.sharma@email.com', '9876543210', 'owner'],
            ['Priya Patel', 'priya.patel@email.com', '9876543211', 'owner'],
            ['Amit Kumar', 'amit.kumar@email.com', '9876543212', 'tenant'],
            ['Sneha Reddy', 'sneha.reddy@email.com', '9876543213', 'owner'],
            ['Vikram Singh', 'vikram.singh@email.com', '9876543214', 'tenant'],
            ['Anita Desai', 'anita.desai@email.com', '9876543215', 'family'],
            ['Rajesh Mehta', 'rajesh.mehta@email.com', '9876543216', 'owner'],
            ['Kavita Nair', 'kavita.nair@email.com', '9876543217', 'tenant'],
        ];

        $staffRoles = ['guard', 'guard', 'cleaner', 'manager', 'electrician', 'plumber'];
        $staffNames = ['Ramesh Guard', 'Suresh Security', 'Lakshmi Cleaner', 'Manager Rao', 'Electric Ali', 'Plumber Khan'];

        $complaintSubjects = [
            ['Water leakage in bathroom', 'maintenance', 'high'],
            ['Lift not working', 'maintenance', 'high'],
            ['Parking slot occupied', 'parking', 'medium'],
            ['Noise from flat above', 'noise', 'low'],
            ['Street light not working', 'maintenance', 'medium'],
            ['Visitor gate pass issue', 'security', 'low'],
        ];

        $noticeTitles = [
            ['AGM Meeting on 15th', 'All members are requested to attend the Annual General Meeting at the clubhouse at 6 PM.'],
            ['Water Supply Timing', 'Water supply will be available from 6 AM to 9 AM and 5 PM to 8 PM daily.'],
            ['Festival Decoration', 'Society will be decorated for Diwali. Contribution of ₹500 per flat requested.'],
            ['Parking Rules Updated', 'Please ensure vehicles are parked only in allotted slots. Visitor parking is at Gate B.'],
        ];

        foreach ($societiesData as $idx => $socData) {
            $society = Society::create([
                'name' => $socData['name'],
                'registration_no' => $socData['registration_no'],
                'address' => $socData['address'],
                'city' => $socData['city'],
                'state' => $socData['state'],
                'pincode' => $socData['pincode'],
                'phone' => $socData['phone'],
                'email' => $socData['email'],
                'total_flats' => 0,
                'is_active' => true,
                'subscription_plan_id' => $socData['plan']->id,
                'subscription_status' => $socData['status'],
                'subscription_ends_at' => now()->addMonths($socData['plan']->duration_months),
            ]);

            // Society admin user
            $admin = User::create([
                'name' => $socData['name'].' Admin',
                'email' => $socData['admin_email'],
                'phone' => '980000000'.$idx,
                'password' => Hash::make('password'),
                'role' => UserRole::Admin->value,
                'society_id' => $society->id,
                'is_active' => true,
            ]);

            // Society settings
            DB::table('society_settings')->insert([
                ['society_id' => $society->id, 'key' => 'visitor_approval_required', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
                ['society_id' => $society->id, 'key' => 'auto_approve_domestic_help', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
                ['society_id' => $society->id, 'key' => 'auto_approve_delivery', 'value' => $idx === 0 ? '1' : '0', 'created_at' => now(), 'updated_at' => now()],
                ['society_id' => $society->id, 'key' => 'maintenance_due_day', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ]);

            // Blocks
            $blocks = [];
            foreach (['Tower A', 'Tower B', 'Tower C'] as $bIdx => $blockName) {
                if ($idx === 1 && $bIdx === 2) {
                    break;
                }
                $blocks[] = Block::create([
                    'society_id' => $society->id,
                    'name' => $blockName,
                    'total_floors' => rand(5, 12),
                    'description' => $blockName.' - '.$society->name,
                ]);
            }

            // Flats
            $flats = [];
            $flatCounter = 0;
            foreach ($blocks as $block) {
                for ($floor = 1; $floor <= min(5, $block->total_floors); $floor++) {
                    for ($unit = 1; $unit <= 2; $unit++) {
                        $flatNo = $floor.str_pad((string) $unit, 2, '0', STR_PAD_LEFT);
                        $flats[] = Flat::create([
                            'society_id' => $society->id,
                            'block_id' => $block->id,
                            'block' => $block->name,
                            'floor' => (string) $floor,
                            'flat_no' => $flatNo,
                            'type' => 'residential',
                            'area_sqft' => rand(650, 1500),
                            'status' => $flatCounter % 4 === 0 ? 'vacant' : 'occupied',
                        ]);
                        $flatCounter++;
                    }
                }
            }

            $society->update(['total_flats' => count($flats)]);

            // Residents (members)
            $occupiedFlats = collect($flats)->where('status', 'occupied')->values();
            foreach ($residentNames as $rIdx => $resident) {
                if (! isset($occupiedFlats[$rIdx])) {
                    break;
                }
                Member::create([
                    'society_id' => $society->id,
                    'flat_id' => $occupiedFlats[$rIdx]->id,
                    'name' => $resident[0],
                    'email' => $resident[1],
                    'phone' => $resident[2],
                    'member_type' => $resident[3],
                    'is_primary' => $rIdx === 0,
                    'is_committee' => $rIdx < 2,
                    'move_in_date' => now()->subMonths(rand(6, 36)),
                ]);
            }

            // Staff
            foreach ($staffNames as $sIdx => $staffName) {
                StaffMember::create([
                    'society_id' => $society->id,
                    'name' => $staffName,
                    'phone' => '91000000'.str_pad((string) ($idx * 10 + $sIdx), 2, '0', STR_PAD_LEFT),
                    'email' => strtolower(str_replace(' ', '.', $staffName)).'@staff.test',
                    'role' => $staffRoles[$sIdx],
                    'is_active' => true,
                    'joined_at' => now()->subMonths(rand(3, 24)),
                ]);
            }

            // Maintenance bills
            $months = [now()->format('Y-m'), now()->subMonth()->format('Y-m')];
            $billStatuses = ['pending', 'paid', 'paid', 'pending', 'overdue'];
            foreach ($flats as $fIdx => $flat) {
                foreach ($months as $mIdx => $month) {
                    $status = $billStatuses[($fIdx + $mIdx) % count($billStatuses)];
                    MaintenanceBill::create([
                        'society_id' => $society->id,
                        'flat_id' => $flat->id,
                        'title' => 'Monthly Maintenance - '.$month,
                        'bill_month' => $month,
                        'amount' => rand(1500, 4500),
                        'due_date' => Carbon::parse($month.'-10'),
                        'status' => $status,
                        'paid_at' => $status === 'paid' ? Carbon::parse($month.'-08') : null,
                    ]);
                }
            }

            // Notices
            foreach ($noticeTitles as $nIdx => $notice) {
                Notice::create([
                    'society_id' => $society->id,
                    'user_id' => $admin->id,
                    'title' => $notice[0],
                    'body' => $notice[1],
                    'is_pinned' => $nIdx === 0,
                    'published_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Complaints
            $statuses = ['open', 'in_progress', 'resolved', 'open', 'closed'];
            foreach ($complaintSubjects as $cIdx => $complaint) {
                Complaint::create([
                    'society_id' => $society->id,
                    'flat_id' => $occupiedFlats[$cIdx % $occupiedFlats->count()]->id ?? null,
                    'user_id' => null,
                    'subject' => $complaint[0],
                    'description' => 'Detailed complaint regarding: '.$complaint[0].'. Please resolve at earliest.',
                    'category' => $complaint[1],
                    'priority' => $complaint[2],
                    'status' => $statuses[$cIdx % count($statuses)],
                    'resolution_notes' => $statuses[$cIdx % count($statuses)] === 'resolved' ? 'Issue fixed by maintenance team.' : null,
                    'resolved_at' => $statuses[$cIdx % count($statuses)] === 'resolved' ? now()->subDays(2) : null,
                ]);
            }

            // Events
            Event::insert([
                [
                    'society_id' => $society->id,
                    'title' => 'Holi Celebration',
                    'description' => 'Community Holi event at central garden.',
                    'starts_at' => now()->addDays(15),
                    'ends_at' => now()->addDays(15)->addHours(4),
                    'venue' => 'Central Garden',
                    'is_published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'society_id' => $society->id,
                    'title' => 'Yoga Session',
                    'description' => 'Morning yoga for all residents.',
                    'starts_at' => now()->addDays(3)->setTime(6, 0),
                    'ends_at' => now()->addDays(3)->setTime(7, 30),
                    'venue' => 'Clubhouse',
                    'is_published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // Parking
            for ($p = 1; $p <= 8; $p++) {
                $assignedFlat = $p <= 5 ? $occupiedFlats[$p - 1] ?? null : null;
                ParkingSlot::create([
                    'society_id' => $society->id,
                    'flat_id' => $assignedFlat?->id,
                    'slot_no' => 'P-'.str_pad((string) $p, 2, '0', STR_PAD_LEFT),
                    'type' => $p <= 6 ? 'car' : ($p === 7 ? 'bike' : 'visitor'),
                    'status' => $assignedFlat ? 'occupied' : 'available',
                    'vehicle_no' => $assignedFlat ? 'MH-01-AB-'.str_pad((string) $p, 4, '0', STR_PAD_LEFT) : null,
                ]);
            }

            // Amenities & bookings
            $clubhouse = Amenity::create([
                'society_id' => $society->id,
                'name' => 'Clubhouse',
                'description' => 'Community hall for events',
                'capacity' => 50,
                'hourly_rate' => 500,
                'is_active' => true,
            ]);

            $gym = Amenity::create([
                'society_id' => $society->id,
                'name' => 'Gym',
                'description' => 'Fitness center',
                'capacity' => 15,
                'hourly_rate' => 0,
                'is_active' => true,
            ]);

            if ($idx === 0) {
                $guardUser = User::create([
                    'name' => 'Gate Guard Ramesh',
                    'email' => 'guard@greenvalley.test',
                    'phone' => '9898989898',
                    'password' => Hash::make('password'),
                    'role' => UserRole::Guard->value,
                    'society_id' => $society->id,
                    'is_active' => true,
                ]);

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

                $visitorService = app(VisitorService::class);
                $sampleFlat = $occupiedFlats[0] ?? $flats[0];
                $sampleMember = Member::where('society_id', $society->id)->where('flat_id', $sampleFlat->id)->first();

                $pending = $visitorService->createEntry([
                    'society_id' => $society->id,
                    'flat_id' => $sampleFlat->id,
                    'member_id' => $sampleMember?->id,
                    'created_by' => $guardUser->id,
                    'visitor_name' => 'Amit Guest',
                    'phone' => '9988776655',
                    'visit_type' => 'guest',
                    'person_count' => 2,
                    'purpose' => 'Family visit',
                ]);

                $approved = $visitorService->createEntry([
                    'society_id' => $society->id,
                    'flat_id' => $occupiedFlats[1]->id ?? $sampleFlat->id,
                    'member_id' => $sampleMember?->id,
                    'created_by' => $guardUser->id,
                    'visitor_name' => 'Priya Sharma',
                    'phone' => '9988776656',
                    'visit_type' => 'guest',
                    'person_count' => 1,
                    'purpose' => 'Meeting',
                ]);
                $visitorService->approve($approved, $admin->id);

                $deliveryEntry = $visitorService->createEntry([
                    'society_id' => $society->id,
                    'flat_id' => $sampleFlat->id,
                    'delivery_staff_id' => $swiggy->id,
                    'created_by' => $guardUser->id,
                    'visitor_name' => $swiggy->name,
                    'phone' => $swiggy->phone,
                    'visit_type' => 'delivery',
                    'person_count' => 1,
                    'purpose' => 'Food delivery',
                ]);
                $visitorService->checkIn($deliveryEntry);

                $inside = $visitorService->createEntry([
                    'society_id' => $society->id,
                    'flat_id' => $occupiedFlats[2]->id ?? $sampleFlat->id,
                    'created_by' => $guardUser->id,
                    'visitor_name' => 'Cab Driver',
                    'visit_type' => 'cab',
                    'vehicle_no' => 'MH-01-CD-9999',
                    'person_count' => 1,
                ]);
                $visitorService->approve($inside, $admin->id);
                $visitorService->checkIn($inside);
            }

            if ($occupiedFlats->isNotEmpty()) {
                AmenityBooking::create([
                    'amenity_id' => $clubhouse->id,
                    'flat_id' => $occupiedFlats[0]->id,
                    'user_id' => $admin->id,
                    'booked_from' => now()->addDays(5)->setTime(18, 0),
                    'booked_to' => now()->addDays(5)->setTime(21, 0),
                    'status' => 'pending',
                    'notes' => 'Birthday party booking',
                ]);

                AmenityBooking::create([
                    'amenity_id' => $gym->id,
                    'flat_id' => $occupiedFlats[1]->id ?? $occupiedFlats[0]->id,
                    'user_id' => null,
                    'booked_from' => now()->addDays(1)->setTime(7, 0),
                    'booked_to' => now()->addDays(1)->setTime(8, 0),
                    'status' => 'approved',
                    'notes' => null,
                ]);
            }
        }

        // One blocked society for demo
        Society::create([
            'name' => 'Blocked Demo Society',
            'registration_no' => 'SOC-BLK-001',
            'address' => '99 Test Lane',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'pincode' => '411001',
            'phone' => '02012345678',
            'email' => 'blocked@demo.test',
            'is_active' => false,
            'subscription_plan_id' => $freePlan->id,
            'subscription_status' => 'expired',
            'blocked_at' => now()->subDays(5),
            'block_reason' => 'Payment overdue - blocked by Super Admin',
        ]);
    }
}
