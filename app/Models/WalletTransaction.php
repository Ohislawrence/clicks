<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'status',
        'reference',
        'offer_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /** True for deposit, offer_refund, and store_sale (money into wallet) */
    public function isCredit(): bool
    {
        return in_array($this->type, ['deposit', 'offer_refund', 'store_sale']);
    }

    /** True for offer_allocation, offer_topup, store_withdrawal, store_refund_clawback (money out of wallet) */
    public function isDebit(): bool
    {
        return in_array($this->type, ['offer_allocation', 'offer_topup', 'store_withdrawal', 'store_refund_clawback']);
    }
}

