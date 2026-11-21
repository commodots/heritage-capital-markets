<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketQuote extends Model
{
    protected $fillable = [
        'symbol',
        'name',
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
}
