<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorePlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'store_type',
        'product_limit',
        'monthly_price',
        'yearly_price',
        'yearly_discount_percent',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'yearly_discount_percent' => 'integer',
        'product_limit' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get stores using this plan.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    /**
     * Get subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(StoreSubscription::class);
    }

    /**
     * Get the calculated yearly price with discount.
     */
    public function getYearlyPriceWithDiscountAttribute(): float
    {
        return $this->yearly_price;
    }

    /**
     * Get the yearly savings amount.
     */
    public function getYearlySavingsAttribute(): float
    {
        return ($this->monthly_price * 12) - $this->yearly_price;
    }
}
