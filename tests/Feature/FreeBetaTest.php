<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * PAYMENTS_ENABLED=false: every business has the beta limits and no Paddle call.
 */
class FreeBetaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        config(['features.payments_enabled' => false]);
    }

    private function owner(): User
    {
        return User::factory()->create([
            'role' => 'Owner',
            'email_verified_at' => now(),
            'deleted_at' => null,
        ]);
    }

    public function test_business_without_subscription_has_access_with_beta_limits(): void
    {
        $business = $this->owner()->business;

        $this->assertTrue($business->subscribedOrOnTrial());
        $progress = $business->progressOrFail();
        $this->assertSame(config('constants.betaResources.accounts'), $progress['accounts']['max']);
        $this->assertSame(config('constants.betaResources.products'), $progress['products']['max']);
        $this->assertSame(config('constants.betaResources.bills'), $progress['bills']['max']);
        // A plan name does not change the beta limits.
        $this->assertSame(config('constants.betaResources.accounts'), $business->progress('advanced')['accounts']['max']);
    }

    public function test_business_page_shows_the_beta_plan_without_paddle_links(): void
    {
        $this->actingAs($this->owner())
            ->get('/business')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Authenticated/Business/Edit')
                ->where('subscriptionLinks.subscribedTo', 'Beta')
                ->where('subscriptionLinks.basic', null)
                ->where('subscriptionLinks.enhanced', null)
                ->where('subscriptionLinks.advanced', null)
                ->has('subscriptionLinks.progress.accounts')
                ->etc());
    }

    public function test_subscribed_middleware_lets_beta_users_through(): void
    {
        // Empty product: the request passes the `subscribed` middleware and
        // fails validation, so it does not redirect to the business page.
        $response = $this->actingAs($this->owner())
            ->from('/inventory')
            ->post('/inventory', []);

        $response->assertRedirect('/inventory');
        $response->assertSessionHasErrors();
    }
}
