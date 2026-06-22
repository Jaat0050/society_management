<?php

use App\Http\Controllers\Api\AccountantApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\CommitteeApiController;
use App\Http\Controllers\Api\GuardApiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ResidentApiController;
use App\Http\Controllers\Api\StaffApiController;
use App\Http\Controllers\Api\VendorApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/otp/send', [AuthController::class, 'sendOtp']);
    Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/config/features', [AuthController::class, 'features']);
        Route::get('/notifications', [AuthController::class, 'notifications']);
        Route::get('/config/app', [AuthController::class, 'appConfig']);
        Route::put('/preferences', [AuthController::class, 'updatePreferences']);

        Route::get('/chat/rooms', [ChatApiController::class, 'rooms']);
        Route::get('/chat/rooms/{id}/messages', [ChatApiController::class, 'messages']);
        Route::post('/chat/rooms/{id}/messages', [ChatApiController::class, 'send']);
        Route::get('/chat/rooms/{id}/stream', [ChatApiController::class, 'stream']);

        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/member', [ProfileController::class, 'updateMember']);
        Route::post('/profile/family', [ProfileController::class, 'addFamily']);
        Route::post('/profile/vehicle', [ProfileController::class, 'addVehicle']);

        Route::middleware('api.role:resident,tenant')->prefix('resident')->group(function () {
            Route::get('/dashboard', [ResidentApiController::class, 'dashboard']);
            Route::get('/bills', [ResidentApiController::class, 'bills']);
            Route::get('/payments/history', [ResidentApiController::class, 'paymentHistory']);
            Route::post('/bills/{id}/pay', [ResidentApiController::class, 'payBill']);
            Route::get('/complaints', [ResidentApiController::class, 'complaints']);
            Route::post('/complaints', [ResidentApiController::class, 'storeComplaint']);
            Route::post('/complaints/{id}/rate', [ResidentApiController::class, 'rateComplaint']);
            Route::get('/visitors', [ResidentApiController::class, 'visitors']);
            Route::post('/visitors', [ResidentApiController::class, 'preRegisterVisitor']);
            Route::get('/visitors/{id}/pass', [ResidentApiController::class, 'visitorPass']);
            Route::post('/visitors/{id}/approve', [ResidentApiController::class, 'approveVisitor']);
            Route::get('/notices', [ResidentApiController::class, 'notices']);
            Route::get('/events', [ResidentApiController::class, 'events']);
            Route::get('/amenities', [ResidentApiController::class, 'amenities']);
            Route::get('/bookings', [ResidentApiController::class, 'bookings']);
            Route::post('/bookings', [ResidentApiController::class, 'bookAmenity']);
            Route::delete('/bookings/{id}', [ResidentApiController::class, 'cancelBooking']);
            Route::post('/sos', [ResidentApiController::class, 'sos']);
            Route::get('/emergency-contacts', [ResidentApiController::class, 'emergencyContacts']);
        });

        Route::middleware('api.role:guard')->prefix('guard')->group(function () {
            Route::get('/dashboard', [GuardApiController::class, 'dashboard']);
            Route::post('/entries', [GuardApiController::class, 'newEntry']);
            Route::post('/verify', [GuardApiController::class, 'verify']);
            Route::post('/entries/{id}/check-in', [GuardApiController::class, 'checkIn']);
            Route::post('/entries/{id}/check-out', [GuardApiController::class, 'checkOut']);
            Route::get('/inside', [GuardApiController::class, 'inside']);
            Route::get('/log', [GuardApiController::class, 'log']);
            Route::get('/delivery-staff', [GuardApiController::class, 'deliveryStaff']);
            Route::get('/flats', [GuardApiController::class, 'flats']);
            Route::get('/sos', [GuardApiController::class, 'sosAlerts']);
            Route::post('/sos/{id}/resolve', [GuardApiController::class, 'resolveSos']);
            Route::post('/incidents', [GuardApiController::class, 'reportIncident']);
            Route::post('/verify-vehicle', [GuardApiController::class, 'verifyVehicle']);
            Route::post('/attendance', [GuardApiController::class, 'markAttendance']);
            Route::get('/staff', [GuardApiController::class, 'staffList']);
        });

        Route::middleware('api.role:staff')->prefix('staff')->group(function () {
            Route::get('/tasks', [StaffApiController::class, 'tasks']);
            Route::put('/tasks/{id}', [StaffApiController::class, 'updateTask']);
            Route::get('/schedule', [StaffApiController::class, 'schedule']);
            Route::post('/attendance', [StaffApiController::class, 'attendance']);
            Route::post('/inventory', [StaffApiController::class, 'inventoryRequest']);
        });

        Route::middleware('api.role:committee')->prefix('committee')->group(function () {
            Route::get('/overview', [CommitteeApiController::class, 'overview']);
            Route::get('/complaints', [CommitteeApiController::class, 'complaints']);
            Route::get('/notices', [CommitteeApiController::class, 'notices']);
            Route::get('/events', [CommitteeApiController::class, 'events']);
            Route::get('/visitor-stats', [CommitteeApiController::class, 'visitorStats']);
            Route::get('/financial', [CommitteeApiController::class, 'financialSummary']);
        });

        Route::middleware('api.role:accountant')->prefix('accountant')->group(function () {
            Route::get('/billing', [AccountantApiController::class, 'billing']);
            Route::get('/expenses', [AccountantApiController::class, 'expenses']);
            Route::post('/expenses', [AccountantApiController::class, 'storeExpense']);
            Route::get('/ledger', [AccountantApiController::class, 'ledger']);
            Route::get('/vendors', [AccountantApiController::class, 'vendors']);
            Route::get('/reports', [AccountantApiController::class, 'reports']);
        });

        Route::middleware('api.role:vendor')->prefix('vendor')->group(function () {
            Route::get('/tasks', [VendorApiController::class, 'tasks']);
            Route::put('/tasks/{id}', [VendorApiController::class, 'updateTask']);
            Route::post('/invoices', [VendorApiController::class, 'submitInvoice']);
            Route::get('/schedule', [VendorApiController::class, 'schedule']);
            Route::get('/payments', [VendorApiController::class, 'payments']);
        });
    });
});
