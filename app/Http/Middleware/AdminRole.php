<?php

namespace App\Http\Middleware;

use App\Enums\AdminRole as AdminRoleEnum;
use Closure;
use Illuminate\Http\Request;

class AdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $admin = auth('admin')->user();

        if (! $admin) {
            return redirect()
                ->route('admin.login');
        }

        $allowedRoles = array_filter(array_map(
            fn (string $role) => AdminRoleEnum::tryFrom($role),
            $roles
        ));

        if (! in_array($admin->role, $allowedRoles, true)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Sem permissão para acessar essa área.');
        }

        return $next($request);
    }
}
