<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AffiliateLink extends Model
{
    protected $fillable = [
        'affiliate_id',
        'offer_id',
        'tracking_code',
        'custom_slug',
        'is_active',
        'click_count',
        'conversion_count',
        'total_earnings',
        // Discount Settings
        'discount_enabled',
        'discount_percentage',
        // Smart Link Rotation
        'enable_rotation',
        'rotation_type',
        'rotation_weight',
        'rotation_priority',
        'rotation_group_id',
        // Geo-Targeting
        'enable_geo_targeting',
        'allowed_countries',
        'blocked_countries',
        'allowed_regions',
        'blocked_regions',
        'allowed_cities',
        'blocked_cities',
        // Device Targeting
        'enable_device_targeting',
        'allowed_devices',
        'allowed_os',
        'allowed_browsers',
        // Time-based Targeting
        'enable_schedule',
        'active_start_time',
        'active_end_time',
        'active_days',
        // Performance Tracking
        'rotation_clicks',
        'rotation_conversions',
        'rotation_cr',
        'last_rotated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'click_count' => 'integer',
        'conversion_count' => 'integer',
        'total_earnings' => 'decimal:2',
        // Smart Links
        'enable_rotation' => 'boolean',
        'rotation_weight' => 'integer',
        'rotation_priority' => 'integer',
        'allowed_countries' => 'array',
        'blocked_countries' => 'array',
        'allowed_regions' => 'array',
        'blocked_regions' => 'array',
        'allowed_cities' => 'array',
        'blocked_cities' => 'array',
        'enable_geo_targeting' => 'boolean',
        'enable_device_targeting' => 'boolean',
        'allowed_devices' => 'array',
        'allowed_os' => 'array',
        'allowed_browsers' => 'array',
        'enable_schedule' => 'boolean',
        'active_days' => 'array',
        'rotation_clicks' => 'integer',
        'rotation_conversions' => 'integer',
        'rotation_cr' => 'decimal:2',
        'last_rotated_at' => 'datetime',
    ];

    protected $appends = [
        'tracking_url',
        'conversion_rate',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($link) {
            if (empty($link->tracking_code)) {
                $link->tracking_code = (string) Str::uuid();
            }
        });
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(Conversion::class);
    }

    public function rotationGroup(): BelongsTo
    {
        return $this->belongsTo(LinkRotationGroup::class, 'rotation_group_id');
    }

    public function getTrackingUrlAttribute(): string
    {
        $url = url('/track/' . $this->tracking_code);

        // Add discount parameter if discount is enabled
        if ($this->discount_enabled && $this->discount_percentage) {
            $url .= '?discount=' . $this->discount_percentage;
        }

        return $url;
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->click_count == 0) {
            return 0;
        }
        return ($this->conversion_count / $this->click_count) * 100;
    }

    /**
     * Generate WhatsApp Click-to-Chat URL with embedded tracking
     */
    public function generateWhatsAppUrl(?string $customMessage = null): array
    {
        // Create a click record for tracking
        $click = Click::create([
            'affiliate_link_id' => $this->id,
            'offer_id' => $this->offer_id,
            'affiliate_id' => $this->affiliate_id,
            'click_id' => 'CLK-' . strtoupper(Str::random(9)),
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent() ?? 'Unknown',
            'referer' => request()->header('referer'),
            'opened_whatsapp' => true,
            'whatsapp_opened_at' => now(),
        ]);

        // Build the WhatsApp message
        $message = $customMessage ?? $this->offer->whatsapp_message_template;

        // Replace template variables
        $message = str_replace([
            '{offer_name}',
            '{business_name}',
            '{click_id}',
        ], [
            $this->offer->name,
            $this->offer->advertiser->name ?? 'Business',
            $click->click_id,
        ], $message);

        // If no template, use default
        if (empty($message)) {
            $message = "Hi {$this->offer->advertiser->name}, I'm interested in {$this->offer->name}. Ref: {$click->click_id}";
        }

        // Generate WhatsApp URL
        $whatsappNumber = preg_replace('/[^0-9]/', '', $this->offer->whatsapp_number);
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        return [
            'url' => $whatsappUrl,
            'click_id' => $click->click_id,
            'message_preview' => $message,
            'phone' => $this->offer->whatsapp_number,
        ];
    }
}
