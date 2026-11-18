<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketIndex extends Model
{
    protected $appends = ['change', 'is_positive'];

    protected $fillable = [
        'name',
        'symbol',
        'value',
        'change_value',
        'change_percent',
        'as_of',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'change_value' => 'decimal:4',
        'change_percent' => 'decimal:4',
        'as_of' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getChangeAttribute(): float
    {
        return abs((float) ($this->attributes['change_value'] ?? 0));
    }

    public function getIsPositiveAttribute(): bool
    {
        return (float) ($this->attributes['change_value'] ?? 0) >= 0;
    }
}