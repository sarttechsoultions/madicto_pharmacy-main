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
                'key_id' => config('razorpay.key', 'rzp_test_U3zV6osfu1lI72'),
                'key_secret' => config('razorpay.secret', 'yKOX3i4suTZvRzaC3TVDe9eT'),
                'mode' => config('razorpay.mode', 'test'),
                'is_active' => true,
                'currency' => config('razorpay.currency', 'INR'),
                'display_name' => 'Razorpay Payment Gateway',
                'description' => 'Online payments via UPI, Credit/Debit Cards, NetBanking, and Wallets.',
            ]
        );
    }
}
