<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class medicineModel extends Model
{
    protected $table = 'medicine';

    protected $fillable = [
        'name',
        'category_id',
        'batch_no',
        'usage_instructions',
        'discount',
        'quantity',
        'stock',
        'manufacturer',
        'reorder_level',
        'description',
        'price',
        'unit_type',
        'pack_size',
        'status',
        'manufacture_date',
        'expiry_date',
        'image',
        'medicine_image'
    ];
    protected $casts = [
        'medicine_image' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function orders()
    {
        return $this->hasMany(OrdersModel::class, 'medicine_id');
    }
    public function reviews()
    {
        return $this->hasMany(ReviewModel::class, 'medicine_id', 'id');
    }
}
