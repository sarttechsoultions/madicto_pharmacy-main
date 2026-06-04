<?php

use App\Http\Controllers\api\api;
use App\Http\Controllers\api\auth;
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

Route::get('/medicines/{id?}', [api::class, 'getMedicines']);
Route::get('/medicines/{id}/related', [api::class, 'relatedMedicines']);
Route::get('/categories/{id?}', [api::class, 'getCategory']);
Route::get('/banners', [api::class, 'getBanners']);

Route::get('/reviews/{medicine_id}', [api::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
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

    Route::get('/cart', [api::class, 'carts']);

    Route::post('/cart-add', [api::class, 'cartstore']);
    Route::post('/cart-update/{id}', [api::class, 'cartupdate']);
    Route::post('/cart-remove/{id}', [api::class, 'cartdestroy']);
});
