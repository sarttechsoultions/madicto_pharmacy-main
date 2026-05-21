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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/medicines', [api::class, 'getMedicines']);
    Route::get('/categories', [api::class, 'getCategory']);
    Route::get('/banners', [api::class, 'getBanners']);
    Route::get('/orders', [api::class, 'getOrders']);

    Route::post('/orders-created', [api::class, 'OrdersCreated']);

    Route::get('/addresses', [api::class, 'getAddresses']);
    Route::post('/address-created', [api::class, 'AddressCreated']);
    Route::post('/address-deleted', [api::class, 'AddressDeleted']);

    Route::post('/update-profile', [api::class, 'updateProfile']);
    Route::get('/profile', [api::class, 'getProfile']);

    Route::get('/referral', [api::class, 'shareReferral']);
    Route::post('/apply-referral', [api::class, 'applyReferralCode']);
    Route::post('/logout', [api::class, 'logout']);
});
