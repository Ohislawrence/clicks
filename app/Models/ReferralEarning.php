<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralEarning extends Model
{
    protected $fillable = [
        'parent_affiliate_id',
        'sub_affiliate_id',
        'commission_id',
        'amount',
        'is_capped',
        'cap_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_capped' => 'boolean',
    ];

    /**
     * Parent affiliate who earned this referral commission
     */
    public function parentAffiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_affiliate_id');
    }

    /**
     * Sub-affiliate who generated the conversion
     */
    public function subAffiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sub_affiliate_id');
    }

    /**
     * Commission that triggered this referral earning
     */
    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    /**
     * Scope to filter active (non-capped) earnings
     */
    public function scopeActive($query)
    {
        return $query->where('is_capped', false);
    }

    /**
     * Scope to filter capped earnings
     */
    public function scopeCapped($query)
    {
        return $query->where('is_capped', true);
    }
}
