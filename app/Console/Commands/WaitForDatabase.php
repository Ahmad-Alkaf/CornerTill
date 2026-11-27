<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Sleep;
use Throwable;

/**
 * docker/entrypoint.sh runs this before the migrations. After a server reboot,
 * the app container can start before MySQL accepts connections. A failed
 * `php artisan migrate` is an unhandled exception, and Sentry reports it
 * (CORNERTILL-2, 2026-11-25). This command catches the connection errors, so a
 * short wait is not an error report. If the database stays down, the command
 * stops with exit code 1 after the time limit, and the container start fails.
 */
class WaitForDatabase extends Command
{
    protected $signature = 'db:wait
        {--timeout=60 : Seconds to wait before the command fails}
        {--interval=2 : Seconds between two tries}';

    protected $description = 'Wait until the default database connection answers a query, with a time limit';

    public function handle(): int
    {
        $timeout = max(0, (int) $this->option('timeout'));
        $interval = max(1, (int) $this->option('interval'));
        $deadline = now()->addSeconds($timeout);
        $tries = 0;

        while (true) {
            $tries++;
            try {
                DB::connection()->select('select 1');
                if ($tries > 1)
                    $this->line("The database is ready after $tries tries.");
                return self::SUCCESS;
            } catch (Throwable $e) {
                // The next try opens a new connection.
                DB::purge();
                $error = $e->getMessage();
            }

            if (now()->addSeconds($interval)->gt($deadline))
                break;
            $this->line("The database is not ready (try $tries). Try again in $interval seconds.");
            Sleep::for($interval)->seconds();
        }

        $this->error("The database is not ready after $timeout seconds ($tries tries): $error");
        return self::FAILURE;
    }
}
