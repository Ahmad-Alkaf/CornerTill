<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Support\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SaudiRiyalSignTest extends TestCase
{
    use RefreshDatabase;

    private const SIGN = "\u{20C1}";

    public function test_normalize_changes_only_old_saudi_riyal_symbols(): void
    {
        $this->assertSame(self::SIGN, Currency::SAUDI_RIYAL_SIGN);
        $this->assertSame(self::SIGN, Currency::normalize('ر.س', 'Saudi Arabia'));
        $this->assertSame(self::SIGN, Currency::normalize(' ر.س ', null));
        $this->assertSame(self::SIGN, Currency::normalize('ر.س.', 'Yemen'));
        $this->assertSame(self::SIGN, Currency::normalize('sar', 'Saudi Arabia'));
        $this->assertSame(self::SIGN, Currency::normalize('﷼', 'Saudi Arabia'));
        $this->assertSame(self::SIGN, Currency::normalize(self::SIGN, 'Saudi Arabia'));

        // The rial sign is also the Iranian and the Yemeni rial.
        $this->assertSame('﷼', Currency::normalize('﷼', 'Yemen'));
        $this->assertSame('SAR', Currency::normalize('SAR', 'Germany'));
        $this->assertSame('ر.ق', Currency::normalize('ر.ق', 'Qatar'));
        $this->assertSame('$', Currency::normalize('$', 'Saudi Arabia'));
        $this->assertNull(Currency::normalize(null, 'Saudi Arabia'));
    }

    public function test_registration_stores_the_saudi_riyal_sign(): void
    {
        $this->post('/register', $this->registration('ر.س'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('business', ['name' => 'Riyal Shop', 'currency' => self::SIGN]);
    }

    public function test_registration_keeps_the_saudi_riyal_sign(): void
    {
        $this->post('/register', $this->registration(self::SIGN));

        $this->assertDatabaseHas('business', ['name' => 'Riyal Shop', 'currency' => self::SIGN]);
    }

    public function test_business_settings_store_the_saudi_riyal_sign(): void
    {
        $owner = User::factory()->create(['role' => 'Owner', 'email_verified_at' => now(), 'deleted_at' => null]);
        $business = $owner->business;
        $business->update(['country' => 'Saudi Arabia', 'currency' => '$']);

        $this->actingAs($owner)
            ->patch(route('business.update', $business->id), ['currency' => 'ر.س'])
            ->assertSessionHasNoErrors();

        $this->assertSame(self::SIGN, $business->fresh()->currency);
    }

    public function test_migration_changes_old_symbols_and_can_be_rolled_back(): void
    {
        $saudi = Business::factory()->create(['country' => 'Saudi Arabia']);
        $sar = Business::factory()->create(['country' => 'Saudi Arabia']);
        $yemen = Business::factory()->create(['country' => 'Yemen']);
        $usd = Business::factory()->create(['country' => 'Saudi Arabia']);
        // Old rows: the query builder does not run the model normalization.
        DB::table('business')->where('id', $saudi->id)->update(['currency' => 'ر.س']);
        DB::table('business')->where('id', $sar->id)->update(['currency' => 'SAR']);
        DB::table('business')->where('id', $yemen->id)->update(['currency' => '﷼']);
        DB::table('business')->where('id', $usd->id)->update(['currency' => '$']);

        $migration = require database_path('migrations/2026_11_13_000000_use_saudi_riyal_sign_for_business_currency.php');

        $migration->up();
        $this->assertSame(self::SIGN, DB::table('business')->where('id', $saudi->id)->value('currency'));
        $this->assertSame(self::SIGN, DB::table('business')->where('id', $sar->id)->value('currency'));
        $this->assertSame('﷼', DB::table('business')->where('id', $yemen->id)->value('currency'));
        $this->assertSame('$', DB::table('business')->where('id', $usd->id)->value('currency'));

        $migration->down();
        $this->assertSame('ر.س', DB::table('business')->where('id', $saudi->id)->value('currency'));
        $this->assertSame('ر.س', DB::table('business')->where('id', $sar->id)->value('currency'));
        $this->assertSame('﷼', DB::table('business')->where('id', $yemen->id)->value('currency'));
        $this->assertSame('$', DB::table('business')->where('id', $usd->id)->value('currency'));
    }

    private function registration(string $currency): array
    {
        return [
            'name' => 'Riyal Owner',
            'email' => 'riyal@example.com',
            'role' => 'Owner',
            'password' => 'asdfasdf',
            'password_confirmation' => 'asdfasdf',
            'business' => [
                'name' => 'Riyal Shop',
                'logo' => null,
                'country' => 'Saudi Arabia',
                'city' => 'Riyadh',
                'address' => 'King Fahd Road',
                'currency' => $currency,
                'phone' => '512345678',
                'countryCallingCode' => '+966',
                'taxPercent' => 0.15,
                'taxIdentificationNumber' => null,
            ],
        ];
    }
}
