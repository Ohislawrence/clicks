<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'about_content',
        'logo',
        'email',
        'phone',
        'whatsapp_number',
        'address',
        'store_plan_id',
        'billing_cycle',
        'subscription_start_date',
        'subscription_end_date',
        'subscription_status',
        'subscription_payment_gateway',
        'expiry_reminder_sent',
        'store_theme_id',
        'theme_customization',
        'payment_method',
        'payment_provider',
        'payment_link',
        'payment_public_key',
        'payment_secret_key',
        'payment_webhook_secret',
        'meta_title',
        'meta_description',
        'meta_image',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'theme_customization' => 'array',
        'is_active' => 'boolean',
        'expiry_reminder_sent' => 'boolean',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'published_at' => 'datetime',
    ];

    protected $hidden = [
        'payment_secret_key',
        'payment_webhook_secret',
    ];

    /**
     * Get the owner (advertiser) of the store.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(StorePlan::class, 'store_plan_id');
    }

    /**
     * Get the theme.
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(StoreTheme::class, 'store_theme_id');
    }

    /**
     * Get store products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(StoreProduct::class);
    }

    /**
     * Get store categories.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(StoreCategory::class)->orderBy('sort_order');
    }

    /**
     * Get store discount codes.
     */
    public function discountCodes(): HasMany
    {
        return $this->hasMany(StoreDiscountCode::class);
    }

    /**
     * Get store orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(StoreOrder::class);
    }

    /**
     * Get store subscriptions.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(StoreSubscription::class);
    }

    /**
     * Encrypt payment secret key before saving.
     */
    public function setPaymentSecretKeyAttribute($value): void
    {
        $this->attributes['payment_secret_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt payment secret key when retrieving.
     */
    public function getPaymentSecretKeyAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Encrypt payment public key before saving.
     */
    public function setPaymentPublicKeyAttribute($value): void
    {
        $this->attributes['payment_public_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt payment public key when retrieving.
     */
    public function getPaymentPublicKeyAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Encrypt payment webhook secret before saving.
     */
    public function setPaymentWebhookSecretAttribute($value): void
    {
        $this->attributes['payment_webhook_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt payment webhook secret when retrieving.
     */
    public function getPaymentWebhookSecretAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Get the webhook secret for verification.
     * For Paystack, uses payment_secret_key if webhook_secret not set.
     * For Flutterwave, requires separate webhook secret hash.
     */
    public function getWebhookSecretAttribute(): ?string
    {
        // If webhook secret is explicitly set, use it
        if ($this->payment_webhook_secret) {
            return $this->payment_webhook_secret;
        }

        // For Paystack, fall back to secret key (same key for API and webhooks)
        if ($this->payment_provider === 'paystack') {
            return $this->payment_secret_key;
        }

        // For Flutterwave, webhook secret must be explicitly set
        return null;
    }

    /**
     * Check if store subscription is active.
     */
    public function isSubscriptionActive(): bool
    {
        return $this->subscription_status === 'active'
            && $this->subscription_end_date >= now()->toDateString();
    }

    /**
     * Check if subscription is expiring soon (within 7 days).
     */
    public function isSubscriptionExpiringSoon(): bool
    {
        return $this->subscription_end_date <= now()->addDays(7)->toDateString()
            && $this->subscription_end_date >= now()->toDateString();
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        return now()->diffInDays($this->subscription_end_date, false);
    }
}
