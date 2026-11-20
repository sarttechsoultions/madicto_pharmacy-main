<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'labs';

    protected $fillable = [
        'name',
        'logo',
        'description',
        'contact_number',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'status',
    ];

    /**
     * Relationship: A Lab has many tests.
     */
    public function tests()
    {
        return $this->hasMany(LabTest::class, 'lab_id');
    }

    /**
     * Relationship: Active tests only.
     */
    public function activeTests()
    {
        return $this->hasMany(LabTest::class, 'lab_id')->where('status', 'active');
    }

    /**
     * Relationship: A Lab has many orders.
     */
    public function orders()
    {
        return $this->hasMany(LabTestOrder::class, 'lab_id');
    }

    /**
     * Scope: Filter active labs only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
