<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class)->orderBy('sort_order')->orderBy('name');
    }

    public function activeStates(): HasMany
    {
        return $this->hasMany(State::class)->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
