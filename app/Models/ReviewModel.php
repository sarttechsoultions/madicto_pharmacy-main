<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewModel extends Model
{
    protected $table = 'reviews';
    protected $fillable = [
        'coustmer_id',
        'medicine_id',
        'rating',
        'review'
    ];

    public function coustmer()
    {
        return $this->belongsTo(User::class);
    }

    public function medicine()
    {
        return $this->belongsTo(medicineModel::class, 'medicine_id');
    }
}
