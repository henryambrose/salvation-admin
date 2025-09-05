<?php

namespace Modules\Members\Http\Middleware;

use Modules\Members\Helpers\AuditHelper;
use Closure;
use Illuminate\Http\Request;
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
