<?php

use App\Http\Controllers\BannersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CoustmerMedicineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
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



// Admin Medicine Route
Route::get('/medicine', [MedicineController::class, 'index'])->name('medicine.index');
Route::post('/medicine/store', [MedicineController::class, 'store'])->name('admin.medicine.store');
Route::put('/admin/medicine/{id}', [MedicineController::class, 'update'])->name('admin.medicine.update');
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

// User Orders Route
Route::get('/users', [UserController::class, 'index'])->name('user.index');
Route::post('/user/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');
Route::post('/admin/toggle-role', [UserController::class, 'toggleRole'])->name('admin.toggle.role');

// Banners Orders Route
Route::get('/banners', [BannersController::class, 'index'])->name('banner.index');
Route::post('/banners/store', [BannersController::class, 'store'])->name('admin.banners.store');
Route::put('/banners/{id}', [BannersController::class, 'update'])->name('admin.banners.update');
Route::post('/banner/toggle-status/{id}', [BannersController::class, 'toggleStatus'])->name('banner.toggleStatus');

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

Route::post('/medicine/import', [MedicineController::class, 'import']);
Route::get('/medicine/sample-download', [MedicineController::class, 'downloadSample']);


require __DIR__ . '/auth.php';
