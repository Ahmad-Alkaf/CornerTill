<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The HTML has only the Ziggy route list (JSON). The route() function comes
 * from the ziggy-js package in the bundle (resources/js/ziggy.ts), so it is
 * not inline in each HTML response.
 */
class ZiggyRoutesTest extends TestCase
{
    public function test_page_has_the_route_list_as_json_and_no_inline_route_function(): void
    {
        $this->withoutVite();

        $html = $this->get('/login')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('#<script id="ziggy-routes-json" type="application/json">\{.*"login":\{"uri":"login"#', $html);
        $this->assertStringNotContainsString('const Ziggy=', $html);
    }
}
