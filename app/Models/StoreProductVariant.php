<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreProductVariant extends Model
{
    protected $fillable = [
        'store_product_id',
        'name',
        'options',
        'price',
        'compare_at_price',
        'stock_quantity',
        'sku',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class, 'store_product_id');
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity === null || $this->stock_quantity > 0;
    }
}
