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
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedInteger('expected_sales')->nullable()->after('spent_budget');
            $table->decimal('product_cost', 12, 2)->nullable()->after('expected_sales');
            $table->decimal('minimum_wallet_required', 12, 2)->nullable()->default(0)->after('product_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['expected_sales', 'product_cost', 'minimum_wallet_required']);
        });
    }
};
