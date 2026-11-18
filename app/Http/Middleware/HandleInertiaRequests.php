<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use App\Models\MarketIndex;
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
            'marketTicker' => fn () => MarketIndex::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'name', 'symbol', 'value', 'change_value', 'change_percent', 'as_of']),
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
