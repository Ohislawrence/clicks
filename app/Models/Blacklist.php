<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blacklist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'value',
        'match_type',
        'scope',
        'scope_id',
        'offer_id',
        'is_active',
        'severity',
        'action',
        'quality_penalty',
        'reason',
        'notes',
        'expires_at',
        'hit_count',
        'last_hit_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'quality_penalty' => 'integer',
        'hit_count' => 'integer',
        'expires_at' => 'datetime',
        'last_hit_at' => 'datetime',
    ];

    /**
     * Check if blacklist entry has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if blacklist entry is currently active
     */
    public function isCurrentlyActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Increment hit counter
     */
    public function recordHit(): void
    {
        $this->increment('hit_count');
        $this->update(['last_hit_at' => now()]);
    }

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeEntity(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scope_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeGlobal($query)
    {
        return $query->where('scope', 'global');
    }

    public function scopeForOffer($query, int $offerId)
    {
        return $query->where(function ($q) use ($offerId) {
            $q->where('scope', 'global')
                ->orWhere(function ($q) use ($offerId) {
                    $q->where('scope', 'offer')->where('offer_id', $offerId);
                });
        });
    }

    public function scopeForAffiliate($query, int $affiliateId)
    {
        return $query->where(function ($q) use ($affiliateId) {
            $q->where('scope', 'global')
                ->orWhere(function ($q) use ($affiliateId) {
                    $q->where('scope', 'affiliate')->where('scope_id', $affiliateId);
                });
        });
    }
}
