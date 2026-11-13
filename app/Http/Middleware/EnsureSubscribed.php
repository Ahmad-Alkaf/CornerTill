<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!$request->user()->business->subscribedOrOnTrial()) {
            return redirect(route( 'business.edit' ))->with('error', $request->method() === 'GET'
                ? __('You need to have an active subscription to access this page!')
                : __('You need to have an active subscription to perform this action!'));
        }
        return $next($request);
    }
}
