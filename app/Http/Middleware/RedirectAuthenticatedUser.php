<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectAuthenticatedUser
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = $guards ?: ['admin', 'web'];

        if (in_array('admin', $guards, true) && Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (in_array('web', $guards, true) && Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
