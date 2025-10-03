<?php
namespace Modules\Members\Http\Middleware;
use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PreventBackHistory {
  public function handle($request, Closure $next) {
    $response = $next($request);

    // Don't add cache headers to file downloads (BinaryFileResponse or StreamedResponse)
    // as they don't support the ->header() method and handle their own headers
    if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
        return $response;
    }

    // Also skip if response has Content-Disposition header (file download)
    if ($response->headers->has('Content-Disposition')) {
        return $response;
    }

    // Skip if response is an image or other binary content
    $contentType = $response->headers->get('Content-Type');
    if ($contentType && (
        str_starts_with($contentType, 'image/') ||
        str_starts_with($contentType, 'application/pdf') ||
        str_starts_with($contentType, 'application/octet-stream')
    )) {
        return $response;
    }

    return $response->header('Cache-Control','no-store, no-cache, must-revalidate, max-age=0')
                    ->header('Pragma','no-cache')
                    ->header('Expires','Sat, 01 Jan 2000 00:00:00 GMT');
  }
}