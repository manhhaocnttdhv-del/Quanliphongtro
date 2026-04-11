<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RentRequestController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ContractController as UserContractController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RoomReviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\RentRequestController as AdminRentRequestController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\UtilityController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

// ─── Local Region API (Tỉnh/Huyện/Xã từ DB) ─────
Route::prefix('api/regions')->group(function () {
    Route::get('/provinces', [RegionController::class, 'provinces']);
    Route::get('/districts/{code}', [RegionController::class, 'districts']);
    Route::get('/wards/{code}', [RegionController::class, 'wards']);
    Route::get('/search', [RegionController::class, 'search']);
});



// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/api/rooms/map', [RoomController::class, 'mapRooms'])->name('rooms.map');


// Reviews (auth)
Route::middleware('auth')->group(function () {
    Route::post('/rooms/{room}/reviews', [RoomReviewController::class, 'store'])->name('rooms.reviews.store');
    Route::delete('/reviews/{review}', [RoomReviewController::class, 'destroy'])->name('rooms.reviews.destroy');
});

// ─── User Routes (authenticated) ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/rooms/{room}/request', [RentRequestController::class, 'create'])->name('rent-requests.create');
    Route::post('/rooms/{room}/request', [RentRequestController::class, 'store'])->name('rent-requests.store');

    // ─── Booking (Đặt phòng) Flow ─────────────────────────────────────────────
    Route::get('/rooms/{room}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'myBookings'])->name('bookings.my');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/payment/success', [BookingController::class, 'paymentSuccess'])->name('bookings.payment.success');
    Route::delete('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/my-invoices', [InvoiceController::class, 'index'])->name('user.invoices');
    Route::get('/my-invoices/{invoice}', [InvoiceController::class, 'show'])->name('user.invoices.show');

    // Contracts (tenant)
    Route::get('/my-contracts', [UserContractController::class, 'index'])->name('user.contracts.index');
    Route::get('/my-contracts/{contract}', [UserContractController::class, 'show'])->name('user.contracts.show');

    // Maintenance (tenant)
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
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
    Route::post('/rooms/{room}/approve', [AdminRoomController::class, 'approve'])->name('rooms.approve');
    Route::post('/rooms/{room}/reject', [AdminRoomController::class, 'reject'])->name('rooms.reject');

    // Rent Requests
    Route::get('/rent-requests', [AdminRentRequestController::class, 'index'])->name('rent-requests.index');
    Route::post('/rent-requests/{rentRequest}/approve', [AdminRentRequestController::class, 'approve'])->name('rent-requests.approve');
    Route::post('/rent-requests/{rentRequest}/reject', [AdminRentRequestController::class, 'reject'])->name('rent-requests.reject');

    // ─── Bookings (Admin) ──────────────────────────────────────────────────────
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm-payment', [AdminBookingController::class, 'confirmPayment'])->name('bookings.confirm-payment');
    Route::delete('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/convert-to-contract', [AdminBookingController::class, 'convertToContract'])->name('bookings.convert');
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
    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
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
    Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/confirm-payment', [AdminInvoiceController::class, 'confirmPayment'])->name('invoices.confirm-payment');
    Route::post('/invoices/{invoice}/cancel', [AdminInvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('/invoices/utility-data', [AdminInvoiceController::class, 'getUtilityData'])->name('invoices.utility-data');

    // Maintenance (admin)
    Route::get('/maintenance', [AdminMaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/{maintenance}', [AdminMaintenanceController::class, 'show'])->name('maintenance.show');
    Route::patch('/maintenance/{maintenance}/status', [AdminMaintenanceController::class, 'updateStatus'])->name('maintenance.update-status');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Super Admin Only: Landlords & Commissions
    Route::get('/landlords', [App\Http\Controllers\Admin\LandlordController::class, 'index'])->name('landlords.index');
    Route::get('/landlords/{user}', [App\Http\Controllers\Admin\LandlordController::class, 'show'])->name('landlords.show');
    Route::get('/commissions', [App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
    Route::post('/commissions/{commission}/pay', [App\Http\Controllers\Admin\CommissionController::class, 'markAsPaid'])->name('commissions.pay');

    // Super Admin Only: User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Sliders
    Route::resource('sliders', SliderController::class);
    Route::post('/sliders/{slider}/toggle', [SliderController::class, 'toggleActive'])->name('sliders.toggle');

    // Notifications (admin)
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

require __DIR__ . '/auth.php';
