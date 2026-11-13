<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The QR code on a printed receipt links to /bill/{id}. A guest can open the
 * bill for 180 days (BillPolicy::show; the privacy policy states this).
 * Carbon 3 diffInDays() is signed: without its `true` (absolute) argument,
 * every bill would stay public.
 */
class BillPublicLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function billCreatedDaysAgo(int $days): Bill
    {
        $date = now()->subDays($days);
        return Bill::factory()->create(['created_at' => $date, 'updated_at' => $date]);
    }

    public function test_guest_can_open_a_recent_bill(): void
    {
        $bill = $this->billCreatedDaysAgo(10);

        $this->get('/bill/' . $bill->id)->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Authenticated/Bill/index'));
    }

    public function test_guest_cannot_open_a_bill_older_than_180_days(): void
    {
        $bill = $this->billCreatedDaysAgo(200);

        $this->get('/bill/' . $bill->id)->assertNotFound();
    }

    public function test_account_of_the_business_can_open_an_old_bill(): void
    {
        $bill = $this->billCreatedDaysAgo(200);
        $user = User::factory()->create(['business_id' => $bill->business_id]);

        $this->actingAs($user)->get('/bill/' . $bill->id)->assertOk();
    }
}
