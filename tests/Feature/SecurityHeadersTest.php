<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeaders;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Each Laravel response has the security headers of
 * App\Http\Middleware\SecurityHeaders: pages, error pages, and JSON.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function assertSecurityHeaders($response): void
    {
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_public_pages_have_the_security_headers(): void
    {
        foreach (['/', '/privacy-policy', '/login', '/sitemap.xml'] as $path)
            $this->assertSecurityHeaders($this->get($path));
    }

    public function test_error_page_has_the_security_headers(): void
    {
        $this->assertSecurityHeaders($this->get('/no-such-page')->assertNotFound());
    }

    public function test_app_page_has_the_security_headers(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null]);

        $this->assertSecurityHeaders($this->actingAs($user)->get('/profile')->assertOk());
    }

    public function test_no_payment_rule_for_the_paddle_checkout_frame(): void
    {
        $this->assertStringNotContainsString('payment', SecurityHeaders::HEADERS['Permissions-Policy']);
    }
}
