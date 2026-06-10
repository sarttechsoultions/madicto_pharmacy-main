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
}
