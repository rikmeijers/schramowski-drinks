<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevent Safari / iOS "Add to Home Screen" PWAs from serving stale HTML.
 * Fresh HTML then loads CSS/JS via versioned vasset() URLs.
 */
class PreventStaleHtmlCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $contentType = (string) $response->headers->get('Content-Type', '');

        if (str_contains($contentType, 'text/html') || $contentType === '') {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
