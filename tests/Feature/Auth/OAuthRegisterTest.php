<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

/**
 * Registration with Google, GitHub, or X (switched off in production; Socialite is mocked).
 * The provider's name and email stay in the session, never in a URL.
 */
class OAuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['features.social.google' => true, 'features.payments_enabled' => false]);
    }

    private function mockGoogleUser(string $name, string $email): void
    {
        $user = (new SocialiteUser())->map(['name' => $name, 'email' => $email, 'nickname' => null]);
        $provider = Mockery::mock();
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    private function registerPayload(string $email): array
    {
        return [
            'name' => 'Sara Ali',
            'email' => $email,
            'role' => 'Owner',
            'password' => 'asdfasdf',
            'password_confirmation' => 'asdfasdf',
            'business' => [
                'name' => 'Corner Shop', 'logo' => null, 'country' => 'Saudi Arabia', 'city' => 'Makkah',
                'address' => 'Ray', 'currency' => '$', 'phone' => '555555555', 'countryCallingCode' => '+966',
                'taxPercent' => 0.15, 'taxIdentificationNumber' => null,
            ],
        ];
    }

    public function test_the_callback_keeps_the_name_and_email_out_of_the_url(): void
    {
        $this->mockGoogleUser('Sara Ali', 'sara@example.com');

        $response = $this->get('/auth/google/callback?state=' . urlencode('plan=Basic&period=Monthly'));

        $location = $response->headers->get('Location');
        $this->assertSame(route('register', ['plan' => 'Basic', 'period' => 'Monthly']), $location);
        $this->assertStringNotContainsString('sara', strtolower(urldecode($location)));
        $this->assertStringNotContainsString('signature', $location);
        $this->assertSame('sara@example.com', session(RegisterController::OAUTH_SESSION_KEY)['email']);

        $this->get($location)->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Authenticating/Register/Register')
            ->where('oauth.name', 'Sara Ali')
            ->where('oauth.email', 'sara@example.com'));
    }

    public function test_the_provider_email_is_marked_as_confirmed(): void
    {
        $this->mockGoogleUser('Sara Ali', 'sara@example.com');
        $this->get('/auth/google/callback');

        $this->post('/register', $this->registerPayload('Sara@Example.com'))->assertSessionHasNoErrors();

        $this->assertNotNull(User::where('email', 'Sara@Example.com')->first()->email_verified_at);
        $this->assertNull(session(RegisterController::OAUTH_SESSION_KEY));
    }

    public function test_another_email_is_not_marked_as_confirmed(): void
    {
        $this->mockGoogleUser('Sara Ali', 'sara@example.com');
        $this->get('/auth/google/callback');

        $this->post('/register', $this->registerPayload('victim@example.com'))->assertSessionHasNoErrors();

        $this->assertNull(User::where('email', 'victim@example.com')->first()->email_verified_at);
    }

    public function test_expired_provider_data_does_not_confirm_the_email(): void
    {
        $this->withSession([RegisterController::OAUTH_SESSION_KEY => [
            'name' => 'Sara Ali', 'email' => 'sara@example.com', 'expires_at' => now()->subMinute()->getTimestamp(),
        ]])->post('/register', $this->registerPayload('sara@example.com'))->assertSessionHasNoErrors();

        $this->assertNull(User::where('email', 'sara@example.com')->first()->email_verified_at);
    }

    public function test_a_normal_registration_is_not_confirmed(): void
    {
        $this->post('/register', $this->registerPayload('new@example.com'))->assertSessionHasNoErrors();

        $this->assertNull(User::where('email', 'new@example.com')->first()->email_verified_at);
    }
}
