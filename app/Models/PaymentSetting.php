<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $table = 'payment_settings';

    protected $fillable = [
        'gateway',
        'key_id',
        'key_secret',
        'mode',
        'is_active',
        'currency',
        'webhook_secret',
        'display_name',
        'description',
        'extra_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra_settings' => 'array',
    ];

    /**
     * Get or create Razorpay settings singleton
     */
    public static function getRazorpay(): self
    {
        return static::firstOrCreate(
            ['gateway' => 'razorpay'],
            [
                'key_id' => config('razorpay.key', 'rzp_test_U5ZUZ60slH5W2J'),
                'key_secret' => config('razorpay.secret', '7s3I21rNfySQ4rH5GISlWIyu'),
                'mode' => config('razorpay.mode', 'test'),
                'is_active' => true,
                'currency' => config('razorpay.currency', 'INR'),
                'display_name' => 'Razorpay Payment Gateway',
                'description' => 'Online payments via UPI, Credit/Debit Cards, NetBanking, and Wallets.',
            ]
        );
    }
}
