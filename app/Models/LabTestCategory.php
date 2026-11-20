<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTestCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lab_test_categories';

    protected $fillable = [
        'name',
        'icon',
        'description',
        'status',
    ];

    /**
     * Relationship: Category has many tests.
     */
    public function tests()
    {
        return $this->hasMany(LabTest::class, 'category_id');
    }

    /**
     * Relationship: Active tests in this category.
     */
    public function activeTests()
    {
        return $this->hasMany(LabTest::class, 'category_id')->where('status', 'active');
    }

    /**
     * Scope: Active categories only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
