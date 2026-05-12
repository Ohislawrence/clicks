<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSubscription extends Model
{
    protected $fillable = [
        'store_id',
        'store_plan_id',
        'billing_cycle',
        'amount',
        'payment_reference',
        'payment_gateway',
        'status',
        'period_start',
        'period_end',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the store this subscription belongs to.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the plan for this subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(StorePlan::class, 'store_plan_id');
    }

    /**
     * Check if subscription is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
