<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lab_tests';

    protected $fillable = [
        'test_name',
        'test_code',
        'lab_id',
        'category_id',
        'description',
        'price',
        'discount_price',
        'preparation_instructions',
        'sample_type',
        'report_delivery_time',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
        'discount_price' => 'float',
    ];

    /**
     * Relationship: Test belongs to a Lab.
     */
    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    /**
     * Relationship: Test belongs to a Category.
     */
    public function category()
    {
        return $this->belongsTo(LabTestCategory::class, 'category_id');
    }

    /**
     * Relationship: Test has many orders.
     */
    public function orders()
    {
        return $this->hasMany(LabTestOrder::class, 'lab_test_id');
    }

    /**
     * Scope: Active tests only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
