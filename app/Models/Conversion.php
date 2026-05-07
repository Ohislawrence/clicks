<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversion extends Model
{
    protected $fillable = [
        'click_id',
        'affiliate_id',
        'offer_id',
        'affiliate_link_id',
        'conversion_id',
        'transaction_id',
        'conversion_value',
        'conversion_amount',
        'advertiser_payout',
        'platform_margin',
        'commission_amount',
        'commission_model',
        'status',
        'is_manual',
        'manual_notes',
        'rejection_reason',
        'tracking_method',
        'postback_data',
        'ip_address',
        'approved_at',
        'paid_at',
        'postback_sent_at',
        'postback_response',
    ];

    protected $casts = [
        'conversion_value' => 'decimal:2',
        'conversion_amount' => 'decimal:2',
        'advertiser_payout' => 'decimal:2',
        'platform_margin' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'is_manual' => 'boolean',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'postback_sent_at' => 'datetime',
    ];

    public function click(): BelongsTo
    {
        return $this->belongsTo(Click::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
