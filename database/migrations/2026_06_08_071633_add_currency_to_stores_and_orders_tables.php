<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // Currency the store charges customers in (Paystack-supported African currencies)
            $table->string('currency', 3)->default('NGN')->after('payment_webhook_secret');
        });

        Schema::table('store_orders', function (Blueprint $table) {
            // Snapshot of the store's currency at the time the order was placed
            $table->string('currency', 3)->default('NGN')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('store_orders', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
