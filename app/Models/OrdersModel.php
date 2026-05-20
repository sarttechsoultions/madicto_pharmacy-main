<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'order_id',
        'medicine_id',
        'payment_status',
        'quantity',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medicine()
    {
        return $this->belongsTo(MedicineModel::class, 'medicine_id');
    }
}
