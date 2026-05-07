<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkRotationGroup extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'affiliate_id',
        'offer_id',
        'name',
        'description',
        'rotation_strategy',
        'is_active',
        'enable_split_test',
        'split_test_duration_days',
        'split_test_started_at',
        'split_test_ends_at',
        'total_clicks',
        'total_conversions',
        'total_revenue',
        'group_cr',
        'group_epc',
        'auto_optimize',
        'optimization_threshold_clicks',
        'last_optimized_at',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'enable_split_test' => 'boolean',
        'split_test_duration_days' => 'integer',
        'split_test_started_at' => 'datetime',
        'split_test_ends_at' => 'datetime',
        'total_clicks' => 'integer',
        'total_conversions' => 'integer',
        'total_revenue' => 'decimal:2',
        'group_cr' => 'decimal:2',
        'group_epc' => 'decimal:2',
        'auto_optimize' => 'boolean',
        'optimization_threshold_clicks' => 'integer',
        'last_optimized_at' => 'datetime',
    ];
    
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
    
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
    
    public function links(): HasMany
    {
        return $this->hasMany(AffiliateLink::class, 'rotation_group_id');
    }
}
