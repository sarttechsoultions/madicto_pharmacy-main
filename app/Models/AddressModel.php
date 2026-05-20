<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressModel extends Model
{
    protected $table = 'address';

    protected $fillable = [
        'coustmer_id',
        'address_label',
        'full_name',
        'phone_number',
        'street_address',
        'landmark',
        'city',
        'state',
        'pin_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
