<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Concerns\WithPanelLayout;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\Block;
use App\Models\CctvCamera;
use App\Models\Complaint;
use App\Models\DeliveryStaff;
use App\Models\EmergencyAlert;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Flat;
use App\Models\LedgerEntry;
use App\Models\MaintenanceBill;
use App\Models\Member;
use App\Models\MemberVehicle;
use App\Models\Notice;
use App\Models\ParkingPass;
use App\Models\ParkingSlot;
use App\Models\PaymentTransaction;
use App\Models\SecurityIncident;
use App\Models\Society;
use App\Models\SocietyNotification;
use App\Models\SosAlert;
use App\Models\StaffAttendance;
use App\Models\StaffMember;
use App\Models\StaffPayment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VisitorEntry;
use Livewire\Component;
use Livewire\WithPagination;

class PlatformPreview extends Component
{
    use WithPanelLayout, WithPagination;

    public ?int $societyId = null;

    public string $tab = 'overview';

    public string $search = '';

    protected $queryString = [
        'societyId' => ['except' => null],
        'tab' => ['except' => 'overview'],
    ];

    public function mount(?int $societyId = null): void
    {
        if ($societyId) {
            $this->societyId = $societyId;
            $this->tab = 'overview';
        }
    }

    public function updatingSocietyId($value): void
    {
        $this->resetPage();
        $this->tab = $value ? 'overview' : 'members';
    }

    public function updatingTab(): void
    {
        $this->resetPage();
        $this->search = '';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
        $this->search = '';
    }

