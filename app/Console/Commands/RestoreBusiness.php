<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Support restores a business that the Owner deleted by mistake:
 *   php artisan business:restore {id}
 * Possible until `business:purge-deleted` deletes it permanently.
 * Also restores single accounts of a business: --with-user=<id> (also after the
 * business was restored, for example when no account came back).
 */
class RestoreBusiness extends Command
{
    protected $signature = 'business:restore
                            {id : The ID of the deleted business}
                            {--with-user=* : Also restore this deleted account of the business (an ID; repeat for more)}
                            {--force : Restore also when the business was deleted more than constants.deletedBusinessRetentionDays days ago}';

    protected $description = 'Restore a deleted business and the accounts that were deleted with it';

    /** Accounts deleted this close to the business count as "deleted with it" (the old code used two separate times). */
    private const SAME_DELETE_SECONDS = 5;

    public function handle(): int
    {
        $id = (string) $this->argument('id');
        $business = Business::withTrashed()->find($id);
        if ($business === null) {
            $this->error('No business has this ID. It was deleted permanently, or the ID is wrong.');
            return self::FAILURE;
        }
        if (!$business->trashed() && empty($this->option('with-user'))) {
            $this->error('This business is not deleted. To restore single accounts of it, use --with-user=<id>.');
            return self::FAILURE;
        }

        // Each --with-user must be a deleted account of this business. Check all
        // of them before anything changes.
        $requested = collect($this->option('with-user'))->unique();
        $known = User::onlyTrashed()->where('business_id', $id)->whereIn('id', $requested)->pluck('id');
        $unknown = $requested->diff($known);
        if ($unknown->isNotEmpty()) {
            foreach ($unknown as $userId)
                $this->error("--with-user={$userId} is not a deleted account of business {$id}. Nothing was restored.");
            return self::FAILURE;
        }

        $counts = [
            'bills' => DB::table('bills')->where('business_id', $id)->count(),
            'products' => DB::table('products')->where('business_id', $id)->count(),
            'accounts' => DB::table('users')->where('business_id', $id)->count(),
        ];
        $countsText = collect($counts)->map(fn ($n, $k) => "{$k}={$n}")->implode(', ');

        // After the retention period, the daily purge may have started (it deletes
        // bills and products first). Restore only after support checked the data.
        $days = (int) config('constants.deletedBusinessRetentionDays');
        if ($business->trashed() && $business->deleted_at->lt(now()->subDays($days)) && !$this->option('force')) {
            $this->error("This business was deleted more than {$days} days ago ({$business->deleted_at}). The daily purge may have deleted part of its data ({$countsText}). Check the data, then run again with --force.");
            return self::FAILURE;
        }

        $restored = DB::transaction(function () use ($business, $id) {
            $ids = collect($this->option('with-user'));
            if ($business->trashed()) {
                $trashed = User::onlyTrashed()->where('business_id', $id);
                // BusinessController::destroy gives the business and its accounts the
                // same time. The old code used two separate times: accept a few seconds.
                $sameTime = (clone $trashed)->where('deleted_at', $business->deleted_at)->pluck('id');
                if ($sameTime->isEmpty())
                    $sameTime = (clone $trashed)->whereBetween('deleted_at', [
                        $business->deleted_at->copy()->subSeconds(self::SAME_DELETE_SECONDS),
                        $business->deleted_at->copy()->addSeconds(self::SAME_DELETE_SECONDS),
                    ])->pluck('id');
                $ids = $ids->merge($sameTime);
                $business->restore();
            }
            return User::onlyTrashed()->where('business_id', $id)->whereIn('id', $ids->unique())->restore();
        });

        $this->info("Restored business {$id} and {$restored} account(s). Data: {$countsText}.");

        // Accounts that stay deleted (for example staff that the Owner removed before).
        $stillDeleted = User::onlyTrashed()->where('business_id', $id)->orderBy('deleted_at')->get(['id', 'role', 'deleted_at']);
        if ($stillDeleted->isNotEmpty()) {
            $this->line('These accounts stay deleted. To restore one: php artisan business:restore ' . $id . ' --with-user=<id>');
            $this->table(['id', 'role', 'deleted_at'], $stillDeleted->map(fn ($u) => [$u->id, $u->role, (string) $u->deleted_at])->all());
        }
        if (!User::where('business_id', $id)->exists())
            $this->warn('No account of this business is active, so nobody can sign in. Restore the Owner account with --with-user=<id>.');

        return self::SUCCESS;
    }
}
