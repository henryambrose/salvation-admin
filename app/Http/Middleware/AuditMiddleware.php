<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only set audit context for authenticated users
        if (Auth::check()) {
            // Store request info for potential audit logging
            app()->instance('audit.request.ip', $request->ip());
            app()->instance('audit.request.user_agent', $request->userAgent());
            app()->instance('audit.request.url', $request->fullUrl());
            app()->instance('audit.request.method', $request->method());
        }

        return $next($request);
    }
}