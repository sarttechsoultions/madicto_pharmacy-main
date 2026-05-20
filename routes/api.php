<?php

use App\Http\Controllers\Api\api;
use App\Http\Controllers\Api\auth;
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
    Route::post('/address-created', [api::class, 'AddressCreated']);
    Route::post('/address-deleted', [api::class, 'AddressDeleted']);
    Route::post('/logout', [api::class, 'logout']);
});
