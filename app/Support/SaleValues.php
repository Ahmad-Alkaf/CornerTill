<?php

namespace App\Support;

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
    /** @return array{bills:int, bill_details:int} the number of rows filled */
    public static function fillMissing(): array
    {
        $bills = DB::table('bills')->whereNull('taxPercent')->update([
            'taxPercent' => DB::raw('(SELECT business.taxPercent FROM business WHERE business.id = bills.business_id)'),
        ]);
        $bills += DB::table('bills')->whereNull('currency')->update([
            'currency' => DB::raw('(SELECT business.currency FROM business WHERE business.id = bills.business_id)'),
        ]);
        $details = DB::table('bill_details')->whereNull('productName')->update([
            'price' => DB::raw('(SELECT products.price FROM products WHERE products.id = bill_details.product_id)'),
            'productName' => DB::raw('(SELECT products.name FROM products WHERE products.id = bill_details.product_id)'),
        ]);

        return ['bills' => $bills, 'bill_details' => $details];
    }
}
