<?php

namespace App\Http\Middleware;

use App\Models\StoreSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStorefrontIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*') || $request->is('health') || $request->is('up')) {
            return $next($request);
        }

        $settings = StoreSetting::current();

        if (! $settings->storefront_enabled && ! auth('admin')->check()) {
            return response()->view('public.maintenance', ['settings' => $settings], 503)
                ->header('Retry-After', '3600');
        }

        return $next($request);
    }
}
