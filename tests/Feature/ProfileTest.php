<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_admin_can_delete_their_business(): void
    {
        $user = User::factory()->create(['role'=>'Owner','email_verified_at' => now(), 'deleted_at' => null]);

        $response = $this
            ->actingAs($user)
            ->delete(route('business.destroy',$user->business_id), [
                'password' => 'asdfasdf',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNotNull($user->fresh()->deleted_at);
    }

    public function test_owner_cannot_delete_their_business_with_wrong_password(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null,'role'=>'Owner']);
        $response = $this
            ->actingAs($user)
            ->from(route('business.edit'))
            ->delete(route('business.destroy',$user->business_id), [
                'password' => 'wrong-password',
            ]);

        $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('business.edit'));

        $this->assertNotNull($user->fresh());
    }

    public function test_empty_name_gets_the_required_message(): void
    {
        // A name of only spaces passes the required attribute of the input.
        // TrimStrings and ConvertEmptyStringsToNull make it null; the message
        // was "The name field must be a string."
        $user = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null]);

        $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', ['name' => '   ', 'email' => $user->email])
            ->assertSessionHasErrors(['name' => 'The name field is required.'])
            ->assertRedirect('/profile');

        $this->assertSame($user->name, $user->fresh()->name);
    }

    public function test_owner_gets_the_required_message_for_an_empty_account_name(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null, 'role' => 'Owner']);
        $cashier = User::factory()->create(['email_verified_at' => now(), 'deleted_at' => null, 'role' => 'Cashier', 'business_id' => $owner->business_id]);

        $this->actingAs($owner)
            ->from('/accounts')
            ->patch(route('account.update', $cashier->id), ['name' => '', 'email' => $cashier->email, 'role' => 'Cashier'])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);

        $this->assertSame($cashier->name, $cashier->fresh()->name);
    }
}
