<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTestBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lab_test_bookings';

    protected $fillable = [
        'booking_id',
        'user_id',
        'lab_test_id',
        'lab_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address',
        'city',
        'state',
        'pincode',
        'booking_date',
        'booking_time',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'sample_collection_type',
        'additional_instructions',
        'cancellation_reason',
        'report_file',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'booking_date' => 'date:Y-m-d',
    ];

    protected $appends = [
        'status_badge',
        'formatted_date',
        'full_address',
    ];

    /**
     * Relationship: Booking belongs to a Customer / User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Booking belongs to a primary LabTest.
     */
    public function labTest()
    {
        return $this->belongsTo(LabTest::class, 'lab_test_id');
    }

    /**
     * Relationship: Booking belongs to a Laboratory.
     */
    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    /**
     * Relationship: Booking has items (individual tests).
     */
    public function items()
    {
        return $this->hasMany(LabTestBookingItem::class, 'booking_id');
    }

    /**
     * Status badge accessor for UI.
     */
    public function getStatusBadgeAttribute(): array
    {
        $status = $this->status ?? 'Pending';
        $styles = [
            'Pending' => [
                'bg' => '#fef3c7',
                'color' => '#92400e',
                'border' => '#fde68a',
                'icon' => 'fa-clock',
            ],
            'Confirmed' => [
                'bg' => '#e0e7ff',
                'color' => '#3730a3',
                'border' => '#c7d2fe',
                'icon' => 'fa-circle-check',
            ],
            'Sample Collection Scheduled' => [
                'bg' => '#dbeafe',
                'color' => '#1e40af',
                'border' => '#bfdbfe',
                'icon' => 'fa-calendar-check',
            ],
            'Sample Collected' => [
                'bg' => '#ede9fe',
                'color' => '#5b21b6',
                'border' => '#ddd6fe',
                'icon' => 'fa-vial',
            ],
            'Completed' => [
                'bg' => '#dcfce7',
                'color' => '#166534',
                'border' => '#bbf7d0',
                'icon' => 'fa-clipboard-check',
            ],
            'Cancelled' => [
                'bg' => '#fee2e2',
                'color' => '#991b1b',
                'border' => '#fecaca',
                'icon' => 'fa-circle-xmark',
            ],
        ];

        return $styles[$status] ?? [
            'bg' => '#f3f4f6',
            'color' => '#374151',
            'border' => '#e5e7eb',
            'icon' => 'fa-circle-dot',
        ];
    }

    public function getFormattedDateAttribute(): string
    {
        if (!$this->booking_date) {
            return '';
        }
        return \Carbon\Carbon::parse($this->booking_date)->format('d M, Y');
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->pincode ? "PIN: {$this->pincode}" : null,
        ]);
        return implode(', ', $parts);
    }

    /**
     * Scope: Search bookings by ID, customer name, phone, or test name.
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        $term = trim($search);
        return $query->where(function ($q) use ($term) {
            $q->where('booking_id', 'LIKE', "%{$term}%")
              ->orWhere('customer_name', 'LIKE', "%{$term}%")
              ->orWhere('customer_phone', 'LIKE', "%{$term}%")
              ->orWhere('customer_email', 'LIKE', "%{$term}%")
              ->orWhere('city', 'LIKE', "%{$term}%")
              ->orWhereHas('labTest', function ($tq) use ($term) {
                  $tq->where('test_name', 'LIKE', "%{$term}%");
              })
              ->orWhereHas('lab', function ($lq) use ($term) {
                  $lq->where('name', 'LIKE', "%{$term}%");
              });
        });
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeStatus($query, $status)
    {
        if (empty($status) || $status === 'all') {
            return $query;
        }
        return $query->where('status', $status);
    }
}
