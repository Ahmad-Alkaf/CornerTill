<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * HTTP 403 messages when a business reaches a resource limit.
 */
class LimitMessagesTest extends TestCase
{
    use RefreshDatabase;

    private const BETA = 'You reached the beta limit. Contact support@kaflabs.com for more.';

    private function owner(): User
    {
        return User::factory()->create([
            'role' => 'Owner',
            'email_verified_at' => now(),
            'deleted_at' => null,
        ]);
    }

    /** Run the policy and return the 403 message. */
    private function denyMessage(User $user, string $ability, string $class): string
    {
        try {
            Gate::forUser($user)->authorize($ability, $class);
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            return $e->getMessage();
        }
        $this->fail("The '$ability' policy did not refuse.");
    }

    public function test_beta_mode_shows_the_beta_limit_message(): void
    {
        config([
            'features.payments_enabled' => false,
            'constants.betaResources' => ['accounts' => 0, 'products' => 0, 'bills' => 0],
        ]);
        $owner = $this->owner();

        $this->assertSame(self::BETA, $this->denyMessage($owner, 'store', Product::class));
        $this->assertSame(self::BETA, $this->denyMessage($owner, 'store', Bill::class));
        $this->assertSame(self::BETA, $this->denyMessage($owner, 'store', User::class));
    }

    public function test_paid_mode_shows_the_upgrade_message(): void
    {
        config([
            'features.payments_enabled' => true,
            'constants.planResources.basic' => ['accounts' => 0, 'products' => 0, 'bills' => 0],
        ]);
        $owner = $this->owner();
        // A trial counts as the Basic plan.
        $owner->business->customer()->create([
            'paddle_id' => 'ctm_test',
            'name' => 'Test',
            'email' => 'owner@example.com',
            'trial_ends_at' => now()->addDay(),
        ]);
        $owner->refresh();

        foreach ([[Product::class, 'products'], [Bill::class, 'bills'], [User::class, 'accounts']] as [$class, $resource]) {
            $message = $this->denyMessage($owner, 'store', $class);
            $this->assertStringContainsString("maximum number of $resource", $message);
            $this->assertStringContainsString('upgrade your plan', $message);
            $this->assertStringContainsString('support@kaflabs.com', $message);
        }
    }
}
