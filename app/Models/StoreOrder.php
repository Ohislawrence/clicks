<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreOrder extends Model
{
    protected $fillable = [
        'store_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'items',
        'subtotal',
        'shipping_fee',
        'total',
        'payment_reference',
        'payment_status',
        'payment_mode',
        'platform_fee_amount',
        'affiliate_commission_amount',
        'advertiser_net_amount',
        'affiliate_id',
        'affiliate_link_id',
        'conversion_id',
        'paid_at',
        'fulfillment_status',
        'notes',
        'refund_status',
        'refund_requested_at',
        'refund_approved_at',
        'refund_note',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'affiliate_commission_amount' => 'decimal:2',
        'advertiser_net_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refund_requested_at' => 'datetime',
        'refund_approved_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class, 'affiliate_link_id');
    }

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(Conversion::class, 'conversion_id');
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPlatformManaged(): bool
    {
        return $this->payment_mode === 'platform';
    }

    /**
     * An order is refundable if it's paid, platform-managed, and no refund is already in progress.
     */
    public function isRefundable(): bool
    {
        return $this->isPaid()
            && $this->isPlatformManaged()
            && $this->refund_status === 'none';
    }
}
