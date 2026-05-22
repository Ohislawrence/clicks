<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // Actual destination URL (affiliate traffic lands here); preview_url remains for browsing only
            $table->string('offer_url')->nullable()->after('preview_url');

            // Product image URL (can be from CPAlead or uploaded)
            $table->string('product_image')->nullable()->after('thumbnail');

            // External reference for CPAlead or other networks
            $table->string('cpalead_offer_id')->nullable()->after('product_image');

            // Geo targeting: JSON array of ISO 3166-1 alpha-2 country codes, null = worldwide
            $table->json('target_countries')->nullable()->after('cpalead_offer_id');

            // Device targeting: JSON array of ['desktop', 'mobile', 'tablet'], null = all
            $table->json('target_devices')->nullable()->after('target_countries');

            // OS targeting: JSON array of ['windows', 'mac', 'linux', 'android', 'ios'], null = all
            $table->json('target_os')->nullable()->after('target_devices');

            // Enforce one conversion per unique IP
            $table->boolean('require_unique_ip')->default(false)->after('target_os');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'offer_url',
                'product_image',
                'cpalead_offer_id',
                'target_countries',
                'target_devices',
                'target_os',
                'require_unique_ip',
            ]);
        });
    }
};
