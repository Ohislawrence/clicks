<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // RevShare recurring settings
            // revshare_type: 'once' = single payment, 'recurring' = subscription/renewal
            $table->enum('revshare_type', ['once', 'recurring'])->default('once')->after('commission_rate');

            // How many billing cycles to pay commission for (NULL = unlimited/lifetime)
            $table->unsignedInteger('revshare_recurring_duration')->nullable()->after('revshare_type');

            // Billing cycle unit
            $table->enum('revshare_recurring_unit', ['month', 'year'])->nullable()->after('revshare_recurring_duration');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['revshare_type', 'revshare_recurring_duration', 'revshare_recurring_unit']);
        });
    }
};
