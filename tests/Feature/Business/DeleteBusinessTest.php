<?php

namespace Tests\Feature\Business;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Delete business: soft delete now, sign-in stops at once, restore is possible
 * for 30 days, then the daily job deletes all data permanently.
 */
class DeleteBusinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'local', 'features.payments_enabled' => false]);
        Storage::fake('businesses-logo');
        Storage::fake('products-images');
    }

    /**
     * A business with an Owner, a staff account that the Owner removed before,
     * a logo file, a product with an image file, a bill, and rows in the other tables.
     *
     * @return array{business: Business, owner: User, staff: User, product: Product, bill: Bill}
     */
    private function businessWithData(string $prefix = 'a'): array
    {
        $owner = User::factory()->create(['role' => 'Owner', 'email_verified_at' => now(), 'deleted_at' => null, 'email' => "{$prefix}-owner@example.com"]);
        $business = $owner->business;
        Storage::disk('businesses-logo')->put("{$prefix}-logo.png", 'x');
        $business->update(['logo' => "{$prefix}-logo.png"]);
        $staff = User::factory()->create([
            'role' => 'Cashier', 'business_id' => $business->id, 'email_verified_at' => now(),
            'email' => "{$prefix}-staff@example.com", 'deleted_at' => now()->subDays(60),
        ]);
        Storage::disk('products-images')->put("{$prefix}-apple.png", 'x');
        $product = Product::factory()->create(['business_id' => $business->id, 'createdBy_id' => $owner->id, 'img' => "{$prefix}-apple.png", 'price' => 5]);
        $bill = Bill::factory()->create(['business_id' => $business->id, 'createdBy_id' => $staff->id]);
        BillDetail::factory()->create(['bill_id' => $bill->id, 'product_id' => $product->id, 'quantity' => 2]);

        DB::table('password_reset_tokens')->insert(['email' => $owner->email, 'token' => 'hash', 'created_at' => now()]);
        // tokenable_id is a big integer (morphs()) and user IDs are ULIDs: MySQL cannot
        // store such a token, so the row exists only on sqlite.
        if (DB::getDriverName() === 'sqlite')
            $owner->createToken('test');
        DB::table('customers')->insert(['billable_type' => Business::class, 'billable_id' => $business->id, 'paddle_id' => "ctm_{$prefix}", 'name' => 'x', 'email' => $owner->email, 'created_at' => now(), 'updated_at' => now()]);
        $subscriptionId = DB::table('subscriptions')->insertGetId(['billable_type' => Business::class, 'billable_id' => $business->id, 'type' => 'default', 'paddle_id' => "sub_{$prefix}", 'status' => 'canceled', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('subscription_items')->insert(['subscription_id' => $subscriptionId, 'product_id' => 'pro_1', 'price_id' => 'pri_1', 'status' => 'canceled', 'quantity' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('transactions')->insert(['billable_type' => Business::class, 'billable_id' => $business->id, 'paddle_id' => "txn_{$prefix}", 'status' => 'paid', 'total' => '1', 'tax' => '0', 'currency' => 'USD', 'billed_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        return compact('business', 'owner', 'staff', 'product', 'bill');
    }

    private function deleteBusiness(User $owner): void
    {
        $this->actingAs($owner)
            ->delete(route('business.destroy', $owner->business_id), ['password' => 'asdfasdf'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');
        $this->assertGuest();
        Auth::forgetGuards();
    }

    private function deletedDaysAgo(Business $business, int $days): void
    {
        $at = now()->subDays($days);
        $deletedAt = Business::withTrashed()->find($business->id)->deleted_at;
        DB::table('users')->where('business_id', $business->id)->where('deleted_at', $deletedAt)->update(['deleted_at' => $at]);
        DB::table('business')->where('id', $business->id)->update(['deleted_at' => $at]);
    }

    public function test_delete_is_a_soft_delete_and_keeps_all_data_and_files(): void
    {
        ['business' => $business, 'owner' => $owner, 'staff' => $staff] = $this->businessWithData();
        $staffDeletedAt = $staff->fresh()->deleted_at;

        $this->deleteBusiness($owner);

        $trashed = Business::withTrashed()->find($business->id);
        $this->assertTrue($trashed->trashed());
        $this->assertSame('a-logo.png', $trashed->logo);
        Storage::disk('businesses-logo')->assertExists('a-logo.png');
        Storage::disk('products-images')->assertExists('a-apple.png');
        $this->assertEquals($trashed->deleted_at, User::withTrashed()->find($owner->id)->deleted_at);
        $this->assertEquals($staffDeletedAt, User::withTrashed()->find($staff->id)->deleted_at);
        $this->assertSame(1, Bill::where('business_id', $business->id)->count());
    }

    public function test_an_account_of_a_deleted_business_cannot_sign_in_and_sees_why(): void
    {
        ['owner' => $owner] = $this->businessWithData();
        $this->deleteBusiness($owner);

        $response = $this->from('/login')->post('/login', ['email' => $owner->email, 'password' => 'asdfasdf']);
        $response->assertSessionHasErrors('email');
        $message = session('errors')->first('email');
        $this->assertStringContainsString('This business was deleted on ' . now()->format('Y-m-d'), $message);
        $this->assertStringContainsString(now()->addDays(30)->format('Y-m-d'), $message);
        $this->assertStringContainsString('support@kaflabs.com', $message);
        $this->assertGuest();

        // A wrong password tells nothing about the business.
        $this->post('/login', ['email' => $owner->email, 'password' => 'wrong-password']);
        $this->assertSame(trans('auth.failed'), session('errors')->first('email'));
    }

    public function test_the_sign_in_message_is_in_arabic(): void
    {
        ['owner' => $owner] = $this->businessWithData();
        $this->deleteBusiness($owner);

        $this->withUnencryptedCookie('i18next', 'ar')
            ->post('/login', ['email' => $owner->email, 'password' => 'asdfasdf']);
        $this->assertStringContainsString('حُذف هذا النشاط التجاري', session('errors')->first('email'));
    }

    public function test_an_active_staff_account_of_a_deleted_business_is_blocked_everywhere(): void
    {
        ['business' => $business, 'owner' => $owner] = $this->businessWithData();
        $staff = User::factory()->create(['role' => 'Cashier', 'business_id' => $business->id, 'email_verified_at' => now(), 'deleted_at' => null, 'email' => 'active-staff@example.com']);
        DB::table('business')->where('id', $business->id)->update(['deleted_at' => now()]);

        // Sign-in.
        $this->post('/login', ['email' => $staff->email, 'password' => 'asdfasdf'])->assertSessionHasErrors('email');
        $this->assertStringContainsString('This business was deleted on', session('errors')->first('email'));
        $this->assertGuest();

        // An existing session.
        $this->withSession([Auth::guard('web')->getName() => $staff->id])
            ->get('/dashboard')
            ->assertRedirect(route('login'));
        Auth::forgetGuards();

        // A "remember me" cookie.
        $staff->forceFill(['remember_token' => 'remember-token'])->saveQuietly();
        $this->assertNull(Auth::guard('web')->getProvider()->retrieveByToken($staff->id, 'remember-token'));

        // Password reset.
        Notification::fake();
        $this->post('/forgot-password', ['email' => $staff->email]);
        Notification::assertNothingSent();
    }

    public function test_the_receipt_link_of_a_deleted_business_stops_working(): void
    {
        ['owner' => $owner, 'bill' => $bill] = $this->businessWithData();
        DB::table('bills')->where('id', $bill->id)->update(['created_at' => now()->subDay()]);
        $this->get(route('bill.show', $bill->id))->assertOk();

        $this->deleteBusiness($owner);

        $this->get(route('bill.show', $bill->id))->assertNotFound();
    }

    public function test_restore_brings_back_the_business_and_its_owner_but_not_removed_staff(): void
    {
        ['business' => $business, 'owner' => $owner, 'staff' => $staff] = $this->businessWithData();
        $this->deleteBusiness($owner);

        $this->artisan('business:restore', ['id' => $business->id])->assertSuccessful();

        $this->assertFalse(Business::find($business->id)->trashed());
        $this->assertNull(User::find($owner->id)?->deleted_at);
        $this->assertTrue(User::withTrashed()->find($staff->id)->trashed());
        $this->post('/login', ['email' => $owner->email, 'password' => 'asdfasdf'])->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($owner->fresh());

        $this->artisan('business:restore', ['id' => $business->id])->assertFailed();
    }

    public function test_purge_keeps_a_business_deleted_29_days_ago(): void
    {
        ['business' => $business, 'owner' => $owner] = $this->businessWithData();
        $this->deleteBusiness($owner);
        $this->deletedDaysAgo($business, 29);

        $this->artisan('business:purge-deleted')->assertSuccessful();

        $this->assertNotNull(Business::withTrashed()->find($business->id));
        $this->assertSame(1, Bill::where('business_id', $business->id)->count());
        Storage::disk('businesses-logo')->assertExists('a-logo.png');
    }

    public function test_purge_deletes_all_data_of_a_business_deleted_31_days_ago_and_nothing_else(): void
    {
        ['business' => $business, 'owner' => $owner, 'staff' => $staff, 'product' => $product, 'bill' => $bill] = $this->businessWithData('a');
        $other = $this->businessWithData('b');
        $this->deleteBusiness($owner);
        $this->deletedDaysAgo($business, 31);

        // --dry-run deletes nothing.
        $this->artisan('business:purge-deleted', ['--dry-run' => true])
            ->expectsOutputToContain("Would purge business {$business->id}: bills=1, bill_details=1, products=1, product_images=1, accounts=2, logo=1")
            ->assertSuccessful();
        $this->assertNotNull(Business::withTrashed()->find($business->id));

        $this->artisan('business:purge-deleted')
            ->expectsOutputToContain("Purged deleted business {$business->id}: bills=1, bill_details=1, products=1, product_images=1, accounts=2, logo=1")
            ->assertSuccessful();

        $this->assertNull(Business::withTrashed()->find($business->id));
        $this->assertSame(0, User::withTrashed()->whereIn('id', [$owner->id, $staff->id])->count());
        $this->assertNull(Product::find($product->id));
        $this->assertNull(Bill::find($bill->id));
        $this->assertSame(0, BillDetail::where('bill_id', $bill->id)->count());
        Storage::disk('businesses-logo')->assertMissing('a-logo.png');
        Storage::disk('products-images')->assertMissing('a-apple.png');
        $this->assertSame(0, DB::table('password_reset_tokens')->where('email', $owner->email)->count());
        $this->assertSame(0, DB::table('personal_access_tokens')->where(DB::raw('CAST(tokenable_id AS CHAR)'), $owner->id)->count());
        foreach (['customers', 'subscriptions', 'transactions'] as $table)
            $this->assertSame(0, DB::table($table)->where('billable_id', $business->id)->count(), $table);
        $this->assertSame(1, DB::table('subscription_items')->count()); // only the other business's item

        // The other business is untouched.
        $this->assertNotNull(Business::find($other['business']->id));
        $this->assertSame(2, User::withTrashed()->where('business_id', $other['business']->id)->count());
        $this->assertSame(1, Bill::where('business_id', $other['business']->id)->count());
        $this->assertSame(1, BillDetail::where('bill_id', $other['bill']->id)->count());
        $this->assertNotNull(Product::find($other['product']->id));
        Storage::disk('businesses-logo')->assertExists('b-logo.png');
        Storage::disk('products-images')->assertExists('b-apple.png');
        $this->assertSame(1, DB::table('customers')->where('billable_id', $other['business']->id)->count());

        // A second run has nothing to do (idempotent).
        $this->artisan('business:purge-deleted')->assertSuccessful();
    }

    public function test_a_purge_blocked_by_another_business_deletes_nothing(): void
    {
        ['business' => $business, 'owner' => $owner, 'product' => $product] = $this->businessWithData('a');
        $other = $this->businessWithData('b');
        // Old data: business B sold a product of A (before products were checked per business).
        BillDetail::factory()->create(['bill_id' => $other['bill']->id, 'product_id' => $product->id, 'quantity' => 1]);
        $this->deleteBusiness($owner);
        $this->deletedDaysAgo($business, 31);

        $this->artisan('business:purge-deleted', ['--dry-run' => true])
            ->expectsOutputToContain('Skipped: 1 bill details of other businesses use products of business')
            ->assertSuccessful();
        $this->artisan('business:purge-deleted')->assertFailed();

        // Nothing of A is deleted, and B is untouched.
        $this->assertSame(1, Bill::where('business_id', $business->id)->count());
        $this->assertNotNull(Product::find($product->id));
        $this->assertSame(2, User::withTrashed()->where('business_id', $business->id)->count());
        Storage::disk('businesses-logo')->assertExists('a-logo.png');
        $this->assertSame(2, BillDetail::where('bill_id', $other['bill']->id)->count());
    }

    public function test_restore_after_the_retention_period_needs_force(): void
    {
        ['business' => $business, 'owner' => $owner] = $this->businessWithData();
        $this->deleteBusiness($owner);
        $this->deletedDaysAgo($business, 31);

        $this->artisan('business:restore', ['id' => $business->id])
            ->expectsOutputToContain('Check the data, then run again with --force')
            ->assertFailed();
        $this->assertTrue(Business::withTrashed()->find($business->id)->trashed());

        $this->artisan('business:restore', ['id' => $business->id, '--force' => true])
            ->expectsOutputToContain('and 1 account(s). Data: bills=1, products=1, accounts=2')
            ->assertSuccessful();
        $this->assertFalse(Business::find($business->id)->trashed());
    }

    public function test_restore_of_a_business_that_the_old_code_deleted(): void
    {
        // The old destroy() deleted the user and the business with two separate times.
        ['business' => $business, 'owner' => $owner, 'staff' => $staff] = $this->businessWithData();
        DB::table('users')->where('id', $owner->id)->update(['deleted_at' => now()->subDays(3)->subSecond()]);
        DB::table('business')->where('id', $business->id)->update(['deleted_at' => now()->subDays(3)]);

        $this->artisan('business:restore', ['id' => $business->id])
            ->expectsOutputToContain('and 1 account(s)')
            ->expectsOutputToContain('These accounts stay deleted')
            ->assertSuccessful();
        $this->post('/login', ['email' => $owner->email, 'password' => 'asdfasdf'])->assertSessionHasNoErrors();
        $this->assertTrue(User::withTrashed()->find($staff->id)->trashed());
    }

    public function test_restore_with_user_restores_a_chosen_account(): void
    {
        ['business' => $business, 'owner' => $owner, 'staff' => $staff] = $this->businessWithData();
        $this->deleteBusiness($owner);

        $this->artisan('business:restore', ['id' => $business->id, '--with-user' => [$staff->id]])
            ->expectsOutputToContain('and 2 account(s)')
            ->assertSuccessful();
        $this->assertFalse(User::find($staff->id)->trashed());

        // Also later, on a business that is not deleted.
        DB::table('users')->where('id', $staff->id)->update(['deleted_at' => now()]);
        $this->artisan('business:restore', ['id' => $business->id, '--with-user' => [$staff->id]])
            ->expectsOutputToContain('and 1 account(s)')
            ->assertSuccessful();
    }

    public function test_restore_refuses_a_with_user_that_is_not_a_deleted_account_of_the_business(): void
    {
        ['business' => $business, 'owner' => $owner] = $this->businessWithData('a');
        $other = $this->businessWithData('b');
        $this->deleteBusiness($owner);

        $this->artisan('business:restore', ['id' => $business->id, '--with-user' => [$other['staff']->id]])
            ->expectsOutputToContain("--with-user={$other['staff']->id} is not a deleted account of business {$business->id}")
            ->assertFailed();

        $this->assertTrue(Business::withTrashed()->find($business->id)->trashed());
        $this->assertTrue(User::withTrashed()->find($other['staff']->id)->trashed());
    }

    public function test_purge_is_scheduled_daily(): void
    {
        $event = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())
            ->first(fn ($e) => str_contains($e->command ?? '', 'business:purge-deleted'));
        $this->assertNotNull($event);
        $this->assertSame('0 0 * * *', $event->expression);
    }
}
