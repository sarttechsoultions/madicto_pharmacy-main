<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'category';

    protected $fillable = [
        'name',
        'description',
        'status',
        'icon'
    ];

    public function medicines()
    {
        return $this->hasMany(medicineModel::class, 'category_id');
    }
}
