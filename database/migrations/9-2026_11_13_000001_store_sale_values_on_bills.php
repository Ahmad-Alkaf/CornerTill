<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A finished sale must not change when the business or a product changes.
 * Store the values of the sale on the bill (tax rate, currency) and on each
 * bill detail (unit price without tax, product name).
 *
 * The columns are nullable with no default: NULL means "not stored yet" (a row
 * that the old code wrote, for example by the old container during a rolling
 * deploy). `php artisan bills:fill-sale-values` fills such rows; the entrypoint
 * runs it after the migrations and the scheduler runs it every hour.
 *
 * Safe to run again after a partial failure (MySQL DDL is not transactional and
 * the entrypoint retries `migrate`): each column is added only when it is missing.
 *
 * The file name starts with "9-" so that it runs after the "8-" bill_details
 * migration on a new database (Laravel sorts migrations by file name).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bills', 'taxPercent'))
            Schema::table('bills', fn (Blueprint $table) => $table->decimal('taxPercent', 8, 4)->nullable()->after('cashReceived')); // 0.15 is 15%
        if (!Schema::hasColumn('bills', 'currency'))
            Schema::table('bills', fn (Blueprint $table) => $table->string('currency', 5)->nullable()->after('taxPercent'));
        if (!Schema::hasColumn('bill_details', 'price'))
            Schema::table('bill_details', fn (Blueprint $table) => $table->decimal('price', 12, 2)->nullable()->after('quantity')); // unit price, tax not included
        if (!Schema::hasColumn('bill_details', 'productName'))
            Schema::table('bill_details', fn (Blueprint $table) => $table->string('productName')->nullable()->after('price'));

        // Old bills: copy the current values (the best values that exist). Only
        // rows that are not filled yet, so a second run changes nothing.
        DB::table('bills')->whereNull('taxPercent')->update([
            'taxPercent' => DB::raw('(SELECT business.taxPercent FROM business WHERE business.id = bills.business_id)'),
        ]);
        DB::table('bills')->whereNull('currency')->update([
            'currency' => DB::raw('(SELECT business.currency FROM business WHERE business.id = bills.business_id)'),
        ]);
        // productName is NULL only on rows that were never filled (the product name is required).
        DB::table('bill_details')->whereNull('productName')->update([
            'price' => DB::raw('(SELECT products.price FROM products WHERE products.id = bill_details.product_id)'),
            'productName' => DB::raw('(SELECT products.name FROM products WHERE products.id = bill_details.product_id)'),
        ]);
    }

    public function down(): void
    {
        foreach (['price', 'productName'] as $column)
            if (Schema::hasColumn('bill_details', $column))
                Schema::table('bill_details', fn (Blueprint $table) => $table->dropColumn($column));
        foreach (['taxPercent', 'currency'] as $column)
            if (Schema::hasColumn('bills', $column))
                Schema::table('bills', fn (Blueprint $table) => $table->dropColumn($column));
    }
};
