<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * SetLocale: server messages follow the i18next cookie, then Accept-Language.
 */
class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_is_english_and_ltr(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);
    }

    public function test_cookie_ar_gives_arabic_and_rtl(): void
    {
        $this->withUnencryptedCookie('i18next', 'ar')
            ->get('/')->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false);
    }

    public function test_accept_language_arabic_without_cookie(): void
    {
        $this->withHeader('Accept-Language', 'ar-SA,ar;q=0.9')
            ->get('/')->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false);
    }

    public function test_cookie_wins_over_accept_language(): void
    {
        $this->withUnencryptedCookie('i18next', 'en-US')
            ->withHeader('Accept-Language', 'ar')
            ->get('/')->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);
    }

    public function test_other_languages_get_english(): void
    {
        $this->withHeader('Accept-Language', 'fr-FR,fr;q=0.9')
            ->get('/')->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);
    }

    public function test_validation_messages_follow_the_language(): void
    {
        $this->from('/login')->post('/login', [])
            ->assertSessionHasErrors(['email' => 'The email field is required.']);

        $this->withUnencryptedCookie('i18next', 'ar')
            ->from('/login')->post('/login', [])
            ->assertSessionHasErrors(['email' => 'حقل البريد الإلكتروني مطلوب.']);
    }

    public function test_failed_login_message_in_arabic(): void
    {
        $this->withUnencryptedCookie('i18next', 'ar')
            ->from('/login')->post('/login', ['email' => 'nobody@example.com', 'password' => 'wrong-password'])
            ->assertSessionHasErrors(['email' => 'بيانات الدخول هذه لا تطابق سجلاتنا.']);
    }

    public function test_beta_limit_message_in_both_languages(): void
    {
        config([
            'features.payments_enabled' => false,
            'constants.betaResources' => ['accounts' => 0, 'products' => 0, 'bills' => 0],
        ]);
        $owner = User::factory()->create(['role' => 'Owner', 'email_verified_at' => now(), 'deleted_at' => null]);

        $message = function () use ($owner): string {
            try {
                Gate::forUser($owner)->authorize('store', Product::class);
            } catch (HttpException $e) {
                return $e->getMessage();
            }
            $this->fail('The policy did not refuse.');
        };

        app()->setLocale('en');
        $this->assertSame('You reached the beta limit. Contact support@kaflabs.com for more.', $message());
        app()->setLocale('ar');
        $this->assertSame('لقد وصلت إلى حد النسخة التجريبية. تواصل مع support@kaflabs.com لطلب المزيد.', $message());
    }

    public function test_flash_message_follows_the_language(): void
    {
        $owner = User::factory()->create(['role' => 'Owner', 'email_verified_at' => now(), 'deleted_at' => null]);

        $this->actingAs($owner)->withUnencryptedCookie('i18next', 'ar')
            ->post('/logout')
            ->assertSessionHas('message', 'نراك قريبًا 👋');
    }

    public function test_error_pages_follow_the_language(): void
    {
        $this->get('/no-such-page')->assertNotFound()
            ->assertSee('dir="ltr"', false)->assertSee('Not Found');

        $this->withUnencryptedCookie('i18next', 'ar')
            ->get('/no-such-page')->assertNotFound()
            ->assertSee('dir="rtl"', false)->assertSee('الصفحة غير موجودة');
    }

    public function test_beta_limit_error_page_in_arabic(): void
    {
        config([
            'features.payments_enabled' => false,
            'constants.betaResources' => ['accounts' => 1, 'products' => 0, 'bills' => 0],
        ]);
        $owner = User::factory()->create(['role' => 'Owner', 'email_verified_at' => now(), 'deleted_at' => null]);

        $this->actingAs($owner)->withUnencryptedCookie('i18next', 'ar')
            ->post('/account', [
                'name' => 'Staff', 'email' => 'staff@example.com', 'role' => 'Cashier',
                'password' => 'password123', 'password_confirmation' => 'password123',
            ])
            ->assertForbidden()
            ->assertSee('لقد وصلت إلى حد النسخة التجريبية', false);
    }
}
