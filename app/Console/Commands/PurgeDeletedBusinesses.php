<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Support\BusinessPurger;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Daily (App\Console\Kernel). Can also run by hand:
 *   php artisan business:purge-deleted --dry-run
 */
class PurgeDeletedBusinesses extends Command
{
    protected $signature = 'business:purge-deleted
                            {--dry-run : Show what would be deleted, and delete nothing}';

    protected $description = 'Permanently delete all data of each business that was deleted more than constants.deletedBusinessRetentionDays days ago';

    public function handle(BusinessPurger $purger): int
    {
        $days = (int) config('constants.deletedBusinessRetentionDays');
        $dryRun = (bool) $this->option('dry-run');

        $ids = Business::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays($days))
            ->orderBy('deleted_at')
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->info("No business was deleted more than {$days} days ago.");
            return self::SUCCESS;
        }

        $failed = 0;
        foreach ($ids as $id) {
            if ($dryRun) {
                $blocker = $purger->blocker($id);
                $this->line($blocker ?? "Would purge business {$id}: " . $this->format($purger->count($id)));
                continue;
            }
            try {
                $line = "Purged deleted business {$id}: " . $this->format($purger->purge($id));
                Log::info($line);
                $this->line($line);
            } catch (Throwable $e) {
                $failed++;
                // No exception message: a SQL error contains the query values (for example email addresses).
                $reason = $e instanceof QueryException ? 'SQLSTATE ' . $e->getCode() : get_class($e) . ': ' . $e->getMessage();
                Log::error("Could not purge deleted business {$id} ({$reason}). The next run tries again.");
                $this->error("Could not purge business {$id} ({$reason}).");
            }
        }

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    /** @param array<string,int> $counts */
    private function format(array $counts): string
    {
        return collect($counts)->map(fn ($n, $k) => "{$k}={$n}")->implode(', ');
    }
}
