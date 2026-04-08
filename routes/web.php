<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RentRequestController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\RentRequestController as AdminRentRequestController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\UtilityController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

// ─── User Routes (authenticated) ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/rooms/{room}/request', [RentRequestController::class, 'create'])->name('rent-requests.create');
    Route::post('/rooms/{room}/request', [RentRequestController::class, 'store'])->name('rent-requests.store');

    Route::get('/my-invoices', [InvoiceController::class, 'index'])->name('user.invoices');
    Route::get('/my-invoices/{invoice}', [InvoiceController::class, 'show'])->name('user.invoices.show');

    Route::get('/maintenance', [App\Http\Controllers\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [App\Http\Controllers\MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [App\Http\Controllers\MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{maintenanceRequest}', [App\Http\Controllers\MaintenanceController::class, 'show'])->name('maintenance.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mark notifications as read
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Fallback dashboard route for Breeze compatibility
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('rooms.index');
    })->name('dashboard');
});


// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rooms
    Route::resource('rooms', AdminRoomController::class);
    Route::delete('/rooms/images/{image}', [AdminRoomController::class, 'destroyImage'])->name('rooms.images.destroy');

    // Rent Requests
    Route::get('/rent-requests', [AdminRentRequestController::class, 'index'])->name('rent-requests.index');
    Route::post('/rent-requests/{rentRequest}/approve', [AdminRentRequestController::class, 'approve'])->name('rent-requests.approve');
    Route::post('/rent-requests/{rentRequest}/reject', [AdminRentRequestController::class, 'reject'])->name('rent-requests.reject');

    // Room Members
    Route::post('/room-members', [App\Http\Controllers\Admin\RoomMemberController::class, 'store'])->name('room-members.store');
    Route::patch('/room-members/{roomMember}', [App\Http\Controllers\Admin\RoomMemberController::class, 'update'])->name('room-members.update');
    Route::delete('/room-members/{roomMember}', [App\Http\Controllers\Admin\RoomMemberController::class, 'destroy'])->name('room-members.destroy');

    // Maintenance
    Route::get('/maintenance', [App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/{maintenance}', [App\Http\Controllers\Admin\MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::patch('/maintenance/{maintenance}', [App\Http\Controllers\Admin\MaintenanceController::class, 'update'])->name('maintenance.update');

    // Contracts
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::post('/contracts/{contract}/end', [ContractController::class, 'endContract'])->name('contracts.end');

    // Utilities
    Route::get('/utilities', [UtilityController::class, 'index'])->name('utilities.index');
    Route::get('/utilities/create', [UtilityController::class, 'create'])->name('utilities.create');
    Route::post('/utilities', [UtilityController::class, 'store'])->name('utilities.store');

    // Invoices
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [AdminInvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [AdminInvoiceController::class, 'store'])->name('invoices.store');
    Route::post('/invoices/{invoice}/confirm-payment', [AdminInvoiceController::class, 'confirmPayment'])->name('invoices.confirm-payment');
    Route::get('/invoices/utility-data', [AdminInvoiceController::class, 'getUtilityData'])->name('invoices.utility-data');

    // Super Admin Only: Landlords & Commissions
    Route::get('/landlords', [App\Http\Controllers\Admin\LandlordController::class, 'index'])->name('landlords.index');
    Route::get('/landlords/{user}', [App\Http\Controllers\Admin\LandlordController::class, 'show'])->name('landlords.show');
    Route::get('/commissions', [App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
    Route::post('/commissions/{commission}/pay', [App\Http\Controllers\Admin\CommissionController::class, 'markAsPaid'])->name('commissions.pay');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Notifications (admin)
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

require __DIR__ . '/auth.php';
