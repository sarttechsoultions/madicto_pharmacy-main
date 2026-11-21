<?php

use App\Http\Controllers\api\api;
use App\Http\Controllers\api\auth;
use App\Http\Controllers\Api\LabApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;


Route::post('/register', [auth::class, 'register']);

// Route::post('/login', [auth::class, 'login']);

Route::post('/forgot-password', [auth::class, 'forgotPassword']);

Route::post('/send-otp', [auth::class, 'sendOtp']);
Route::post('/verify-otp', [auth::class, 'verifyOtp']);

// Public Medicine APIs
Route::match(['get', 'post'], '/medicines', [api::class, 'getMedicines']);
Route::match(['get', 'post'], '/medicines-search', [api::class, 'searchMedicines']);
Route::get('/medicines/{id}', [api::class, 'getMedicineDetail'])->where('id', '[0-9]+');
Route::get('/medicines/{id}/related', [api::class, 'relatedMedicines'])->where('id', '[0-9]+');
Route::post('/categories', [api::class, 'getCategory']);
Route::get('/banners', [api::class, 'getBanners']);

Route::get('/reviews/{medicine_id}', [api::class, 'index']);
Route::get('/trending-medicine', [api::class, 'trendingProducts']);
Route::get('/deals-of-day', [api::class, 'dealsOfDay']);
Route::get('/category-is-available', [api::class, 'categoryIsAvailable']);

// Mobile APIs for Lab Test Booking
Route::get('/lab-test-bookings/available-slots', [LabApiController::class, 'getAvailableSlots']);
Route::post('/lab-test-bookings', [LabApiController::class, 'createBooking']);
Route::get('/my-lab-test-bookings', [LabApiController::class, 'getMyBookings']);
Route::get('/lab-test-bookings/{id}', [LabApiController::class, 'getBookingDetail']);
Route::post('/lab-test-bookings/{id}/cancel', [LabApiController::class, 'cancelBooking']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/customer-medicine-store', [api::class, 'storeCustomerMedicine']);
    Route::post('/cancel-order', [api::class, 'cancelOrder']);
    Route::get('/orders', [api::class, 'getOrders']);
    Route::get('/order-detail/{id}', [api::class, 'orderDetail']);

    Route::post('/orders-created', [api::class, 'OrdersCreated']);

    Route::get('/addresses', [api::class, 'getAddresses']);
    Route::post('/address-created', [api::class, 'AddressCreated']);
    Route::post('/address-updated/{id?}', [api::class, 'AddressUpdated']);
    Route::post('/address-deleted/{id?}', [api::class, 'AddressDeleted']);

    Route::post('/update-profile', [api::class, 'updateProfile']);
    Route::get('/profile', [api::class, 'getProfile']);

    Route::post('/review-add', [api::class, 'store']);

    Route::put('/review-update/{id}', [api::class, 'update']);

    Route::delete('/review-delete/{id}', [api::class, 'destroy']);

    Route::get('/referral', [api::class, 'shareReferral']);
    Route::post('/apply-referral', [api::class, 'applyReferralCode']);
    Route::post('/logout', [api::class, 'logout']);
    Route::delete('/delete-account', [api::class, 'deleteAccount']);
    Route::post('/delete-account', [api::class, 'deleteAccount']);

    Route::get('/cart', [api::class, 'carts']);

    Route::post('/cart-add', [api::class, 'cartstore']);
    Route::post('/cart-update/{id}', [api::class, 'cartupdate']);
    Route::post('/cart-remove/{id}', [api::class, 'cartdestroy']);

    Route::post('/medicine-favourite', [api::class, 'toggleFavourite']);
    Route::get('/favourite-medicines', [api::class, 'favouriteMedicines']);

    // Admin / Management Medicine APIs
    Route::post('/medicines/create', [api::class, 'storeMedicine']);
    Route::post('/medicines/{id}/update', [api::class, 'updateMedicine'])->where('id', '[0-9]+');
    Route::put('/medicines/{id}', [api::class, 'updateMedicine'])->where('id', '[0-9]+');
    Route::delete('/medicines/{id}', [api::class, 'destroyMedicine'])->where('id', '[0-9]+');
    Route::post('/medicines/{id}/image', [api::class, 'uploadMedicineImage'])->where('id', '[0-9]+');
    Route::post('/medicines/{id}/toggle-status', [api::class, 'toggleMedicineStatus'])->where('id', '[0-9]+');
});
