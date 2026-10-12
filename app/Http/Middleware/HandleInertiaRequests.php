<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
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
