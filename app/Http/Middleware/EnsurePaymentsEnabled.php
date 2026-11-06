<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Return 404 for payment routes (plans, subscribe, subscription changes,
 * payment policy) when PAYMENTS_ENABLED is false (free beta).
 */
class EnsurePaymentsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('features.payments_enabled'), 404);

        return $next($request);
    }
}
