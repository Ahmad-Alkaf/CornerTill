<?php

namespace Tests\Feature\Business;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The business page has three forms. Only the Identity form (name and logo)
 * can change the logo.
 */
class BusinessLogoTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'local']);
        Storage::fake('businesses-logo');
        $this->owner = User::factory()->create([
            'role' => 'Owner',
            'email_verified_at' => now(),
            'deleted_at' => null,
        ]);
        $this->owner->business->update(['logo' => null]);
    }

    private function uploadLogo(): string
    {
        $business = $this->owner->business->fresh();
        $this->actingAs($this->owner)
            ->patch(route('business.update', $business->id), [
                'name' => $business->name,
                'logo' => $business->logo,
                'logoFile' => UploadedFile::fake()->image('logo.png', 40, 40),
            ])
            ->assertSessionHasNoErrors();
        $logo = $business->fresh()->logo;
        $this->assertNotNull($logo);
        Storage::disk('businesses-logo')->assertExists($logo);
        return $logo;
    }

    public function test_location_and_financial_forms_keep_the_logo(): void
    {
        $logo = $this->uploadLogo();
        $business = $this->owner->business->fresh();

        // Location & Contact form: no `logo` key.
        $this->actingAs($this->owner)->patch(route('business.update', $business->id), [
            'country' => 'Saudi Arabia',
            'city' => 'Jeddah',
            'address' => 'Street 1',
            'phone' => '512345678',
            'countryCallingCode' => '+966',
        ])->assertSessionHasNoErrors();

        // Financial form: no `logo` key.
        $this->actingAs($this->owner)->patch(route('business.update', $business->id), [
            'currency' => '$',
            'taxPercent' => 0.1,
            'taxIdentificationNumber' => null,
        ])->assertSessionHasNoErrors();

        $this->assertSame($logo, $business->fresh()->logo);
        $this->assertSame('Jeddah', $business->fresh()->city);
        Storage::disk('businesses-logo')->assertExists($logo);
    }

    public function test_a_new_logo_replaces_the_old_file(): void
    {
        $old = $this->uploadLogo();
        $new = $this->uploadLogo();

        $this->assertNotSame($old, $new);
        Storage::disk('businesses-logo')->assertMissing($old);
        Storage::disk('businesses-logo')->assertExists($new);
    }

    public function test_an_explicit_null_logo_removes_the_file(): void
    {
        $logo = $this->uploadLogo();
        $business = $this->owner->business->fresh();

        $this->actingAs($this->owner)->patch(route('business.update', $business->id), [
            'name' => $business->name,
            'logo' => null,
        ])->assertSessionHasNoErrors();

        $this->assertNull($business->fresh()->logo);
        Storage::disk('businesses-logo')->assertMissing($logo);
    }

    public function test_a_logo_name_from_the_client_is_ignored(): void
    {
        $logo = $this->uploadLogo();
        Storage::disk('businesses-logo')->put('other-business.png', 'x');
        $business = $this->owner->business->fresh();

        $this->actingAs($this->owner)->patch(route('business.update', $business->id), [
            'name' => $business->name,
            'logo' => 'other-business.png',
        ])->assertSessionHasNoErrors();

        $this->assertSame($logo, $business->fresh()->logo);
        Storage::disk('businesses-logo')->assertExists('other-business.png');
        Storage::disk('businesses-logo')->assertExists($logo);
    }
}
