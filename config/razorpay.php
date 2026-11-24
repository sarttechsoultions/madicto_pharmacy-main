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
    'key' => env('RAZORPAY_KEY', 'rzp_test_U3zV6osfu1lI72'),
    'secret' => env('RAZORPAY_SECRET', 'yKOX3i4suTZvRzaC3TVDe9eT'),
    'currency' => env('RAZORPAY_CURRENCY', 'INR'),
    'mode' => env('RAZORPAY_MODE', 'test'),
    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
    'company_name' => env('APP_NAME', 'Medicto Pharmacy'),
];
