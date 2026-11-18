<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class NewsletterSubscriber extends Model
{
    protected $appends = ['status'];

    protected $attributes = [
        'subscription_version' => 1,
    ];

    protected $fillable = [
        'email',
        'name',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
        'subscription_version',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'subscription_version' => 'integer',
    ];

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_active) {
            return 'subscribed';
        }

        return $this->unsubscribed_at ? 'unsubscribed' : 'inactive';
    }

    public function unsubscribeUrl(): string
    {
        return URL::signedRoute('newsletter.unsubscribe.confirm', [
            'newsletterSubscriber' => $this->getKey(),
            'version' => $this->subscriptionVersion(),
        ]);
    }

    public function subscriptionVersion(): int
    {
        return (int) $this->subscription_version;
    }
}
