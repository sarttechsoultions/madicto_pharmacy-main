<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'order_id',
        'booking_id',
        'payment_type',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'currency',
        'status',
        'method',
        'bank',
        'wallet',
        'vpa',
        'email',
        'contact',
        'error_code',
        'error_description',
        'refund_id',
        'refund_status',
        'amount_refunded',
        'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_refunded' => 'decimal:2',
        'raw_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(OrdersModel::class, 'order_id', 'order_id');
    }

    public function booking()
    {
        return $this->belongsTo(LabTestBooking::class, 'booking_id', 'booking_id');
    }
}
