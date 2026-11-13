<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Only the product form (with its Change and Remove image buttons) can change
 * the product image. The "Update Stock" form sends only `stock`.
 */
class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => 'local', 'features.payments_enabled' => false]);
        Storage::fake('products-images');
        Storage::disk('products-images')->put('apple.png', 'x');
        $this->owner = User::factory()->create([
            'role' => 'Owner',
            'email_verified_at' => now(),
            'deleted_at' => null,
        ]);
        $this->product = Product::factory()->create([
            'business_id' => $this->owner->business_id,
            'createdBy_id' => $this->owner->id,
            'img' => 'apple.png',
            'stock' => 3,
        ]);
    }

    public function test_update_stock_keeps_the_image(): void
    {
        $this->actingAs($this->owner)
            ->patch(route('product.update', $this->product->id), ['stock' => 10])
            ->assertSessionHasNoErrors();

        $this->assertSame(10, $this->product->fresh()->stock);
        $this->assertSame('apple.png', $this->product->fresh()->img);
        Storage::disk('products-images')->assertExists('apple.png');
    }

    public function test_an_explicit_null_image_removes_the_file(): void
    {
        $this->actingAs($this->owner)
            ->patch(route('product.update', $this->product->id), [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'img' => null,
            ])
            ->assertSessionHasNoErrors();

        $this->assertNull($this->product->fresh()->img);
        Storage::disk('products-images')->assertMissing('apple.png');
    }

    public function test_an_image_name_from_the_client_is_ignored(): void
    {
        Storage::disk('products-images')->put('other.png', 'x');
        $this->actingAs($this->owner)
            ->patch(route('product.update', $this->product->id), [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'img' => 'other.png',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('apple.png', $this->product->fresh()->img);
        Storage::disk('products-images')->assertExists('other.png');
    }
}
