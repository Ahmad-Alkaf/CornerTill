<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Fills the sale values of bills that do not have them yet: bills.taxPercent and
 * bills.currency from the business, bill_details.price and productName from the
 * product. Such rows come from the old code, for example from the old container
 * that still serves during a rolling deploy.
 *
 * Only NULL values change, so it is safe to run any number of times.
 * bill_details.productName is NULL only on rows that were never filled (the
 * product name is required); a NULL price with a productName is a product that
 * had no price, and stays NULL.
 */
class SaleValues
{
    /** @return array{bills:int, bill_details:int, riyal_signs:int} the number of rows filled or changed */
    public static function fillMissing(): array
    {
        $bills = self::updateIfAny(DB::table('bills')->whereNull('taxPercent'), [
            'taxPercent' => DB::raw('(SELECT business.taxPercent FROM business WHERE business.id = bills.business_id)'),
        ]);
        $bills += self::updateIfAny(DB::table('bills')->whereNull('currency'), [
            'currency' => DB::raw('(SELECT business.currency FROM business WHERE business.id = bills.business_id)'),
        ]);
        $details = self::updateIfAny(DB::table('bill_details')->whereNull('productName'), [
            'price' => DB::raw('(SELECT products.price FROM products WHERE products.id = bill_details.product_id)'),
            'productName' => DB::raw('(SELECT products.name FROM products WHERE products.id = bill_details.product_id)'),
        ]);

        return ['bills' => $bills, 'bill_details' => $details, 'riyal_signs' => self::normalizeRiyal()];
    }

    /**
     * Stored bill currencies with an old Saudi riyal symbol get the Saudi riyal
     * sign, with the rules of Currency::normalize (the same currency, written the
     * new way). For example bills that an older version copied from a business
     * before its currency was normalized.
     */
    private static function normalizeRiyal(): int
    {
        $changed = self::updateIfAny(
            DB::table('bills')->whereIn(DB::raw('TRIM(currency)'), Currency::OLD_SAUDI_RIYAL_SYMBOLS),
            ['currency' => Currency::SAUDI_RIYAL_SIGN],
        );
        $changed += self::updateIfAny(
            DB::table('bills')
                ->whereIn(DB::raw('UPPER(TRIM(currency))'), Currency::OLD_RIYAL_SYMBOLS_IN_SAUDI_ARABIA)
                ->whereIn('business_id', DB::table('business')->where('country', 'Saudi Arabia')->select('id')),
            ['currency' => Currency::SAUDI_RIYAL_SIGN],
        );
        return $changed;
    }

    /**
     * The fill runs every hour and nearly always finds nothing. A SELECT ... LIMIT 1
     * reads without locks (InnoDB consistent read); the UPDATE, which locks the rows
     * it scans, runs only when a row matches.
     */
    private static function updateIfAny(Builder $query, array $values): int
    {
        if (!(clone $query)->exists())
            return 0;
        return $query->update($values);
    }
}
