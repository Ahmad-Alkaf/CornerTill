<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A finished sale must not change when the tax rate, the currency, or a product changes.
 */
class BillSaleValuesTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Product $apple;

    protected function setUp(): void
    {
        parent::setUp();
        config(['features.payments_enabled' => false]);
        $this->owner = User::factory()->create(['role' => 'Owner', 'email_verified_at' => now(), 'deleted_at' => null]);
        $this->owner->business->update(['taxPercent' => 0.15, 'currency' => '$']);
        $this->apple = $this->product('Apple', 7);
    }

    private function product(string $name, ?float $price): Product
    {
        return Product::factory()->create([
            'name' => $name, 'price' => $price, 'stock' => null, 'img' => null,
            'business_id' => $this->owner->business_id, 'createdBy_id' => $this->owner->id,
        ]);
    }

    private function sell(array $lines, ?string $oldBillId = null): Bill
    {
        $payload = ['cashReceived' => null, 'bill_details' => $lines];
        if ($oldBillId) $payload['id'] = $oldBillId;
        $response = $this->actingAs($this->owner)->post(route('bill.store'), $payload);
        $response->assertSessionHasNoErrors()->assertRedirect(route('bill.create'));
        $id = session('createdUpdatedBillId');
        $this->assertNotNull($id);
        return Bill::with('bill_details')->findOrFail($id);
    }

    public function test_a_sale_stores_the_tax_rate_currency_price_and_name(): void
    {
        $bill = $this->sell([['product_id' => $this->apple->id, 'quantity' => 2]]);

        $this->assertSame(0.15, $bill->taxPercent);
        $this->assertSame('$', $bill->currency);
        $this->assertSame(7.0, $bill->bill_details[0]->price);
        $this->assertSame('Apple', $bill->bill_details[0]->productName);
    }

    public function test_later_changes_do_not_change_the_bill_or_the_dashboard_values(): void
    {
        $bill = $this->sell([['product_id' => $this->apple->id, 'quantity' => 2]]);

        $this->owner->business->update(['taxPercent' => 0.10, 'currency' => '€']);
        $this->apple->update(['price' => 9, 'name' => 'Green apple']);

        $this->actingAs($this->owner)->get(route('bill.show', $bill->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('bill.taxPercent', 0.15)
                ->where('bill.currency', '$')
                ->where('bill.bill_details.0.price', 7)
                ->where('bill.bill_details.0.productName', 'Apple')
                ->etc());

        $this->actingAs($this->owner)->get(route('bill.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('bills.data.0.taxPercent', 0.15)
                ->where('bills.data.0.bill_details.0.price', 7)
                ->etc());

        // The dashboard sums use the same stored values (SQL without the products table).
        $sum = (float) DB::table('bills')->join('bill_details', 'bills.id', '=', 'bill_details.bill_id')
            ->selectRaw('SUM(COALESCE(bill_details.price, 0) * bill_details.quantity * (1 + bills.taxPercent)) AS s')
            ->value('s');
        $this->assertEqualsWithDelta(16.1, $sum, 0.0001);
    }

    public function test_an_update_keeps_the_values_of_the_sale_and_prices_new_products_now(): void
    {
        $old = $this->sell([['product_id' => $this->apple->id, 'quantity' => 2]]);
        DB::table('bills')->where('id', $old->id)->update(['created_at' => now()->subDays(3)]);
        $old->refresh();

        $this->owner->business->update(['taxPercent' => 0.10]);
        $this->apple->update(['price' => 9]);
        $pear = $this->product('Pear', 5);

        $new = $this->sell([
            ['product_id' => $this->apple->id, 'quantity' => 3],
            ['product_id' => $pear->id, 'quantity' => 1],
        ], $old->id);

        $this->assertNotSame($old->id, $new->id);
        $this->assertNull(Bill::find($old->id));
        $this->assertSame(0.15, $new->taxPercent);
        $this->assertSame($old->created_at->toDateTimeString(), $new->created_at->toDateTimeString());
        $lines = $new->bill_details->keyBy('product_id');
        $this->assertSame(7.0, $lines[$this->apple->id]->price);
        $this->assertSame(3, $lines[$this->apple->id]->quantity);
        $this->assertSame(5.0, $lines[$pear->id]->price);
        $this->assertSame('Successfully updated', session('success'));
    }

    public function test_the_edit_page_shows_the_prices_of_the_sale(): void
    {
        $bill = $this->sell([['product_id' => $this->apple->id, 'quantity' => 2]]);
        $this->apple->update(['price' => 9, 'name' => 'Green apple']);

        $this->actingAs($this->owner)->get(route('bill.edit', $bill->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('bill.taxPercent', 0.15)
                ->where('bill.bill_details.0.product.price', 7)
                ->where('bill.bill_details.0.product.name', 'Apple')
                ->etc());
        $this->assertSame(9.0, $this->apple->fresh()->price);
    }

    public function test_a_product_of_another_business_cannot_be_sold(): void
    {
        $other = Product::factory()->create(['price' => 1]);

        $this->actingAs($this->owner)->post(route('bill.store'), [
            'cashReceived' => null,
            'bill_details' => [['product_id' => $other->id, 'quantity' => 1]],
        ])->assertSessionHasErrors('serverError');
        $this->assertSame(0, Bill::count());
    }

    public function test_the_migration_copies_the_current_values_into_old_bills(): void
    {
        $migration = require database_path('migrations/9-2026_11_13_000001_store_sale_values_on_bills.php');
        $migration->down();

        $billId = (string) Str::ulid();
        DB::table('bills')->insert([
            'id' => $billId, 'cashReceived' => null, 'createdBy_id' => $this->owner->id,
            'business_id' => $this->owner->business_id, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('bill_details')->insert([
            'id' => (string) Str::ulid(), 'quantity' => 2, 'product_id' => $this->apple->id,
            'bill_id' => $billId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $migration->up();

        $bill = DB::table('bills')->where('id', $billId)->first();
        $this->assertEqualsWithDelta(0.15, (float) $bill->taxPercent, 0.00001);
        $this->assertSame('$', $bill->currency);
        $detail = DB::table('bill_details')->where('bill_id', $billId)->first();
        $this->assertEqualsWithDelta(7.0, (float) $detail->price, 0.00001);
        $this->assertSame('Apple', $detail->productName);
    }
}
