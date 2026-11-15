<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * /sitemap.xml lists the public pages for search engines. robots.txt points
 * to it.
 */
class SitemapTest extends TestCase
{
    public function test_sitemap_lists_the_public_pages(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);

        $urls = [];
        foreach ($xml->url as $url) {
            $urls[] = (string) $url->loc;
        }

        $this->assertSame([
            url('/'),
            route('privacyPolicy'),
            route('termsAndConditions'),
        ], $urls);
    }

    public function test_sitemap_has_no_private_pages(): void
    {
        $content = $this->get('/sitemap.xml')->getContent();

        foreach (['dashboard', 'inventory', 'bill', 'profile', 'login', 'register'] as $path) {
            $this->assertStringNotContainsString('/'.$path, $content);
        }
    }

    public function test_robots_points_to_the_sitemap(): void
    {
        $this->assertStringContainsString(
            'Sitemap: https://cornertill.com/sitemap.xml',
            file_get_contents(public_path('robots.txt'))
        );
    }
}
