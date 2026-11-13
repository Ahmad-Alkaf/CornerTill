<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_returns_200_when_the_database_answers(): void
    {
        $response = $this->get('/api/health');

        $response->assertOk()
            ->assertExactJson(['status' => 'ok', 'database' => 'ok'])
            ->assertCookieMissing(config('session.cookie'))
            ->assertCookieMissing('XSRF-TOKEN');
    }

    public function test_health_returns_503_when_the_database_fails(): void
    {
        $default = config('database.default');
        config([
            "database.connections.$default.host" => '127.0.0.1',
            "database.connections.$default.port" => 1,
            "database.connections.$default.database" => '/nonexistent/folder/db.sqlite',
        ]);
        DB::purge($default);

        $response = $this->get('/api/health');

        $response->assertStatus(503)
            ->assertExactJson(['status' => 'error', 'database' => 'unreachable']);
    }
}
