<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreTheme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'config',
        'store_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get stores using this theme.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }
}
