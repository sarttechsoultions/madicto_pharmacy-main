<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Razorpay API Credentials
    |--------------------------------------------------------------------------
    |
    | Used by Razorpay SDK and Payment Services across API and Admin.
    |
    */
    'key' => env('RAZORPAY_KEY', 'rzp_test_U5ZUZ60slH5W2J'),
    'secret' => env('RAZORPAY_SECRET', '7s3I21rNfySQ4rH5GISlWIyu'),
    'currency' => env('RAZORPAY_CURRENCY', 'INR'),
    'mode' => env('RAZORPAY_MODE', 'test'),
    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
    'company_name' => env('APP_NAME', 'Medicto Pharmacy'),
];
