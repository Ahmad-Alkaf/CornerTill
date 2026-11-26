<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Sleep;
use Tests\TestCase;

/**
 * `php artisan db:wait` (docker/entrypoint.sh, before the migrations): a
 * database that is not ready yet is not an error report (Sentry CORNERTILL-2),
 * and a database that stays down stops the container start after the limit.
 */
class WaitForDatabaseTest extends TestCase
{
    private string $missingFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->missingFile = sys_get_temp_dir() . '/cornertill-db-wait-' . uniqid() . '.sqlite';
        // A SQLite file that does not exist refuses each connection, the same as
        // a MySQL server that has not started.
        config([
            'database.connections.not_ready' => ['driver' => 'sqlite', 'database' => $this->missingFile, 'prefix' => ''],
        ]);
        Exceptions::fake();
    }

    protected function tearDown(): void
    {
        @unlink($this->missingFile);
        parent::tearDown();
    }

    public function test_returns_at_once_when_the_database_answers(): void
    {
        Sleep::fake();

        $this->artisan('db:wait')->assertSuccessful();

        Sleep::assertNeverSlept();
        Exceptions::assertNothingReported();
    }

    public function test_tries_again_until_the_database_answers(): void
    {
        config(['database.default' => 'not_ready']);
        Sleep::fake();
        // The database starts during the first wait.
        Sleep::whenFakingSleep(fn () => touch($this->missingFile));

        $this->artisan('db:wait', ['--timeout' => 60, '--interval' => 2])
            ->expectsOutput('The database is not ready (try 1). Try again in 2 seconds.')
            ->expectsOutput('The database is ready after 2 tries.')
            ->assertSuccessful();

        Sleep::assertSleptTimes(1);
        Exceptions::assertNothingReported();
    }

    public function test_fails_after_the_time_limit_when_the_database_stays_down(): void
    {
        config(['database.default' => 'not_ready']);
        // Each fake sleep moves the frozen clock forward.
        $this->freezeTime();
        Sleep::fake(syncWithCarbon: true);

        $this->artisan('db:wait', ['--timeout' => 6, '--interval' => 2])
            ->expectsOutputToContain('The database is not ready after 6 seconds (4 tries)')
            ->assertFailed();

        Sleep::assertSleptTimes(3);
        Exceptions::assertNothingReported();
    }
}
