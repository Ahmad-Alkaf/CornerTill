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
 * The file name starts with "9-" so that it runs after the "8-" bill_details
 * migration on a new database (Laravel sorts migrations by file name).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('taxPercent', 8, 4)->default(0)->after('cashReceived'); // 0.15 is 15%
            $table->string('currency', 5)->nullable()->after('taxPercent');
        });
        Schema::table('bill_details', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('quantity'); // unit price, tax not included
            $table->string('productName')->nullable()->after('price');
        });

        // Old bills: copy the current values. They are the best values that exist.
        DB::table('bills')->update([
            'taxPercent' => DB::raw('COALESCE((SELECT business.taxPercent FROM business WHERE business.id = bills.business_id), 0)'),
            'currency' => DB::raw('(SELECT business.currency FROM business WHERE business.id = bills.business_id)'),
        ]);
        DB::table('bill_details')->update([
            'price' => DB::raw('(SELECT products.price FROM products WHERE products.id = bill_details.product_id)'),
            'productName' => DB::raw('(SELECT products.name FROM products WHERE products.id = bill_details.product_id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('bill_details', function (Blueprint $table) {
            $table->dropColumn(['price', 'productName']);
        });
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['taxPercent', 'currency']);
        });
    }
};
