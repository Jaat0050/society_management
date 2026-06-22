<?php

use App\Livewire\Admin\Amenities as AdminAmenities;
use App\Livewire\Admin\Billing as AdminBilling;
use App\Livewire\Admin\Blocks as AdminBlocks;
use App\Livewire\Admin\Complaints as AdminComplaints;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Events as AdminEvents;
use App\Livewire\Admin\Flats as AdminFlats;
use App\Livewire\Admin\Notices as AdminNotices;
use App\Livewire\Admin\Parking as AdminParking;
use App\Livewire\Admin\Payments as AdminPayments;
use App\Livewire\Admin\Reports as AdminReports;
use App\Livewire\Admin\Residents as AdminResidents;
use App\Livewire\Admin\Accounting as AdminAccounting;
use App\Livewire\Admin\Communications as AdminCommunications;
use App\Livewire\Admin\DeliveryStaff as AdminDeliveryStaff;
use App\Livewire\Admin\Expenses as AdminExpenses;
use App\Livewire\Admin\ResidentProfile as AdminResidentProfile;
use App\Livewire\Admin\SecurityModule as AdminSecurity;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\StaffHub as AdminStaffHub;
use App\Livewire\Admin\Vendors as AdminVendors;
use App\Http\Controllers\ExportController;
use App\Livewire\Resident\Amenities as ResidentAmenities;
use App\Livewire\Resident\Bills as ResidentBills;
use App\Livewire\Resident\Complaints as ResidentComplaints;
use App\Livewire\Resident\Dashboard as ResidentDashboard;
use App\Livewire\Resident\Notices as ResidentNotices;
use App\Livewire\Resident\Sos as ResidentSos;
use App\Livewire\Resident\Visitors as ResidentVisitors;
use App\Livewire\Admin\Staff as AdminStaff;
use App\Livewire\Admin\Visitors as AdminVisitors;
use App\Livewire\Auth\Login;
use App\Livewire\Guard\Dashboard as GuardDashboard;
use App\Livewire\Guard\EntryLog as GuardEntryLog;
use App\Livewire\Guard\InsideVisitors as GuardInsideVisitors;
use App\Livewire\Guard\NewEntry as GuardNewEntry;
use App\Livewire\Guard\PassDetail as GuardPassDetail;
use App\Livewire\Guard\ScanVerify as GuardScanVerify;
use App\Livewire\Visit\Approve as VisitApprove;
use App\Http\Controllers\VisitorQrController;
use App\Livewire\SuperAdmin\Admins as SuperAdmins;
use App\Livewire\SuperAdmin\Dashboard as SuperDashboard;
use App\Livewire\SuperAdmin\Notifications as SuperNotifications;
use App\Livewire\SuperAdmin\PlatformPreview as SuperPlatformPreview;
use App\Livewire\SuperAdmin\Plans as SuperPlans;
use App\Livewire\SuperAdmin\Reports as SuperReports;
use App\Livewire\SuperAdmin\Settings as SuperSettings;
use App\Livewire\SuperAdmin\Societies as SuperSocieties;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', SuperDashboard::class)->name('dashboard');
    Route::get('/societies', SuperSocieties::class)->name('societies');
    Route::get('/preview', SuperPlatformPreview::class)->name('preview');
    Route::get('/preview/{societyId}', SuperPlatformPreview::class)->name('preview.society');
    Route::get('/admins', SuperAdmins::class)->name('admins');
    Route::get('/plans', SuperPlans::class)->name('plans');
    Route::get('/notifications', SuperNotifications::class)->name('notifications');
    Route::get('/reports', SuperReports::class)->name('reports');
    Route::get('/settings', SuperSettings::class)->name('settings');
});

Route::middleware(['auth', 'society_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/blocks', AdminBlocks::class)->name('blocks');
    Route::get('/flats', AdminFlats::class)->name('flats');
    Route::get('/residents', AdminResidents::class)->name('residents');
    Route::get('/residents/{id}', AdminResidentProfile::class)->name('residents.profile');
    Route::get('/staff', AdminStaff::class)->name('staff');
    Route::get('/staff-hub', AdminStaffHub::class)->name('staff-hub');
    Route::get('/vendors', AdminVendors::class)->name('vendors');
    Route::get('/expenses', AdminExpenses::class)->name('expenses');
    Route::get('/accounting', AdminAccounting::class)->name('accounting');
    Route::get('/communications', AdminCommunications::class)->name('communications');
    Route::get('/security', AdminSecurity::class)->name('security');
    Route::get('/export/bills', [ExportController::class, 'bills'])->name('export.bills');
    Route::get('/export/residents', [ExportController::class, 'residents'])->name('export.residents');
    Route::get('/export/ledger', [ExportController::class, 'ledger'])->name('export.ledger');
    Route::get('/export/visitors', [ExportController::class, 'visitors'])->name('export.visitors');
    Route::get('/complaints', AdminComplaints::class)->name('complaints');
    Route::get('/notices', AdminNotices::class)->name('notices');
    Route::get('/events', AdminEvents::class)->name('events');
    Route::get('/billing', AdminBilling::class)->name('billing');
    Route::get('/payments', AdminPayments::class)->name('payments');
    Route::get('/parking', AdminParking::class)->name('parking');
    Route::get('/amenities', AdminAmenities::class)->name('amenities');
    Route::get('/visitors', AdminVisitors::class)->name('visitors');
    Route::get('/delivery-staff', AdminDeliveryStaff::class)->name('delivery-staff');
    Route::get('/settings', AdminSettings::class)->name('settings');
    Route::get('/reports', AdminReports::class)->name('reports');
});

Route::middleware(['auth', 'guard'])->prefix('guard')->name('guard.')->group(function () {
    Route::get('/dashboard', GuardDashboard::class)->name('dashboard');
    Route::get('/new-entry', GuardNewEntry::class)->name('new-entry');
    Route::get('/scan', GuardScanVerify::class)->name('scan');
    Route::get('/inside', GuardInsideVisitors::class)->name('inside');
    Route::get('/log', GuardEntryLog::class)->name('log');
    Route::get('/pass/{id}', GuardPassDetail::class)->name('pass');
});

Route::middleware(['auth', 'resident'])->prefix('resident')->name('resident.')->group(function () {
    Route::get('/dashboard', ResidentDashboard::class)->name('dashboard');
    Route::get('/bills', ResidentBills::class)->name('bills');
    Route::get('/complaints', ResidentComplaints::class)->name('complaints');
    Route::get('/visitors', ResidentVisitors::class)->name('visitors');
    Route::get('/amenities', ResidentAmenities::class)->name('amenities');
    Route::get('/notices', ResidentNotices::class)->name('notices');
    Route::get('/sos', ResidentSos::class)->name('sos');
});

Route::get('/visit/approve/{token}', VisitApprove::class)->name('visit.approve');
Route::get('/visit/qr/{token}', [VisitorQrController::class, 'show'])->name('visit.qr');
