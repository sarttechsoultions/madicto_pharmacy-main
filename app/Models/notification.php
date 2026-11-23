<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $table = 'noties';

    protected $fillable = [
        'user_id',
        'target_role',
        'title',
        'message',
        'type',
        'screen',
        'reference_id',
        'data',
        'image',
        'send_to',
        'status',
        'is_read',
        'read_at',
        'total_users',
        'success_count',
        'failed_count',
    ];

    protected $casts = [
        'data'       => 'array',
        'is_read'    => 'boolean',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Optional specific user recipient
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for mobile app customer feed:
     * Broadcasts for users/all OR user's own notifications
     */
    public function scopeForCustomerFeed($query, ?int $userId = null)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($sub) {
                $sub->whereNull('user_id')
                    ->whereIn('target_role', ['all', 'user']);
            });

            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        });
    }

    /**
     * Scope for admin notification center
     */
    public function scopeForAdminFeed($query)
    {
        return $query->whereIn('target_role', ['admin', 'all']);
    }
}
