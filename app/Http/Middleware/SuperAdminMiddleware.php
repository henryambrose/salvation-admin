<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasRole('superadmin')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Superadmin access required.'], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Access denied. Superadmin privileges required.');
        }

        return $next($request);
    }
} 