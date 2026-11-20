<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTestOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lab_test_orders';

    protected $fillable = [
        'order_id',
        'user_id',
        'lab_test_id',
        'lab_id',
        'patient_name',
        'patient_phone',
        'patient_age',
        'patient_gender',
        'sample_collection_type',
        'collection_address',
        'appointment_date',
        'appointment_time',
        'amount',
        'payment_status',
        'payment_method',
        'status',
        'report_file',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'appointment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
