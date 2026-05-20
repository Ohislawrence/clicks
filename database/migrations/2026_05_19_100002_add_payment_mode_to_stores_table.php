<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // 'direct' = advertiser uses their own payment keys
            // 'platform' = platform collects via its own Paystack key and splits
            $table->enum('payment_mode', ['direct', 'platform'])->default('direct')->after('payment_method');
            // Locked to true once any platform-mode order is placed (prevents mode switching)
            $table->boolean('has_orders')->default(false)->after('payment_mode');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'has_orders']);
        });
    }
};
