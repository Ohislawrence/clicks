<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrafficSource extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'url',
        'followers',
        'monthly_visitors',
        'description',
        'is_verified',
        'is_primary',
    ];

    protected $casts = [
        'followers' => 'integer',
        'monthly_visitors' => 'integer',
        'is_verified' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted follower count (e.g., 10K, 1.5M)
     */
    public function getFormattedFollowersAttribute(): string
    {
        if (!$this->followers) {
            return 'N/A';
        }

        if ($this->followers >= 1000000) {
            return number_format($this->followers / 1000000, 1) . 'M';
        }

        if ($this->followers >= 1000) {
            return number_format($this->followers / 1000, 1) . 'K';
        }

        return number_format($this->followers);
    }

    /**
     * Get platform icon name
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'instagram' => 'instagram',
            'tiktok' => 'tiktok',
            'youtube' => 'youtube',
            'twitter' => 'twitter',
            'facebook' => 'facebook',
            'website', 'blog' => 'globe',
            default => 'link',
        };
    }
}
