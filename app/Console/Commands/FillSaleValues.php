<?php

namespace App\Console\Commands;

use App\Support\SaleValues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Runs after the migrations (docker/entrypoint.sh) and every hour (App\Console\Kernel),
 * so that bills written by the old code during a rolling deploy get their sale values.
 */
class FillSaleValues extends Command
{
    protected $signature = 'bills:fill-sale-values';

    protected $description = 'Fill the stored tax rate, currency, unit price, and product name of bills that do not have them yet';

    public function handle(): int
    {
        $filled = SaleValues::fillMissing();
        $line = "Filled sale values: bills={$filled['bills']}, bill_details={$filled['bill_details']}, riyal_signs={$filled['riyal_signs']}";
        if (array_sum($filled) > 0)
            Log::info($line);
        $this->line($line);
        return self::SUCCESS;
    }
}
