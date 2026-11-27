<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers for each Laravel response (also error pages). nginx sends
 * X-Content-Type-Options (docker/nginx.conf). Not here: HSTS (a Cloudflare
 * setting) and a Content-Security-Policy for scripts (needs a separate
 * decision: Inertia, Paddle.js, Sentry, and the Cloudflare beacon).
 */
class SecurityHeaders
{
    public const HEADERS = [
        // Other sites cannot show CornerTill pages in a frame (clickjacking).
        'X-Frame-Options' => 'SAMEORIGIN',
        // Other sites get only the origin, not the path (for example a bill ID).
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        // CornerTill uses none of these. No "payment" rule: the Paddle checkout
        // frame can need it when PAYMENTS_ENABLED=true.
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach (self::HEADERS as $name => $value) {
            if (!$response->headers->has($name))
                $response->headers->set($name, $value);
        }

        return $response;
    }
}
