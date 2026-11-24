<?php

use App\Http\Controllers\BannersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CoustmerMedicineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\LabTestCategoryController;
use App\Http\Controllers\Admin\RazorpayAdminController;
use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'webIndex'])->name('home');

Route::get('/privacy', function () {
    return view('web.privacy');
})->name('privacy');

// Testing Route
// Route::get('/settings/profile', function () {
//     return view('admin.setting');
// })->name('settings.profile');

// Route::get('/dashboard', function () {
//     return view('admin.dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth:sanctum')->group(function () {

    // Admin Medicine Route
    Route::get('/medicine', [MedicineController::class, 'index'])->name('medicine.index');
    Route::post('/medicine/store', [MedicineController::class, 'store'])->name('admin.medicine.store');
    Route::put('/medicine/{id}', [MedicineController::class, 'update'])->name('admin.medicine.update');
    Route::delete('/admin/medicine/{id}', [MedicineController::class, 'destroy'])->name('admin.medicine.destroy');

    // Admin Category Route
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // Admin Orders Route
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::post('/orders/store', [OrdersController::class, 'store'])->name('admin.orders.store');
    Route::post('/update-order-status', [OrdersController::class, 'updateOrderStatus'])->name('admin.orders.updateOrderStatus');
    Route::post('/update-payment-status', [OrdersController::class, 'updatePaymentStatus'])->name('admin.orders.updatePaymentStatus');
    Route::get('/order/{id}/details', [OrdersController::class, 'details'])->name('order.details');
    Route::delete('/orders/{id}', [OrdersController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{id}/invoice', [OrdersController::class, 'invoice'])->name('orders.invoice');

    // Admin Razorpay Payments & Gateway Management
    Route::get('/admin/payments', [RazorpayAdminController::class, 'index'])->name('admin.payments.index');
    Route::get('/admin/payments/settings', [RazorpayAdminController::class, 'settings'])->name('admin.payments.settings');
    Route::post('/admin/payments/settings', [RazorpayAdminController::class, 'updateSettings'])->name('admin.payments.update_settings');
    Route::get('/admin/payments/test-connection', [RazorpayAdminController::class, 'testConnection'])->name('admin.payments.test_connection');
    Route::get('/admin/payments/{id}', [RazorpayAdminController::class, 'show'])->name('admin.payments.show');
    Route::post('/admin/payments/{id}/sync', [RazorpayAdminController::class, 'sync'])->name('admin.payments.sync');
    Route::post('/admin/payments/{id}/refund', [RazorpayAdminController::class, 'refund'])->name('admin.payments.refund');

    // User Orders Route
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');
    Route::post('/admin/toggle-role', [UserController::class, 'toggleRole'])->name('admin.toggle.role');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');

    // Banners Orders Route
    Route::get('/banners', [BannersController::class, 'index'])->name('banner.index');
    Route::post('/banners/store', [BannersController::class, 'store'])->name('admin.banners.store');
    Route::put('/banners/{id}', [BannersController::class, 'update'])->name('admin.banners.update');
    Route::post('/banner/toggle-status/{id}', [BannersController::class, 'toggleStatus'])->name('banner.toggleStatus');
    Route::put('/banners/{id}', [BannersController::class, 'updatedata'])->name('banners.update');

    // Coustmer Medicine Orders Route
    Route::get('/coustmer/medicine', [CoustmerMedicineController::class, 'index'])->name('coustmer.medicine.index');
    Route::post('/admin/add-cart', [CartController::class, 'addCart'])->name('admin.add.cart');

    // Setting Orders Route
    Route::get('/settings', [UserController::class, 'profile'])->name('settings.profile');
    Route::post('/admin/profile-update', [UserController::class, 'profileUpdate'])
        ->name('admin.profile.update');
    Route::post('/admin/role-store', [UserController::class, 'store'])
        ->name('admin.role.store');


    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Review Route
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

    Route::post('/medicine/dod/{id}', [MedicineController::class, 'toggleDod'])->name('medicine.dod');
    Route::post('/medicine/{id}/toggle-status', [MedicineController::class, 'toggleStatus'])->name('admin.medicine.toggleStatus');

    Route::post('/medicine/import', [MedicineController::class, 'import']);
    Route::get('/medicine/sample-download', [MedicineController::class, 'downloadSample']);
    Route::post('/bannersd/{id}', [BannersController::class, 'destroy'])
        ->name('banners.destroy');


    Route::middleware('auth:sanctum')->post(
        '/save-fcm-token',
        [UserController::class, 'saveFcmToken']
    );

    Route::get('/notification', [NotificationController::class, 'index'])
        ->name('notifications');

    Route::post('/notification/send', [NotificationController::class, 'send'])
        ->name('admin.notification.send');

    Route::get('/notification/{id}', [NotificationController::class, 'show'])
        ->name('admin.notification.show');

    Route::delete('/notification/{id}', [NotificationController::class, 'destroy'])
        ->name('admin.notification.delete');

    Route::post('/notification/{id}/resend', [NotificationController::class, 'resend'])
        ->name('admin.notification.resend');


    Route::get('/medicine/{id}/details', [MedicineController::class, 'show'])
        ->name('medicine.show');

    Route::get('/users/{id}', [UserController::class, 'show'])
        ->name('user.show');

    // Lab Test Management (New Booking System)
    Route::get('/lab/bookings/dashboard', [LabController::class, 'dashboard'])->name('admin.lab.bookings.dashboard');
    Route::get('/lab/bookings', [LabController::class, 'allBookings'])->name('admin.lab.bookings.index');
    Route::get('/lab/bookings/calendar', [LabController::class, 'calendar'])->name('admin.lab.bookings.calendar');
    Route::get('/lab/bookings/reports', [LabController::class, 'reports'])->name('admin.lab.bookings.reports');
    Route::get('/lab/bookings/{id}', [LabController::class, 'showBooking'])->name('admin.lab.bookings.show');
    Route::post('/lab/bookings/{id}/status', [LabController::class, 'updateBookingStatus'])->name('admin.lab.bookings.status');
    Route::post('/lab/bookings/{id}/confirm', [LabController::class, 'confirmBooking'])->name('admin.lab.bookings.confirm');
    Route::post('/lab/bookings/{id}/cancel', [LabController::class, 'quickCancelBooking'])->name('admin.lab.bookings.cancel');
    Route::get('/lab/bookings/{id}/details-json', [LabController::class, 'bookingDetails'])->name('admin.lab.bookings.details_json');
    Route::delete('/lab/bookings/{id}', [LabController::class, 'destroyBooking'])->name('admin.lab.bookings.destroy');

    // Compatibility aliases
    Route::get('/lab/dashboard', [LabController::class, 'dashboard'])->name('admin.lab.dashboard');
    Route::get('/lab/orders', [LabController::class, 'allBookings'])->name('admin.lab.orders');
});

// Public Customer Mobile-Friendly Lab Test Browsing & Booking
Route::get('/lab-tests', [LabController::class, 'customerIndex'])->name('customer.lab.tests');
Route::post('/lab-test-booking', [LabController::class, 'customerBook'])->name('customer.lab.book');
Route::get('/lab-test-booking/confirmation/{id}', [LabController::class, 'customerConfirmation'])->name('customer.lab.confirmation');
Route::get('/my-lab-bookings', [LabController::class, 'customerBookings'])->name('customer.lab.my_bookings');

require __DIR__ . '/auth.php';
