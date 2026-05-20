<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannersModel extends Model
{
    protected $table = 'banners';
    protected $fillable = ['title', 'description', 'img', 'start_date', 'end_date'];
}
