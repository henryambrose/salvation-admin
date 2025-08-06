<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set current user ID for database triggers
        AuditHelper::setCurrentUserId();

        $response = $next($request);

        // Clear user ID after request
        AuditHelper::clearCurrentUserId();

        return $response;
    }
} 