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
        'medicine_image',
        'dod',
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

    /**
     * Safely access gallery images as an array.
     */
    public function getGalleryImagesAttribute(): array
    {
        if (is_array($this->medicine_image)) {
            return $this->medicine_image;
        }

        if (empty($this->medicine_image)) {
            return [];
        }

        $decoded = json_decode($this->medicine_image, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return [$this->medicine_image];
    }
}