    protected function societyScope($query)
    {
        return $query->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId));
    }

    protected function buildCounts(): array
    {
        $sid = $this->societyId;

        return [
            'overview' => 0,
            'members' => Member::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'flats' => Flat::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'blocks' => Block::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'vehicles' => MemberVehicle::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'staff' => StaffMember::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'attendance' => StaffAttendance::when($sid, fn ($q) => $q->whereHas('staff', fn ($s) => $s->where('society_id', $sid)))->count(),
            'salaries' => StaffPayment::when($sid, fn ($q) => $q->whereHas('staff', fn ($s) => $s->where('society_id', $sid)))->count(),
            'vendors' => Vendor::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'admins' => User::where('role', 'admin')->when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'guards' => User::where('role', 'guard')->when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'residents_users' => User::where('role', 'resident')->when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'visitors' => VisitorEntry::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'delivery' => DeliveryStaff::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'complaints' => Complaint::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'notices' => Notice::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'events' => Event::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'notifications' => SocietyNotification::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'emergency' => EmergencyAlert::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'bills' => MaintenanceBill::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'payments' => PaymentTransaction::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'expenses' => Expense::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'ledger' => LedgerEntry::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'parking' => ParkingSlot::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'parking_passes' => ParkingPass::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'amenities' => Amenity::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'bookings' => AmenityBooking::when($sid, fn ($q) => $q->whereHas('amenity', fn ($a) => $a->where('society_id', $sid)))->count(),
            'security' => SecurityIncident::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'cctv' => CctvCamera::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
            'sos' => SosAlert::when($sid, fn ($q) => $q->where('society_id', $sid))->count(),
        ];
    }

    protected function buildOverview(): array
    {
        $sid = $this->societyId;

        $flatsQuery = Flat::when($sid, fn ($q) => $q->where('society_id', $sid));
        $totalFlats = (clone $flatsQuery)->count();
        $occupied = (clone $flatsQuery)->where('status', 'occupied')->count();

        return [
            'occupancy' => $totalFlats ? round(($occupied / $totalFlats) * 100) : 0,
            'pending_bills' => MaintenanceBill::when($sid, fn ($q) => $q->where('society_id', $sid))->whereIn('status', ['pending', 'overdue'])->count(),
            'pending_dues' => MaintenanceBill::when($sid, fn ($q) => $q->where('society_id', $sid))->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'collected' => MaintenanceBill::when($sid, fn ($q) => $q->where('society_id', $sid))->where('status', 'paid')->sum('paid_amount'),
            'expenses_total' => Expense::when($sid, fn ($q) => $q->where('society_id', $sid))->sum('amount'),
            'income_ledger' => LedgerEntry::when($sid, fn ($q) => $q->where('society_id', $sid))->where('type', 'income')->sum('amount'),
            'open_complaints' => Complaint::when($sid, fn ($q) => $q->where('society_id', $sid))->whereIn('status', ['open', 'in_progress'])->count(),
            'visitors_inside' => VisitorEntry::when($sid, fn ($q) => $q->where('society_id', $sid))->where('status', 'checked_in')->count(),
            'visitors_pending' => VisitorEntry::when($sid, fn ($q) => $q->where('society_id', $sid))->where('status', 'pending')->count(),
            'active_sos' => SosAlert::when($sid, fn ($q) => $q->where('society_id', $sid))->where('status', 'active')->count(),
            'active_emergency' => EmergencyAlert::when($sid, fn ($q) => $q->where('society_id', $sid))->where('is_active', true)->count(),
            'recent_visitors' => VisitorEntry::when($sid, fn ($q) => $q->where('society_id', $sid))->with('flat')->latest()->take(5)->get(),
            'recent_complaints' => Complaint::when($sid, fn ($q) => $q->where('society_id', $sid))->with('flat')->latest()->take(5)->get(),
        ];
    }

    public function render()
    {
        $societies = Society::orderBy('name')->get();
        $selectedSociety = $this->societyId ? Society::with('subscriptionPlan')->find($this->societyId) : null;
        $counts = $this->buildCounts();
        $overview = $this->tab === 'overview' ? $this->buildOverview() : [];

        $search = '%'.$this->search.'%';
        $data = collect();

        if ($this->tab !== 'overview') {
            $data = match ($this->tab) {
                'members' => Member::with(['society', 'flat'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('name', 'like', $search)->orWhere('phone', 'like', $search)))
                    ->latest()->paginate(15),

                'flats' => Flat::with(['society', 'blockRelation'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('flat_no', 'like', $search))
                    ->latest()->paginate(15),

                'blocks' => Block::with('society')->withCount('flats')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('name', 'like', $search))
                    ->orderBy('name')->paginate(15),

                'vehicles' => MemberVehicle::with(['member', 'member.society'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('vehicle_no', 'like', $search))
                    ->latest()->paginate(15),

                'staff' => StaffMember::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('name', 'like', $search))
                    ->latest()->paginate(15),

                'attendance' => StaffAttendance::with('staff.society')
                    ->when($this->societyId, fn ($q) => $q->whereHas('staff', fn ($s) => $s->where('society_id', $this->societyId)))
                    ->latest('attendance_date')->paginate(15),

                'salaries' => StaffPayment::with('staff.society')
                    ->when($this->societyId, fn ($q) => $q->whereHas('staff', fn ($s) => $s->where('society_id', $this->societyId)))
                    ->latest('paid_at')->paginate(15),

                'vendors' => Vendor::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('name', 'like', $search))
                    ->orderBy('name')->paginate(15),

                'admins', 'guards', 'residents_users' => User::with('society')
                    ->where('role', match ($this->tab) {
                        'guards' => 'guard',
                        'residents_users' => 'resident',
                        default => 'admin',
                    })
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('name', 'like', $search)->orWhere('email', 'like', $search)))
                    ->latest()->paginate(15),

                'visitors' => VisitorEntry::with(['society', 'flat', 'deliveryStaff'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('visitor_name', 'like', $search)->orWhere('pass_code', 'like', $search)))
                    ->latest()->paginate(15),

                'delivery' => DeliveryStaff::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('name', 'like', $search))
                    ->orderBy('name')->paginate(15),

                'complaints' => Complaint::with(['society', 'flat', 'assignedStaff', 'assignedVendor'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('subject', 'like', $search))
                    ->latest()->paginate(15),

                'notices' => Notice::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('title', 'like', $search))
                    ->latest()->paginate(15),

                'events' => Event::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('title', 'like', $search))
                    ->orderBy('starts_at')->paginate(15),

                'notifications' => SocietyNotification::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('title', 'like', $search))
                    ->latest()->paginate(15),

                'emergency' => EmergencyAlert::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->latest()->paginate(15),

                'bills' => MaintenanceBill::with(['society', 'flat'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('title', 'like', $search))
                    ->latest()->paginate(15),

                'payments' => PaymentTransaction::with(['society', 'bill', 'bill.flat'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->latest()->paginate(15),

                'expenses' => Expense::with(['society', 'vendor'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('category', 'like', $search))
                    ->latest('expense_date')->paginate(15),

                'ledger' => LedgerEntry::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('category', 'like', $search))
                    ->latest('entry_date')->paginate(15),

                'parking' => ParkingSlot::with(['society', 'flat'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('slot_no', 'like', $search))
                    ->orderBy('slot_no')->paginate(15),

                'parking_passes' => ParkingPass::with(['society', 'member', 'parkingSlot'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->when($this->search, fn ($q) => $q->where('vehicle_no', 'like', $search))
                    ->latest()->paginate(15),

                'amenities' => Amenity::with('society')->withCount('bookings')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->orderBy('name')->paginate(15),

                'bookings' => AmenityBooking::with(['amenity.society', 'flat'])
                    ->when($this->societyId, fn ($q) => $q->whereHas('amenity', fn ($a) => $a->where('society_id', $this->societyId)))
                    ->latest()->paginate(15),

                'security' => SecurityIncident::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->latest()->paginate(15),

                'cctv' => CctvCamera::with('society')
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->orderBy('name')->paginate(15),

                'sos' => SosAlert::with(['society', 'flat', 'member'])
                    ->when($this->societyId, fn ($q) => $q->where('society_id', $this->societyId))
                    ->latest()->paginate(15),

                default => Member::with(['society', 'flat'])->latest()->paginate(15),
            };
        }

        $tabGroups = [
            'Overview' => [['overview', $this->societyId ? 'Society Overview' : 'Platform Overview', '📊']],
            'Property' => [
                ['blocks', 'Blocks', '🏗️'],
                ['flats', 'Flats', '🏠'],
                ['parking', 'Parking Slots', '🚗'],
                ['parking_passes', 'Parking Passes', '🎫'],
            ],
            'Residents' => [
                ['members', 'Members', '👥'],
                ['vehicles', 'Vehicles', '🚙'],
                ['residents_users', 'Resident Logins', '📱'],
            ],
            'Staff & Vendors' => [
                ['staff', 'Staff', '🛡️'],
                ['attendance', 'Attendance', '📋'],
                ['salaries', 'Salary Payments', '💵'],
                ['vendors', 'Vendors', '🔧'],
                ['admins', 'Society Admins', '👤'],
                ['guards', 'Guards', '🚪'],
            ],
            'Visitors' => [
                ['visitors', 'Visitor Entries', '🚶'],
                ['delivery', 'Delivery Staff', '📦'],
            ],
            'Operations' => [
                ['complaints', 'Complaints', '📋'],
                ['notices', 'Notices', '📢'],
                ['events', 'Events', '📅'],
                ['notifications', 'Notifications', '✉️'],
                ['emergency', 'Emergency Alerts', '🚨'],
            ],
            'Finance' => [
                ['bills', 'Maintenance Bills', '💰'],
                ['payments', 'Online Payments', '💳'],
                ['expenses', 'Expenses', '💸'],
                ['ledger', 'Ledger', '📒'],
            ],
            'Amenities' => [
                ['amenities', 'Amenities', '🏊'],
                ['bookings', 'Bookings', '📅'],
            ],
            'Security' => [
                ['security', 'Incidents', '⚠️'],
                ['cctv', 'CCTV', '📹'],
                ['sos', 'SOS Alerts', '🆘'],
            ],
        ];

        return $this->superAdminLayout('Platform Preview — Track All Societies')
            ->with(compact('societies', 'selectedSociety', 'counts', 'data', 'overview', 'tabGroups'));
    }
}
