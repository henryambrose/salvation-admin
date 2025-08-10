<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is a superadmin
        if (! auth()->check() || ! auth()->user()->is_superadmin) {
            abort(403, 'Access denied. Superadmin privileges required.');
        }

        return $next($request);
    }
}
