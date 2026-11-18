<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin || ! $user->hasFeature($feature)) {
            abort(403, 'You do not have permission to access this feature.');
        }

        return $next($request);
    }
}
