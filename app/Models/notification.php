<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{

    protected $table = 'noties';

    protected $fillable = [

        'title',

        'message',

        'type',

        'image',

        'send_to',

        'status',

        'total_users',

        'success_count',

        'failed_count',

    ];
}
