<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use App\Models\MarketIndex;
use App\Models\MarketQuote;
use App\Support\AdminFeatures;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'status' => $request->session()->get('status'),
            ],
            'auth' => fn () => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'is_super_admin' => (bool) $request->user()->is_super_admin,
                    'permissions' => $request->user()->is_admin
                        ? ($request->user()->is_super_admin
                            ? AdminFeatures::keys()
                            : ($request->user()->adminRole?->is_active
                                ? ($request->user()->adminRole->permissions ?? [])
                                : []))
                        : [],
                ] : null,
            ],
            'accountApplication' => fn () => $request->session()->get('accountApplication'),
            'whatsappBusinessPhone' => config('services.whatsapp.business_phone'),
            'marketTicker' => fn () => MarketQuote::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('symbol')
                ->get(['id', 'name', 'symbol', 'value', 'change_value', 'change_percent', 'as_of'])
                ->map(fn (MarketQuote $quote) => [
                    'id' => 'quote-'.$quote->id,
                    'type' => 'stock',
                    'name' => $quote->name,
                    'symbol' => $quote->symbol,
                    'value' => $quote->value,
                    'change_value' => $quote->change_value,
                    'change_percent' => $quote->change_percent,
                    'as_of' => $quote->as_of,
                ])
                ->concat(MarketIndex::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(['id', 'name', 'symbol', 'value', 'change_value', 'change_percent', 'as_of'])
                    ->map(fn (MarketIndex $index) => [
                        'id' => 'index-'.$index->id,
                        'type' => 'index',
                        'name' => $index->name,
                        'symbol' => $index->symbol,
                        'value' => $index->value,
                        'change_value' => $index->change_value,
                        'change_percent' => $index->change_percent,
                        'as_of' => $index->as_of,
                    ]))
                ->values(),
            'siteSettings' => fn () => SiteSetting::query()
                ->whereIn('key', [
                    'company_name',
                    'phone',
                    'email',
                    'address',
                    'address_aba',
                    'facebook',
                    'linkedin',
                    'instagram',
                    'footer_text',
                ])
                ->pluck('value', 'key'),
        ]);
    }
}
