<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineFavourite extends Model
{
    protected $table = 'medicine_favourites';
    protected $fillable = [
        'coustmer_id',
        'medicine_id'
    ];

    public function medicine()
    {
        return $this->belongsTo(medicineModel::class, 'medicine_id');
    }
}
