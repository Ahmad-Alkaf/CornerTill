<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Demo data for the README and welcome-page screenshots (public/assets/screenshots).
 * It recreates the data of the 2023 screenshots: the 10 food products (prices,
 * stock, images), the owner "Ali" and the cashier "Rogers Gorczany", and bills
 * that give dashboard numbers close to the old ones.
 *
 * Use it only on an empty, disposable database:
 *   php artisan migrate:fresh --force
 *   php artisan db:seed --class=ScreenshotDemoSeeder --force
 * Log in as ali@example.com / password. Take the screenshots soon after seeding:
 * the dashboard counts bills relative to the current time.
 */
class ScreenshotDemoSeeder extends Seeder
{
    private const TAX = 0.15;

    /** Shown price (tax included) => [name, stock, image file in storage/products-template-images]. Newest first. */
    private const PRODUCTS = [
        ['Rice Chicken', 29, 8, 'rice chicken.jpg'],
        ['Pizza', 18, 0, 'pizza.jpg'],
        ['Pasta', 15, 40, 'pasta.jpg'],
        ['French Fries', 3.45, 31, 'french fries.jpg'],
        ['Curry with Rice', 20, 22, 'curry with rice.jpg'],
        ['Burger', 6, 25, 'burger.jpg'],
        ['Orange Juice', 4, 11, 'orange juice.jpg'],
        ['Red Tea', 3, 40, 'tea.jpg'],
        ['Coca Cola', 2.5, 124, 'Coca Cola.jpg'],
        ['Water', 1, 86, 'water bottle.jpg'],
    ];

    /** Monthly sales (tax included) of the 2023 dashboard, oldest month first; the last value is the current month. */
    private const MONTHLY_SALES = [200000, 188000, 160000, 188000, 225000, 200000, 205000, 173000, 213000, 180000, 160000, 200000];

    /** Bills per day of the last 7 days (the last value is today), and of the 7 days before. */
    private const BILLS_THIS_WEEK = [6, 14, 10, 8, 8, 7, 15];
    private const BILLS_LAST_WEEK = 63;
    private const CASH_BILLS_THIS_WEEK = 42;
    private const SALES_TODAY = 11465.88;
    private const SALES_YESTERDAY = 9438.53; // 11465.88 is 21.48 % more

    private array $products = [];
    private string $ali;
    private string $rogers;
    private string $businessId;
    private array $bills = [];
    private array $details = [];

