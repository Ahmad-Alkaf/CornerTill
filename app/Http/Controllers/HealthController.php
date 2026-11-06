<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Health check for the hosting platform (Coolify) and uptime monitors.
 * Returns 200 when Laravel runs and the database answers, 503 when the
 * database check fails. The route has no middleware: no session, no auth.
 */
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::connection()->select('SELECT 1');
        } catch (Throwable $e) {
            Log::error('Health check: database unreachable', ['error' => $e->getMessage()]);

            return response()
                ->json(['status' => 'error', 'database' => 'unreachable'], 503)
                ->header('Cache-Control', 'no-store');
        }

        return response()
            ->json(['status' => 'ok', 'database' => 'ok'])
            ->header('Cache-Control', 'no-store');
    }
}
