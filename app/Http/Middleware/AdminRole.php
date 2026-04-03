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
            abort(403);
        }

        if (!in_array($admin->role, $roles)) {
            abort(403, 'Sem permissão');
        }

        return $next($request);
    }
}
