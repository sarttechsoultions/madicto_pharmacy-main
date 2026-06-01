<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';

    protected $fillable = [

        'order_id',
        'medicine_id',

        'medicine_name',
        'medicine_image',

        'price',
        'quantity',
        'subtotal'
    ];

    public function order()
    {
        return $this->belongsTo(OrdersModel::class, 'order_id');
    }

    public function medicine()
    {
        return $this->belongsTo(MedicineModel::class, 'medicine_id');
    }
}