    public function run(): void
    {
        mt_srand(2023);
        $now = Carbon::now();

        $this->businessId = (string) Str::ulid();
        DB::table('business')->insert([
            'id' => $this->businessId, 'name' => 'Corner Cafe', 'country' => 'United States', 'city' => 'Springfield',
            'address' => '12 Market Street', 'currency' => '$', 'taxPercent' => self::TAX, 'countryCallingCode' => '+1',
            'phone' => '5550100', 'created_at' => $now->copy()->subYears(2), 'updated_at' => $now,
        ]);
        $this->ali = $this->user('Ali', 'ali@example.com', 'Owner', $now);
        $this->rogers = $this->user('Rogers Gorczany', 'rogers@example.com', 'Cashier', $now);

        // Products: the stored price has no tax; the app shows price * (1 + tax).
        foreach (array_reverse(self::PRODUCTS) as $i => [$name, $shown, $stock, $image]) {
            $file = Str::slug(pathinfo($image, PATHINFO_FILENAME)) . '.jpg';
            Storage::disk('products-images')->put($file, file_get_contents(storage_path('products-template-images/' . $image)));
            $price = round($shown / (1 + self::TAX), 2);
            $id = (string) Str::ulid();
            DB::table('products')->insert([
                'id' => $id, 'name' => $name, 'price' => $price, 'stock' => $stock, 'img' => $file,
                'createdBy_id' => $this->ali, 'business_id' => $this->businessId,
                'created_at' => $now->copy()->subDays(30)->addMinutes($i), 'updated_at' => $now,
            ]);
            $this->products[] = ['id' => $id, 'name' => $name, 'price' => $price, 'unit' => $price * (1 + self::TAX)];
        }

        $today = $now->copy()->startOfDay();

        // This week: today (15 bills), yesterday (7 bills), and 5 earlier days.
        $cashLeft = self::CASH_BILLS_THIS_WEEK;
        $billsLeft = array_sum(self::BILLS_THIS_WEEK);
        foreach (self::BILLS_THIS_WEEK as $k => $count) {
            $daysAgo = 6 - $k;
            $day = $today->copy()->subDays($daysAgo);
            $times = $this->timesInDay($day, $count, $daysAgo === 0 ? $now->copy()->subMinutes(30) : null, $now);
            if ($daysAgo === 6) // the 7-day window starts 6 days ago at this hour
                $times = array_map(fn ($t) => $t->copy()->max($now->copy()->subDays(6)->addHours(1))->min($day->copy()->setTime(23, 50)), $times);
            $dayTarget = match ($daysAgo) { 0 => self::SALES_TODAY, 1 => self::SALES_YESTERDAY, default => 700 * $count };
            $dayReal = 0;
            foreach ($this->split($dayTarget, $count) as $j => $amount) {
                $cash = mt_rand(1, $billsLeft) <= $cashLeft;
                $billsLeft--;
                if ($cash) $cashLeft--;
                if ($j === $count - 1) $amount = $dayTarget - $dayReal; // the last bill takes the rounding
                $dayReal += $this->bill($times[$j], $amount, $cash, $this->creatorLast24h($times[$j], $now));
            }
        }

        // Previous week (63 bills), between 13 and 7 days ago.
        $lastWeekStart = $now->copy()->subDays(13)->addHours(1);
        $lastWeekEnd = $now->copy()->subDays(7)->subHours(1);
        $lastWeekTotal = 0;
        foreach ($this->split(700 * self::BILLS_LAST_WEEK, self::BILLS_LAST_WEEK) as $amount) {
            $at = Carbon::createFromTimestamp(mt_rand($lastWeekStart->timestamp, $lastWeekEnd->timestamp));
            $lastWeekTotal += $this->bill($at, $amount, mt_rand(1, 100) <= 60, $this->filler());
        }

        // Older bills fill each month up to its monthly total. They stay outside the
        // windows above, so the weekly and daily numbers do not change.
        $weekStart = $now->copy()->subWeek()->addDay();
        $monthTotals = $this->monthTotalsSoFar();
        foreach (self::MONTHLY_SALES as $m => $target) {
            $monthStart = $now->copy()->startOfMonth()->subMonths(11 - $m);
            $from = $monthStart->copy();
            $to = $monthStart->copy()->endOfMonth();
            if ($to->greaterThan($lastWeekStart)) $to = $m === 11 ? $weekStart->copy()->subHours(1) : $lastWeekStart->copy()->subHours(1);
            $left = $target - ($monthTotals[$monthStart->format('Y-m')] ?? 0);
            if ($left <= 0 || $to->lessThanOrEqualTo($from)) continue;
            $count = max(1, (int) round($left / 750));
            foreach ($this->split($left, $count) as $amount) {
                $at = Carbon::createFromTimestamp(mt_rand($from->timestamp, $to->timestamp));
                $this->bill($at, $amount, mt_rand(1, 100) <= 60, $this->filler());
            }
        }

        foreach (array_chunk($this->bills, 500) as $chunk) DB::table('bills')->insert($chunk);
        foreach (array_chunk($this->details, 1000) as $chunk) DB::table('bill_details')->insert($chunk);
    }

    private function user(string $name, string $email, string $role, Carbon $now): string
    {
        $id = (string) Str::ulid();
        DB::table('users')->insert([
            'id' => $id, 'name' => $name, 'email' => $email, 'password' => Hash::make('password'), 'role' => $role,
            'email_verified_at' => $now, 'business_id' => $this->businessId,
            'created_at' => $now->copy()->subYears(2), 'updated_at' => $now,
        ]);
        return $id;
    }

    /** Last 24 hours: 13 bills by Ali and 4 by Rogers, as in the 2023 dashboard. */
    private int $last24h = 0;
    private function creatorLast24h(Carbon $at, Carbon $now): string
    {
        if (!$at->greaterThan($now->copy()->subHours(24))) return $this->ali;
        // 17 bills in the last 24 hours: numbers 1, 5, 9, 13 are by Rogers.
        return $this->last24h++ % 4 === 1 ? $this->rogers : $this->ali;
    }

