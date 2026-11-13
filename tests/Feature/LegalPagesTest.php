<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The CornerTill legal supplements. X and the Google consent screen use these
 * URLs, so they must not change.
 */
class LegalPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_privacy_policy_opens(): void
    {
        $this->get('/privacy-policy')->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Anyone/PrivacyPolicy'));
        $this->assertSame(url('/privacy-policy'), route('privacyPolicy'));
    }

    public function test_terms_open(): void
    {
        $this->get('/terms-and-conditions')->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Anyone/TermsAndConditions'));
        $this->assertSame(url('/terms-and-conditions'), route('termsAndConditions'));
    }

    public function test_payment_service_policy_is_removed(): void
    {
        config(['features.payments_enabled' => true]);

        $this->get('/payment-service-policy')->assertNotFound();
    }
}
