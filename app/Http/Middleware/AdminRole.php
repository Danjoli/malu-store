<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return redirect()
                ->route('admin.login');
        }

        if (!in_array($admin->role, $roles)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Sem permissão para acessar essa área.');
        }

        return $next($request);
    }
}