    private function filler(bool $mixed = true): string
    {
        return $mixed && mt_rand(1, 4) === 1 ? $this->rogers : $this->ali;
    }

    /** $count times in one day, between 08:00 and 22:00 (or until $until). Yesterday keeps 2 bills inside the last 24 hours. */
    private function timesInDay(Carbon $day, int $count, ?Carbon $until, Carbon $now): array
    {
        $start = $day->copy()->setTime(8, 0);
        $end = $until ?? $day->copy()->setTime(22, 0);
        if ($until !== null && $end->lessThanOrEqualTo($start)) $start = $day->copy()->addMinutes(5);
        $times = [];
        for ($i = 0; $i < $count; $i++)
            $times[] = Carbon::createFromTimestamp($start->timestamp + (int) (($end->timestamp - $start->timestamp) * ($i + 0.5) / $count));
        if ($day->isSameDay($now->copy()->subDay())) {
            // two bills in the last 24 hours, the others before
            $times = array_map(fn ($t) => $t->copy()->min($now->copy()->subHours(25)), $times);
            $times[$count - 1] = $day->copy()->setTime(23, 30)->max($now->copy()->subHours(23));
            $times[$count - 2] = $day->copy()->setTime(23, 0)->max($now->copy()->subHours(23)->subMinutes(20));
        }
        return $times;
    }

    /** Split $total into $count amounts of different sizes. */
    private function split(float $total, int $count): array
    {
        $weights = [];
        for ($i = 0; $i < $count; $i++) $weights[] = mt_rand(60, 140);
        $sum = array_sum($weights);
        return array_map(fn ($w) => $total * $w / $sum, $weights);
    }

    /** Add one bill with items worth about $amount (tax included). Returns the real amount. */
    private function bill(Carbon $at, float $amount, bool $cash, string $creator): float
    {
        $lines = [];
        $left = $amount;
        $water = 0; // Water is the first inserted product (the cheapest)
        $tries = 0;
        while ($left > 35 && $tries++ < 50) {
            $p = mt_rand(0, count($this->products) - 1);
            $q = min(mt_rand(1, 12), (int) floor($left / $this->products[$p]['unit']));
            if ($q < 1) continue;
            $lines[$p] = ($lines[$p] ?? 0) + $q;
            $left -= $q * $this->products[$p]['unit'];
        }
        $q = (int) floor($left / $this->products[$water]['unit']);
        if ($q > 0) {
            $lines[$water] = ($lines[$water] ?? 0) + $q;
            $left -= $q * $this->products[$water]['unit'];
        }
        $real = $amount - $left;
        $billId = (string) Str::ulid();
        $this->bills[] = [
            'id' => $billId, 'cashReceived' => $cash ? ceil($real / 10) * 10 : null,
            'createdBy_id' => $creator, 'business_id' => $this->businessId, 'created_at' => $at, 'updated_at' => $at,
            'taxPercent' => self::TAX, 'currency' => '$',
        ];
        foreach ($lines as $p => $qty)
            $this->details[] = [
                'id' => (string) Str::ulid(), 'quantity' => $qty, 'product_id' => $this->products[$p]['id'],
                'price' => $this->products[$p]['price'], 'productName' => $this->products[$p]['name'],
                'bill_id' => $billId, 'created_at' => $at, 'updated_at' => $at,
            ];
        return $real;
    }

    /** Sales already added per month (Y-m), tax included. */
    private function monthTotalsSoFar(): array
    {
        $byBill = [];
        foreach ($this->details as $d) {
            $unit = collect($this->products)->firstWhere('id', $d['product_id'])['unit'];
            $byBill[$d['bill_id']] = ($byBill[$d['bill_id']] ?? 0) + $unit * $d['quantity'];
        }
        $totals = [];
        foreach ($this->bills as $b) {
            $key = $b['created_at']->format('Y-m');
            $totals[$key] = ($totals[$key] ?? 0) + ($byBill[$b['id']] ?? 0);
        }
        return $totals;
    }
}
