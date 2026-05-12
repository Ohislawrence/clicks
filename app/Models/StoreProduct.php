<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'sku',
        'stock_quantity',
        'images',
        'is_featured',
        'is_active',
        'sort_order',
        'product_type',
        'delivery_fees',
        'tags',
        'download_file',
        'download_url',
        'download_expiry_hours',
        'max_downloads',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'delivery_fees' => 'array',
        'tags' => 'array',
        'download_expiry_hours' => 'integer',
        'max_downloads' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(StoreCategory::class, 'store_product_category');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(StoreProductVariant::class)->orderBy('sort_order');
    }

    public function offer(): HasOne
    {
        return $this->hasOne(Offer::class, 'store_product_id');
    }

    public function hasOffer(): bool
    {
        return $this->offer()->exists();
    }

    public function hasDiscount(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return (int) round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity === null || $this->stock_quantity > 0;
    }

    public function isDigital(): bool
    {
        return $this->product_type === 'digital';
    }

    public function hasDownload(): bool
    {
        return $this->isDigital() && ($this->download_file || $this->download_url);
    }
}
