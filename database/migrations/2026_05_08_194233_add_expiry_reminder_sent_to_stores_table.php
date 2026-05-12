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
        if (! Schema::hasColumn('stores', 'expiry_reminder_sent')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->boolean('expiry_reminder_sent')->default(false)->after('subscription_end_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('expiry_reminder_sent');
        });
    }
};
