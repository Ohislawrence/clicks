<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreDiscountCode extends Model
{
    protected $fillable = [
        'store_id',
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'max_uses',
        'uses_count',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Check if discount code is currently valid.
     */
    public function isValid(float $orderAmount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }

        if ($this->min_order_amount !== null && $orderAmount < $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given order total.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $orderTotal * ($this->value / 100);
            if ($this->max_discount_amount !== null) {
                $discount = min($discount, $this->max_discount_amount);
            }
            return round($discount, 2);
        }

        // fixed
        return min(round($this->value, 2), $orderTotal);
    }

    /**
     * Increment the usage counter.
     */
    public function recordUsage(): void
    {
        $this->increment('uses_count');
    }
}
