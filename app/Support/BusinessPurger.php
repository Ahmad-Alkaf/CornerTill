<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Deletes all data of a deleted (soft-deleted) business permanently: bills and
 * bill details, products and their image files, the logo file, all accounts
 * (also deleted ones) with their tokens and password-reset rows, the Paddle
 * rows, and the business row.
 *
 * Bills and products go in chunks; each chunk is one transaction. The last
 * transaction deletes the accounts and the business row. If a step fails, its
 * transaction rolls back and the next run continues: the business row stays
 * until the end, and deleting a file that is already gone is not an error.
 * A file is deleted inside the transaction of its row: if the file delete
 * fails, the row stays, and the next run tries again.
 */
class BusinessPurger
{
    private const CHUNK = 500;

    /**
     * @return array<string,int> what each table lost (no personal data)
     */
    public function purge(string $businessId): array
    {
        $counts = $this->emptyCounts();
        $business = DB::table('business')->where('id', $businessId)->first();
        if ($business === null)
            return $counts; // already purged
        if ($business->deleted_at === null)
            throw new RuntimeException("Business {$businessId} is not deleted.");

        // 1. Bills and bill details.
        while (($billIds = DB::table('bills')->where('business_id', $businessId)->orderBy('id')->limit(self::CHUNK)->pluck('id'))->isNotEmpty()) {
            DB::transaction(function () use ($billIds, &$counts) {
                $counts['bill_details'] += DB::table('bill_details')->whereIn('bill_id', $billIds)->delete();
                $counts['bills'] += DB::table('bills')->whereIn('id', $billIds)->delete();
            });
        }

        // A product of this business in a bill of another business (only possible
        // with data from before products were checked per business): stop, and do
        // not delete data of the other business.
        $usedElsewhere = DB::table('bill_details')
            ->join('products', 'products.id', '=', 'bill_details.product_id')
            ->where('products.business_id', $businessId)
            ->count();
        if ($usedElsewhere > 0)
            throw new RuntimeException("{$usedElsewhere} bill details of other businesses use products of business {$businessId}.");

        // 2. Products and their image files.
        while (($products = DB::table('products')->where('business_id', $businessId)->orderBy('id')->limit(self::CHUNK)->get(['id', 'img']))->isNotEmpty()) {
            DB::transaction(function () use ($products, &$counts) {
                $counts['products'] += DB::table('products')->whereIn('id', $products->pluck('id'))->delete();
                foreach ($products as $product) {
                    if ($product->img !== null && $this->deleteFile('products-images', $product->img))
                        $counts['product_images']++;
                }
            });
        }

        // 3. Accounts, Paddle rows, the business row, and the logo file.
        DB::transaction(function () use ($business, $businessId, &$counts) {
            $accounts = DB::table('users')->where('business_id', $businessId)->get(['id', 'email']);
            $userIds = $accounts->pluck('id');

            $subscriptionIds = DB::table('subscriptions')->where('billable_id', $businessId)->pluck('id');
            DB::table('subscription_items')->whereIn('subscription_id', $subscriptionIds)->delete();
            DB::table('subscriptions')->where('billable_id', $businessId)->delete();
            DB::table('transactions')->where('billable_id', $businessId)->delete();
            DB::table('customers')->where('billable_id', $businessId)->delete();

            DB::table('personal_access_tokens')
                ->where('tokenable_type', User::class)
                ->whereIn('tokenable_id', $userIds)
                ->delete();
            DB::table('password_reset_tokens')->whereIn('email', $accounts->pluck('email'))->delete();
            // Sessions are files (SESSION_DRIVER=file); a "sessions" table exists only with the database driver.
            if (Schema::hasTable('sessions'))
                DB::table('sessions')->whereIn('user_id', $userIds)->delete();

            $counts['accounts'] = DB::table('users')->whereIn('id', $userIds)->delete();
            DB::table('business')->where('id', $businessId)->delete();

            if ($business->logo !== null && $this->deleteFile('businesses-logo', $business->logo))
                $counts['logo'] = 1;
        });

        return $counts;
    }

    /**
     * What purge() would delete. For `--dry-run`.
     *
     * @return array<string,int>
     */
    public function count(string $businessId): array
    {
        $business = DB::table('business')->where('id', $businessId)->first();
        if ($business === null)
            return $this->emptyCounts();

        return [
            'bills' => DB::table('bills')->where('business_id', $businessId)->count(),
            'bill_details' => DB::table('bill_details')
                ->join('bills', 'bills.id', '=', 'bill_details.bill_id')
                ->where('bills.business_id', $businessId)
                ->count(),
            'products' => DB::table('products')->where('business_id', $businessId)->count(),
            'product_images' => DB::table('products')->where('business_id', $businessId)->get(['img'])
                ->filter(fn ($p) => $p->img !== null && $this->storedPath($p->img) !== null)->count(),
            'accounts' => DB::table('users')->where('business_id', $businessId)->count(),
            'logo' => $business->logo !== null && $this->storedPath($business->logo) !== null ? 1 : 0,
        ];
    }

    /** @return array<string,int> */
    private function emptyCounts(): array
    {
        return ['bills' => 0, 'bill_details' => 0, 'products' => 0, 'product_images' => 0, 'accounts' => 0, 'logo' => 0];
    }

    /**
     * The path of an uploaded file on its disk, as the controllers store it: a
     * name on the local disk, a URL on S3. Null for an external URL on the local
     * disk (for example demo data): there is no file to delete.
     */
    private function storedPath(string $value): ?string
    {
        if (config('filesystems.default') == 's3')
            return basename($value);
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://'))
            return null;
        return $value;
    }

    /** Delete an uploaded file. A file that is already gone is not an error. */
    private function deleteFile(string $disk, string $value): bool
    {
        $path = $this->storedPath($value);
        if ($path === null)
            return false;
        $storage = Storage::disk($disk);
        if (!$storage->exists($path))
            return false;
        if (!$storage->delete($path))
            throw new RuntimeException("Could not delete a file on the {$disk} disk.");
        return true;
    }
}
