<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    protected $fillable = [
        'affiliate_link_id',
        'offer_id',
        'affiliate_id',
        'click_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country_code',
        'country_name',
        'city',
        'device_type',
        'browser',
        'os',
        'is_valid',
        'fraud_reason',
        'is_converted',
        'converted_at',
        // Fraud Detection
        'quality_score',
        'fraud_indicators',
        'is_vpn',
        'is_proxy',
        'is_datacenter',
        'is_bot_detected',
        'is_suspicious_pattern',
        'device_fingerprint',
        'device_details',
        'click_velocity_score',
        'conversion_time_score',
        'engagement_score',
        'risk_level',
        'risk_reasons',
        'needs_manual_review',
        'reviewed_at',
        'reviewed_by',
        'opened_whatsapp',
        'whatsapp_opened_at',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'is_converted' => 'boolean',
        'converted_at' => 'datetime',
        'opened_whatsapp' => 'boolean',
        'whatsapp_opened_at' => 'datetime',
        // Fraud Detection
        'quality_score' => 'integer',
        'fraud_indicators' => 'array',
        'is_vpn' => 'boolean',
        'is_proxy' => 'boolean',
        'is_datacenter' => 'boolean',
        'is_bot_detected' => 'boolean',
        'is_suspicious_pattern' => 'boolean',
        'device_details' => 'array',
        'click_velocity_score' => 'integer',
        'conversion_time_score' => 'integer',
        'engagement_score' => 'integer',
        'needs_manual_review' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(Conversion::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
