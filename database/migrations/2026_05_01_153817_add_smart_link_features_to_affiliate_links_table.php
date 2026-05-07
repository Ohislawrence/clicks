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
        Schema::table('affiliate_links', function (Blueprint $table) {
            // Smart Link Rotation
            $table->boolean('enable_rotation')->default(false)->after('is_active');
            $table->enum('rotation_type', ['sequential', 'weighted', 'random'])->default('random')->after('enable_rotation');
            $table->integer('rotation_weight')->default(1)->after('rotation_type');
            $table->integer('rotation_priority')->default(0)->after('rotation_weight');
            $table->foreignId('rotation_group_id')->nullable()->after('rotation_priority')->constrained('link_rotation_groups')->onDelete('set null');
            
            // Geo-Targeting
            $table->boolean('enable_geo_targeting')->default(false)->after('rotation_group_id');
            $table->json('allowed_countries')->nullable()->after('enable_geo_targeting');
            $table->json('blocked_countries')->nullable()->after('allowed_countries');
            $table->json('allowed_regions')->nullable()->after('blocked_countries');
            $table->json('blocked_regions')->nullable()->after('allowed_regions');
            $table->json('allowed_cities')->nullable()->after('blocked_regions');
            $table->json('blocked_cities')->nullable()->after('allowed_cities');
            
            // Device Targeting
            $table->boolean('enable_device_targeting')->default(false)->after('blocked_cities');
            $table->json('allowed_devices')->nullable()->after('enable_device_targeting'); // mobile, desktop, tablet
            $table->json('allowed_os')->nullable()->after('allowed_devices'); // ios, android, windows, mac, linux
            $table->json('allowed_browsers')->nullable()->after('allowed_os'); // chrome, firefox, safari, edge
            
            // Time-based Targeting
            $table->boolean('enable_schedule')->default(false)->after('allowed_browsers');
            $table->time('active_start_time')->nullable()->after('enable_schedule');
            $table->time('active_end_time')->nullable()->after('active_start_time');
            $table->json('active_days')->nullable()->after('active_end_time'); // [0-6] for Sun-Sat
            
            // Performance Tracking
            $table->integer('rotation_clicks')->default(0)->after('active_days');
            $table->integer('rotation_conversions')->default(0)->after('rotation_clicks');
            $table->decimal('rotation_cr', 8, 2)->default(0)->after('rotation_conversions');
            $table->timestamp('last_rotated_at')->nullable()->after('rotation_cr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_links', function (Blueprint $table) {
            $table->dropForeign(['rotation_group_id']);
            $table->dropColumn([
                'enable_rotation',
                'rotation_type',
                'rotation_weight',
                'rotation_priority',
                'rotation_group_id',
                'enable_geo_targeting',
                'allowed_countries',
                'blocked_countries',
                'allowed_regions',
                'blocked_regions',
                'allowed_cities',
                'blocked_cities',
                'enable_device_targeting',
                'allowed_devices',
                'allowed_os',
                'allowed_browsers',
                'enable_schedule',
                'active_start_time',
                'active_end_time',
                'active_days',
                'rotation_clicks',
                'rotation_conversions',
                'rotation_cr',
                'last_rotated_at',
            ]);
        });
    }
};
