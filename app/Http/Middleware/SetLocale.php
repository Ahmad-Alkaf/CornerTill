<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Set the Laravel locale (en or ar) for server messages: validation,
 * flash messages, and error pages.
 * 1. The `i18next` cookie, which the React language detector and the
 *    EN/AR switch write (resources/js/i18n.ts).
 * 2. The Accept-Language header of the browser.
 * English is the default.
 */
class SetLocale
{
    public const SUPPORTED = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(self::detect($request));

        return $next($request);
    }

    public static function detect(Request $request): string
    {
        $cookie = $request->cookie('i18next');
        if (is_string($cookie) && $cookie !== '')
            return str_starts_with(strtolower($cookie), 'ar') ? 'ar' : 'en';

        return $request->getPreferredLanguage(self::SUPPORTED) === 'ar' ? 'ar' : 'en';
    }
}
