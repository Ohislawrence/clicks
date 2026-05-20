<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSaleTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'store_id',
        'advertiser_id',
        'affiliate_id',
        'gross_amount',
        'discount_amount',
        'net_amount',
        'platform_fee_amount',
        'affiliate_commission_amount',
        'advertiser_net_amount',
        'platform_reference',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'affiliate_commission_amount' => 'decimal:2',
        'advertiser_net_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(StoreOrder::class, 'order_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
}
