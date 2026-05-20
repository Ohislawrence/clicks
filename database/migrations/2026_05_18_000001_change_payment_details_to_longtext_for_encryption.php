<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NDPR Compliance: convert payment_details columns from json to longtext
 * so Laravel's encrypted:array cast can store encrypted (non-JSON) values.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('payment_details')->nullable()->change();
        });

        Schema::table('payout_requests', function (Blueprint $table) {
            $table->longText('payment_details')->nullable()->change();
        });
    }

    public function down(): void
    {
        // NOTE: rolling back will drop any encrypted data currently in these columns.
        Schema::table('users', function (Blueprint $table) {
            $table->json('payment_details')->nullable()->change();
        });

        Schema::table('payout_requests', function (Blueprint $table) {
            $table->json('payment_details')->nullable()->change();
        });
    }
};
