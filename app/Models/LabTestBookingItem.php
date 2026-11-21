<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestBookingItem extends Model
{
    use HasFactory;

    protected $table = 'lab_test_booking_items';

    protected $fillable = [
        'booking_id',
        'lab_test_id',
        'lab_id',
        'test_name',
        'test_code',
        'lab_name',
        'price',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function booking()
    {
        return $this->belongsTo(LabTestBooking::class, 'booking_id');
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class, 'lab_test_id');
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }
}
