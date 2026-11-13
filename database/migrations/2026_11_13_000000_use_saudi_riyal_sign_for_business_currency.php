<?php

use App\Support\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Existing businesses: change old Saudi riyal symbols (for example 'ر.س') to the
 * new Saudi riyal sign U+20C1. App\Support\Currency::normalize() selects the rows.
 * It uses the query builder, so soft-deleted businesses change too.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('business')
            ->select(['id', 'currency', 'country'])
            ->orderBy('id')
            ->chunkById(500, function ($businesses) {
                foreach ($businesses as $business) {
                    $currency = Currency::normalize($business->currency, $business->country);
                    if ($currency !== $business->currency)
                        DB::table('business')->where('id', $business->id)->update(['currency' => $currency]);
                }
            });
    }

    /**
     * Back to 'ر.س', the symbol that the registration form stored before.
     * A business that had an other old symbol (for example 'SAR') also gets 'ر.س'.
     */
    public function down(): void
    {
        DB::table('business')
            ->where('currency', Currency::SAUDI_RIYAL_SIGN)
            ->update(['currency' => 'ر.س']);
    }
};
