<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * PAYMENTS_ENABLED and SOCIAL_*_ENABLED switches (config/features.php).
 */
class LaunchSwitchesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_payments_are_off_by_default(): void
    {
        $this->assertFalse(
            config('features.payments_enabled'),
            'The privacy policy and the terms say that CornerTill takes no payments. '
            . 'Before payments are on: name the payment provider in Privacy section 7, '
            . 'update Terms section 7, publish a refund policy and link it from the '
            . 'pricing cards (README.md "Launch switches").'
        );
    }

    public function test_payment_routes_return_404_when_payments_are_off(): void
    {
        config(['features.payments_enabled' => false]);

        $this->get('/subscription/plans')->assertNotFound();
        $this->get('/subscribe?plan=Basic&period=Monthly')->assertNotFound();
        $this->get('/subscription/pause')->assertNotFound();
        $this->get('/update-payment-method')->assertNotFound();
        // Cashier::ignoreRoutes() removes the Paddle webhook in the free beta.
        $this->post('/paddle/webhook')->assertNotFound();
    }

    public function test_welcome_page_shows_free_beta_when_payments_are_off(): void
    {
        config(['features.payments_enabled' => false]);

        $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Guest/Welcome/index')
            ->where('features.paymentsEnabled', false)
            ->where('betaMaxRes.accounts', config('constants.betaResources.accounts'))
            ->etc());
    }

    public function test_paddle_js_is_not_loaded_when_payments_are_off(): void
    {
        config(['features.payments_enabled' => false]);

        $this->get('/')->assertOk()->assertDontSee('paddle.js', false);
    }

    public function test_default_social_switches(): void
    {
        $this->assertTrue(config('features.social.google'));
        $this->assertTrue(config('features.social.github'));
        $this->assertFalse(config('features.social.x'));

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('features.social', ['google' => true, 'github' => true, 'x' => false])
            ->etc());
    }

    public function test_disabled_social_provider_returns_404(): void
    {
        config(['features.social.x' => false, 'features.social.github' => false]);

        $this->get('/auth/x/redirect')->assertNotFound();
        $this->get('/auth/x/callback')->assertNotFound();
        $this->get('/auth/github/redirect')->assertNotFound();
        $this->get('/auth/github/callback')->assertNotFound();
        $this->get('/auth/unknown/redirect')->assertNotFound();
    }

    public function test_enabled_social_provider_redirects_to_the_provider(): void
    {
        config([
            'features.social.google' => true,
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-secret',
        ]);

        $this->get('/auth/google/redirect')
            ->assertRedirectContains('accounts.google.com');
    }
}
