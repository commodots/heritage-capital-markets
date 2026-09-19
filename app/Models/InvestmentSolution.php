<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentSolution extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'featured_image',
        'button_label',
        'button_url',
        'meta_title',
        'meta_description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}