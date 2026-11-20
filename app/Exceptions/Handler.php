<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Unhandled errors go to Sentry when SENTRY_LARAVEL_DSN is set
        // (config/sentry.php). Without a DSN this does nothing.
        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });
    }
}
