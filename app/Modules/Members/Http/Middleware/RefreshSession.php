<?php

namespace Modules\Members\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RefreshSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Refresh the session to prevent expiration
        if (Session::isStarted()) {
            // Only refresh if session is older than 1 hour
            $lastActivity = Session::get('last_activity');
            $now = time();
            
            if (!$lastActivity || ($now - $lastActivity) > 3600) {
                Session::migrate();
                Session::put('last_activity', $now);
            }
        }

        return $next($request);
    }
}
