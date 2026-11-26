<?php

namespace Tests\Feature;

use App\Models\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * There is no Inertia SSR, so search engines and link previews read only the
 * head tags of resources/views/app.blade.php. Each public page has its own
 * title, description, and canonical link.
 */
class PageMetaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public static function publicPages(): array
    {
        return [
            'home' => ['/', 'Point of Sale App', 'Simplify your sales process and boost your productivity. Streamline your business operations with our user-friendly Point of Sale app.'],
            'privacy' => ['/privacy-policy', 'Privacy Policy', 'CornerTill Privacy Policy. Supplement to the KafLabs Privacy Policy.'],
            'terms' => ['/terms-and-conditions', 'Terms', 'CornerTill Terms. Supplement to the KafLabs Terms of Service.'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_page_has_its_own_title_description_and_canonical(string $path, string $title, string $description): void
    {
        // The home page URL has the slash, the same as the Google canonical.
        $url = $path === '/' ? url('/') . '/' : url($path);
        $fullTitle = $title . "\u{202F}-\u{202F}" . config('app.name');

        $this->get($path)->assertOk()
            ->assertSee('<title inertia>' . e($fullTitle) . '</title>', false)
            ->assertSee('<meta name="description" content="' . e($description) . '">', false)
            ->assertSee('<link rel="canonical" href="' . $url . '">', false)
            ->assertSee('<meta property="og:url" content="' . $url . '">', false)
            ->assertSee('<meta property="twitter:url" content="' . $url . '">', false)
            ->assertSee('<meta property="og:image" itemprop="image" content="' . url('/assets/logo/cornertill-logo.png') . '">', false)
            ->assertDontSee('name="robots"', false);
    }

    public function test_arabic_page_has_the_arabic_title_and_description(): void
    {
        config(['app.name' => 'CornerTill']);

        $this->withHeader('Accept-Language', 'ar')->get('/privacy-policy')->assertOk()
            ->assertSee("<title inertia>سياسة الخصوصية\u{202F}-\u{202F}كورنر تِل</title>", false)
            ->assertSee('<meta name="description" content="سياسة الخصوصية في كورنر تِل. ملحق بسياسة الخصوصية لدى KafLabs.">', false);
    }

    public function test_other_pages_have_the_app_name_as_title_and_their_own_canonical(): void
    {
        $this->get('/login')->assertOk()
            ->assertSee('<title inertia>' . e(config('app.name')) . '</title>', false)
            ->assertSee('<link rel="canonical" href="' . url('/login') . '">', false);
    }

    public function test_bill_receipt_is_not_for_search_engines(): void
    {
        $bill = Bill::factory()->create();

        $this->get('/bill/' . $bill->id)->assertOk()
            ->assertSee('<meta name="robots" content="noindex">', false);
    }

    public function test_not_found_page_links_to_the_home_page(): void
    {
        $this->get('/no-such-page')->assertNotFound()
            ->assertSee('<a href="' . url('/') . '"', false)
            ->assertSee('Go to the home page');

        $this->withHeader('Accept-Language', 'ar')->get('/no-such-page')->assertNotFound()
            ->assertSee('اذهب إلى الصفحة الرئيسية');
    }
}
