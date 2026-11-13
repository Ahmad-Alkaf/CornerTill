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
 */
class RestoreBusiness extends Command
{
    protected $signature = 'business:restore {id : The ID of the deleted business}';

    protected $description = 'Restore a deleted business and the accounts that were deleted with it';

    public function handle(): int
    {
        $business = Business::onlyTrashed()->find($this->argument('id'));
        if ($business === null) {
            $this->error('No deleted business has this ID. It is not deleted, or it was deleted permanently.');
            return self::FAILURE;
        }

        $accounts = DB::transaction(function () use ($business) {
            // BusinessController::destroy gives the business and its accounts the same
            // time. Accounts that the Owner removed before stay removed.
            $accounts = User::onlyTrashed()
                ->where('business_id', $business->id)
                ->where('deleted_at', $business->deleted_at)
                ->restore();
            $business->restore();
            return $accounts;
        });

        $this->info("Restored business {$business->id} and {$accounts} account(s).");
        return self::SUCCESS;
    }
}
