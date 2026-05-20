<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_sale_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('store_orders')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('advertiser_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('affiliate_id')->nullable()->constrained('users')->nullOnDelete();

            // Amounts
            $table->decimal('gross_amount', 12, 2);          // Order total
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);             // gross - discount
            $table->decimal('platform_fee_amount', 12, 2);
            $table->decimal('affiliate_commission_amount', 12, 2)->default(0);
            $table->decimal('advertiser_net_amount', 12, 2);  // net - platform_fee - commission

            $table->string('platform_reference')->nullable(); // Paystack reference on platform account

            $table->timestamps();

            $table->index(['store_id']);
            $table->index(['advertiser_id']);
            $table->index(['affiliate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_sale_transactions');
    }
};
