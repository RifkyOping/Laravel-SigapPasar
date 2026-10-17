<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $allowedRoles = $roles;
        if (in_array('super_admin', $roles) && !in_array('admin', $allowedRoles)) {
            $allowedRoles[] = 'admin';
        }
        if (in_array('admin', $roles) && !in_array('super_admin', $allowedRoles)) {
            $allowedRoles[] = 'super_admin';
        }

        if (!in_array($user->role, $allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
