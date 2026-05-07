<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'affiliate_code',
        'phone',
        'company_name',
        'bio',
        'website',
        'country',
        'payout_frequency',
        'payment_details',
        'instagram_handle',
        'tiktok_handle',
        'youtube_channel',
        'twitter_handle',
        'follower_count',
        'is_verified',
        'is_active',
        'balance',
        'pending_balance',
        'lifetime_earnings',
        'advertiser_balance',
        'tier',
        'tier_commission_bonus',
        'total_clicks',
        'total_conversions',
        'conversion_rate',
        'tier_updated_at',
        'parent_affiliate_id',
        'referral_code',
        'referral_count',
        'referral_earnings',
        'referral_cap_type',
        'referral_cap_amount',
        'referral_cap_months',
        'referral_started_at',
        'referral_cap_reached_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'payment_details' => 'array',
            'balance' => 'decimal:2',
            'pending_balance' => 'decimal:2',
            'lifetime_earnings' => 'decimal:2',
            'advertiser_balance' => 'decimal:2',
            'follower_count' => 'integer',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'verified_at' => 'datetime',
            'tier_commission_bonus' => 'decimal:2',
            'total_clicks' => 'integer',
            'total_conversions' => 'integer',
            'conversion_rate' => 'decimal:2',
            'tier_updated_at' => 'datetime',
            'referral_count' => 'integer',
            'referral_earnings' => 'decimal:2',
            'referral_cap_amount' => 'decimal:2',
            'referral_cap_months' => 'integer',
            'referral_started_at' => 'datetime',
            'referral_cap_reached_at' => 'datetime',
        ];
    }

    // Affiliate Relationships
    public function parentAffiliate()
    {
        return $this->belongsTo(User::class, 'parent_affiliate_id');
    }

    public function subAffiliates()
    {
        return $this->hasMany(User::class, 'parent_affiliate_id');
    }
    public function affiliateLinks()
    {
        return $this->hasMany(AffiliateLink::class, 'affiliate_id');
    }

    public function trafficSources()
    {
        return $this->hasMany(TrafficSource::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class, 'affiliate_id');
    }

    public function conversions()
    {
        return $this->hasMany(Conversion::class, 'affiliate_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'affiliate_id');
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class, 'affiliate_id');
    }

    public function offerAccessRequests()
    {
        return $this->hasMany(OfferAccessRequest::class, 'affiliate_id');
    }

    public function referralEarnings()
    {
        return $this->hasMany(ReferralEarning::class, 'parent_affiliate_id');
    }

    // Advertiser Relationships
    public function offers()
    {
        return $this->hasMany(Offer::class, 'advertiser_id');
    }

    // Referral Cap Methods

    /**
     * Check if referral commissions are still active (not capped)
     */
    public function isReferralActive(): bool
    {
        // If already marked as reached, it's not active
        if ($this->referral_cap_reached_at) {
            return false;
        }

        // If unlimited, always active
        if ($this->referral_cap_type === 'unlimited') {
            return true;
        }

        // Check amount cap
        if ($this->referral_cap_type === 'amount' || $this->referral_cap_type === 'both') {
            if ($this->referral_cap_amount && $this->referral_earnings >= $this->referral_cap_amount) {
                return false;
            }
        }

        // Check time cap
        if ($this->referral_cap_type === 'time' || $this->referral_cap_type === 'both') {
            if ($this->referral_started_at && $this->referral_cap_months) {
                $monthsActive = now()->diffInMonths($this->referral_started_at);
                if ($monthsActive >= $this->referral_cap_months) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get remaining referral cap amount
     */
    public function getRemainingReferralCap(): ?float
    {
        if ($this->referral_cap_type === 'unlimited') {
            return null;
        }

        if ($this->referral_cap_type === 'amount' || $this->referral_cap_type === 'both') {
            if ($this->referral_cap_amount) {
                $remaining = $this->referral_cap_amount - $this->referral_earnings;
                return max(0, $remaining);
            }
        }

        return null;
    }

    /**
     * Get remaining referral cap months
     */
    public function getRemainingReferralMonths(): ?int
    {
        if ($this->referral_cap_type === 'unlimited') {
            return null;
        }

        if ($this->referral_cap_type === 'time' || $this->referral_cap_type === 'both') {
            if ($this->referral_started_at && $this->referral_cap_months) {
                $monthsActive = now()->diffInMonths($this->referral_started_at);
                $remaining = $this->referral_cap_months - $monthsActive;
                return max(0, $remaining);
            }
        }

        return null;
    }

    /**
     * Check if referral cap has been reached
     */
    public function hasReachedReferralCap(): bool
    {
        return !$this->isReferralActive();
    }

    /**
     * Get referral cap progress percentage (0-100)
     */
    public function getReferralCapProgress(): array
    {
        $progress = [
            'amount_percentage' => 0,
            'time_percentage' => 0,
            'overall_percentage' => 0,
        ];

        if ($this->referral_cap_type === 'unlimited') {
            return $progress;
        }

        // Amount progress
        if ($this->referral_cap_type === 'amount' || $this->referral_cap_type === 'both') {
            if ($this->referral_cap_amount && $this->referral_cap_amount > 0) {
                $progress['amount_percentage'] = min(100, ($this->referral_earnings / $this->referral_cap_amount) * 100);
            }
        }

        // Time progress
        if ($this->referral_cap_type === 'time' || $this->referral_cap_type === 'both') {
            if ($this->referral_started_at && $this->referral_cap_months) {
                $monthsActive = now()->diffInMonths($this->referral_started_at);
                $progress['time_percentage'] = min(100, ($monthsActive / $this->referral_cap_months) * 100);
            }
        }

        // Overall progress (average of applicable caps)
        $applicableCaps = [];
        if ($progress['amount_percentage'] > 0) $applicableCaps[] = $progress['amount_percentage'];
        if ($progress['time_percentage'] > 0) $applicableCaps[] = $progress['time_percentage'];

        if (!empty($applicableCaps)) {
            $progress['overall_percentage'] = array_sum($applicableCaps) / count($applicableCaps);
        }

        return $progress;
    }

    // Helper Methods
    public function isAffiliate(): bool
    {
        return $this->hasRole('affiliate');
    }

    public function isAdvertiser(): bool
    {
        return $this->hasRole('advertiser');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Boot method to auto-generate affiliate code for new affiliates
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Generate affiliate code when user is created and has affiliate role
            if (!$user->affiliate_code && $user->hasRole('affiliate')) {
                $user->affiliate_code = self::generateUniqueAffiliateCode();
                $user->saveQuietly(); // Save without triggering events
            }
        });
    }

    /**
     * Generate a unique affiliate code (CI + 5 digits)
     */
    public static function generateUniqueAffiliateCode(): string
    {
        do {
            // Generate code: CI + 5 random digits
            $code = 'CI' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('affiliate_code', $code)->exists());

        return $code;
    }

    /**
     * Get the display name for advertiser views (affiliate code or ID)
     */
    public function getAffiliateDisplayName(): string
    {
        return $this->affiliate_code ?? 'Affiliate #' . $this->id;
    }
}
