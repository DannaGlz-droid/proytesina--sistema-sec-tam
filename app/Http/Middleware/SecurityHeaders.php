<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->routeIs('reportes.file.preview')) {
            $response->headers->remove('X-Frame-Options');
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'; base-uri 'self'; form-action 'self'");
        } else {
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
        }

        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        if ($request->routeIs('login', 'password.*')) {
            $response->headers->set('Cache-Control', 'no-store, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
