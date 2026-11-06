<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
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
            $this->checkDatabase();
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

    /**
     * Open a new connection and run SELECT 1, with a short time limit
     * (DB_HEALTH_TIMEOUT). The limits apply to this request only: this
     * route has no middleware, so no other code opened a connection yet.
     */
    private function checkDatabase(): void
    {
        $timeout = max(1, (int) config('database.health_timeout', 2));
        $name = config('database.default');

        // Connect timeout.
        config(["database.connections.$name.options" => array_replace(
            (array) config("database.connections.$name.options", []),
            [PDO::ATTR_TIMEOUT => $timeout]
        )]);
        // Read timeout: a server that accepts TCP but does not answer would
        // otherwise block for hours (mysqlnd.net_read_timeout default).
        ini_set('mysqlnd.net_read_timeout', (string) $timeout);
        ini_set('default_socket_timeout', (string) $timeout);

        DB::purge($name);
        // getPdo() + query skips the query-level reconnect of Laravel,
        // so the worst case is two connect attempts, not four.
        DB::connection($name)->getPdo()->query('SELECT 1');
    }
}
