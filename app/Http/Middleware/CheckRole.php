<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // If no roles match, check for specific permissions
        $permission = $request->route()->getAction('permission');
        if ($permission && $user->hasPermission($permission)) {
            return $next($request);
        }

        // If no permission is required or user doesn't have it, check for admin role
        if ($user->isAdmin()) {
            return $next($request);
        }

        // If user doesn't have required role or permission, return 403
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        abort(403, 'Unauthorized action.');
    }
}
