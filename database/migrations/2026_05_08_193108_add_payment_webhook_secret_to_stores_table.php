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
            // Add webhook secret field for webhook verification
            // For Paystack: same as payment_secret_key (can be left null, will use payment_secret_key)
            // For Flutterwave: separate secret hash configured in their dashboard
            $table->text('payment_webhook_secret')->nullable()->after('payment_secret_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('payment_webhook_secret');
        });
    }
};
