<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('offer_channel')->default('platform')->after('advertiser_id');
            $table->string('network_name')->nullable()->after('offer_channel');
            $table->string('network_offer_id')->nullable()->after('network_name');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['offer_channel', 'network_name', 'network_offer_id']);
        });
    }
};
