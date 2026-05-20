<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class medicineModel extends Model
{
    protected $table = 'medicine';

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function orders()
    {
        return $this->hasMany(OrdersModel::class, 'medicine_id');
    }
}
