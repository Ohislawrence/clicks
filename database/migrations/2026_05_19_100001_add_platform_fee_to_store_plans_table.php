<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_plans', function (Blueprint $table) {
            $table->decimal('platform_fee_percentage', 5, 2)->nullable()->default(null)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('store_plans', function (Blueprint $table) {
            $table->dropColumn('platform_fee_percentage');
        });
    }
};
