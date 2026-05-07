<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferCreative extends Model
{
    protected $fillable = [
        'offer_id',
        'type',
        'name',
        'file_path',
        'content',
        'width',
        'height',
        'size',
        'format',
        'clicks_count',
        'is_active',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'clicks_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }
        return asset('storage/' . $this->file_path);
    }

    public function getDimensionsAttribute(): ?string
    {
        if ($this->width && $this->height) {
            return "{$this->width}x{$this->height}";
        }
        return null;
    }
}
