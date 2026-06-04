<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersModel extends Model
{
    protected $table = 'orders';

    protected $fillable = [

        'order_id',
        'coustmer_id',

        'delivery_address_label',
        'delivery_phone_number',
        'delivery_street_address',
        'delivery_city',
        'delivery_state',
        'delivery_pin_code',
        'delivery_landmark',

        'subtotal',
        'shipping_charge',
        'discount',
        'total_amount',

        'status',

        'ordered_at',
        'confirmed_at',
        'processing_at',
        'out_for_delivery_at',
        'delivered_at',
        'cancelled_at'
    ];

    public function items()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }



    public function user()
    {
        return $this->belongsTo(User::class, 'coustmer_id');
    }

    public function medicine()
    {
        return $this->belongsTo(medicineModel::class, 'medicine_id');
    }
}
