<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoustmerMedicineModel extends Model
{
    protected $table = 'coustmer_medicine';
    protected $fillable = [
        'coustmer_id',
        'medicine_id',
        'quantity',
    ];

    public function coustmer()
    {
        return $this->belongsTo(User::class, 'coustmer_id');
    }

    public function medicine()
    {
        return $this->belongsTo(medicineModel::class, 'medicine_id');
    }
}
