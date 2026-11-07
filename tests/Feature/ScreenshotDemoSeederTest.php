<?php

namespace Tests\Feature;

use Database\Seeders\ScreenshotDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScreenshotDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_screenshot_demo_data(): void
    {
        Storage::fake('products-images');

        $this->seed(ScreenshotDemoSeeder::class);

        $this->assertSame(10, DB::table('products')->count());
        $this->assertSame(0, (int) DB::table('products')->where('name', 'Pizza')->value('stock'));
        $this->assertEquals(5.22, (float) DB::table('products')->where('name', 'Burger')->value('price')); // $6 with 15 % tax
        $this->assertSame(['Ali', 'Rogers Gorczany'], DB::table('users')->orderBy('name')->pluck('name')->all());
        $this->assertSame(68, DB::table('bills')->where('created_at', '>', now()->subWeek()->addDay())->count());
        Storage::disk('products-images')->assertExists('burger.jpg');
    }
}
