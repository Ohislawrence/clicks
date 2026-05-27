<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Offer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'advertiser_id',
        'offer_channel',
        'network_name',
        'network_offer_id',
        'store_product_id',
        'category_id',
        'name',
        'slug',
        'description',
        'terms_and_conditions',
        'preview_url',
        'thumbnail',
        'commission_model',
        'commission_rate',
        'pricing_model',
        'advertiser_payout',
        'affiliate_payout',
        'platform_spread_percentage',
        'cookie_duration',
        'access_type',
        'is_active',
        'is_featured',
        'approval_status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'postback_url',
        'postback_secret',
        'conversion_pixel',
        'enable_whatsapp_tracking',
        'whatsapp_number',
        'whatsapp_message_template',
        'total_clicks',
        'total_conversions',
        'total_revenue',
        'daily_conversion_cap',
        'monthly_conversion_cap',
        'total_conversion_cap',
        'budget_limit',
        'spent_budget',
        'expected_sales',
        'product_cost',
        'minimum_wallet_required',
        'today_conversions',
        'month_conversions',
        'last_cap_reset_date',
        'auto_pause_on_cap',
        'pause_reason',
        'revshare_type',
        'revshare_recurring_duration',
        'revshare_recurring_unit',
        'offer_url',
        'product_image',
        'cpalead_offer_id',
        'is_cpalead',
        'target_countries',
        'target_devices',
        'target_os',
        'require_unique_ip',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'advertiser_payout' => 'decimal:2',
        'affiliate_payout' => 'decimal:2',
        'platform_spread_percentage' => 'decimal:2',
        'cookie_duration' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'enable_whatsapp_tracking' => 'boolean',
        'total_clicks' => 'integer',
        'total_conversions' => 'integer',
        'total_revenue' => 'decimal:2',
        'daily_conversion_cap' => 'integer',
        'monthly_conversion_cap' => 'integer',
        'total_conversion_cap' => 'integer',
        'budget_limit' => 'decimal:2',
        'spent_budget' => 'decimal:2',
        'expected_sales' => 'integer',
        'product_cost' => 'decimal:2',
        'minimum_wallet_required' => 'decimal:2',
        'today_conversions' => 'integer',
        'month_conversions' => 'integer',
        'last_cap_reset_date' => 'date',
        'auto_pause_on_cap' => 'boolean',
        'reviewed_at' => 'datetime',
        'revshare_recurring_duration' => 'integer',
        'target_countries' => 'array',
        'target_devices' => 'array',
        'target_os' => 'array',
        'require_unique_ip' => 'boolean',
        'is_cpalead' => 'boolean',
    ];

    protected $appends = [
        'conversion_rate',
        'platform_margin',
        'margin_percentage',
    ];

    /**
     * Determine whether a visitor matches this offer's targeting rules.
     * Returns true if the visitor is allowed, false if they should be blocked.
     */
    public function isTargetedTo(?string $countryCode, ?string $device, ?string $os): bool
    {
        if (!empty($this->target_countries) && $countryCode) {
            if (!in_array(strtoupper($countryCode), $this->target_countries)) {
                return false;
            }
        }

        if (!empty($this->target_devices) && $device) {
            if (!in_array(strtolower($device), $this->target_devices)) {
                return false;
            }
        }

        if (!empty($this->target_os) && $os) {
            if (!in_array(strtolower($os), $this->target_os)) {
                return false;
            }
        }

        return true;
    }

    protected static function booted(): void
    {
        static::creating(function (Offer $offer) {
            if (empty($offer->postback_secret)) {
                $offer->postback_secret = Str::random(48);
            }
        });
    }

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    public function storeProduct(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class, 'store_product_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(OfferCategory::class, 'category_id');
    }

    public function affiliateLinks(): HasMany
    {
        return $this->hasMany(AffiliateLink::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(Conversion::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function accessRequests(): HasMany
    {
        return $this->hasMany(OfferAccessRequest::class);
    }

    public function creatives(): HasMany
    {
        return $this->hasMany(OfferCreative::class);
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->total_clicks == 0) {
            return 0;
        }
        return ($this->total_conversions / $this->total_clicks) * 100;
    }

    /**
     * Calculate platform margin (spread between advertiser payout and affiliate payout)
     */
    public function getPlatformMarginAttribute(): float
    {
        if (!$this->advertiser_payout || !$this->affiliate_payout) {
            return 0;
        }
        return $this->advertiser_payout - $this->affiliate_payout;
    }

    /**
     * Calculate margin percentage
     */
    public function getMarginPercentageAttribute(): float
    {
        if (!$this->advertiser_payout || $this->advertiser_payout == 0) {
            return 0;
        }
        return ($this->platform_margin / $this->advertiser_payout) * 100;
    }

    /**
     * Calculate and set payouts based on platform spread percentage
     * Called by admin when approving offer
     */
    public function calculatePayoutsFromSpread(): void
    {
        if (!$this->platform_spread_percentage || !$this->commission_rate) {
            return;
        }

        // Advertiser payout is the total amount the advertiser is willing to pay
        $this->advertiser_payout = $this->commission_rate;

        // Affiliate payout is what remains after the platform takes its spread
        if ($this->commission_model === 'revshare') {
            // For revshare, the spread is usually a cut of the commission rate percentage
            $spreadAmount = ($this->commission_rate * $this->platform_spread_percentage) / 100;
            $this->affiliate_payout = max(0, $this->commission_rate - $spreadAmount);
        } else {
            // For fixed amounts (CPA/CPL)
            $spreadAmount = ($this->commission_rate * $this->platform_spread_percentage) / 100;
            $this->affiliate_payout = max(0, $this->commission_rate - $spreadAmount);
        }
    }

    /**
     * Get the effective commission amount for affiliates
     */
    public function getEffectiveCommission(float $conversionValue = 0): float
    {
        // If payouts are set (admin approved with spread), use affiliate_payout
        if ($this->affiliate_payout) {
            // For revshare, affiliate_payout stores a percentage rate (e.g. 30 = 30%)
            if ($this->commission_model === 'revshare') {
                return ($conversionValue * $this->affiliate_payout) / 100;
            }
            return $this->affiliate_payout;
        }

        // Legacy: calculate based on commission model
        if ($this->commission_model === 'cpa' || $this->commission_model === 'cpl') {
            return $this->commission_rate;
        } elseif ($this->commission_model === 'revshare') {
            return ($conversionValue * $this->commission_rate) / 100;
        }

        return 0;
    }

    /**
     * Get the effective advertiser payout
     */
    public function getEffectiveAdvertiserPayout(float $conversionValue = 0): float
    {
        // If advertiser_payout is set (admin approved with spread), use it
        if ($this->advertiser_payout) {
            // For revshare, advertiser_payout stores a percentage rate (e.g. 33 = 33%)
            if ($this->commission_model === 'revshare') {
                return ($conversionValue * $this->advertiser_payout) / 100;
            }
            return $this->advertiser_payout;
        }

        // Legacy: advertiser pays same as affiliate commission (no platform margin)
        return $this->getEffectiveCommission($conversionValue);
    }

    /**
     * Scope to only get approved offers
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope to only get pending offers
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope to only get rejected offers
     */
    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    /**
     * Check whether the advertiser's wallet balance is below the offer's minimum.
     * Requires the `advertiser` relation to be loaded.
     */
    public function advertiserHasInsufficientBalance(): bool
    {
        if (!$this->minimum_wallet_required || $this->minimum_wallet_required <= 0) {
            return false;
        }

        $balance = $this->advertiser?->advertiser_balance ?? 0;
        return $balance < $this->minimum_wallet_required;
    }

    /**
     * Find the best fallback offer for traffic diversion:
     * same category, approved, active, and advertiser wallet is sufficient.
     */
    public function findFallbackOffer(): ?self
    {
        return self::join('users', 'users.id', '=', 'offers.advertiser_id')
            ->select('offers.*')
            ->where('offers.category_id', $this->category_id)
            ->where('offers.id', '!=', $this->id)
            ->where('offers.approval_status', 'approved')
            ->where('offers.is_active', true)
            ->where(function ($q) {
                // Advertiser balance meets the offer's own minimum, or no minimum is set
                $q->whereNull('offers.minimum_wallet_required')
                  ->orWhere('offers.minimum_wallet_required', '<=', 0)
                  ->orWhereRaw('users.advertiser_balance >= offers.minimum_wallet_required');
            })
            ->inRandomOrder()
            ->first();
    }
}

