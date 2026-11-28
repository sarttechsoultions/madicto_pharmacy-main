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
        'cut_price',
        'discount_percentage',
        'discount_price',
        'preparation_instructions',
        'sample_type',
        'report_delivery_time',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
        'cut_price' => 'float',
        'discount_percentage' => 'integer',
        'discount_price' => 'float',
    ];

    /**
     * Accessor: Calculate discount percentage OFF.
     */
    public function getCalculatedOffAttribute(): int
    {
        if (!empty($this->discount_percentage) && $this->discount_percentage > 0) {
            return (int)$this->discount_percentage;
        }

        $cutPrice = (float)($this->cut_price ?: 0);
        $sellingPrice = (float)($this->price ?: 0);

        if ($cutPrice > $sellingPrice && $cutPrice > 0) {
            return (int)round((($cutPrice - $sellingPrice) / $cutPrice) * 100);
        }

        // Fallback for legacy discount_price: if price > discount_price
        if ($this->price && $this->discount_price && $this->price > $this->discount_price) {
            return (int)round((($this->price - $this->discount_price) / $this->price) * 100);
        }

        return 0;
    }

    /**
     * Accessor: Actual Selling / Offer Price (₹).
     */
    public function getSellingPriceAttribute(): float
    {
        if ($this->cut_price && $this->cut_price > $this->price) {
            return (float)$this->price;
        }
        if ($this->discount_price && $this->discount_price < $this->price && $this->discount_price > 0) {
            return (float)$this->discount_price;
        }
        return (float)($this->price ?: 0);
    }

    /**
     * Accessor: Original MRP / Strike-through Cut Price (₹).
     */
    public function getOriginalPriceAttribute(): ?float
    {
        if ($this->cut_price && $this->cut_price > $this->price) {
            return (float)$this->cut_price;
        }
        if ($this->discount_price && $this->discount_price < $this->price && $this->discount_price > 0) {
            return (float)$this->price;
        }
        return null;
    }

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
     * Relationship: Test has many bookings.
     */
    public function bookings()
    {
        return $this->hasMany(LabTestBooking::class, 'lab_test_id');
    }

    /**
     * Scope: Active tests only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
