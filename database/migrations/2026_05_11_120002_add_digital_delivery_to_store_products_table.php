<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add digital-delivery columns to store_products
        Schema::table('store_products', function (Blueprint $table) {
            $table->string('download_file')->nullable()->after('tags'); // stored file path
            $table->string('download_url')->nullable()->after('download_file'); // or external URL
            $table->unsignedInteger('download_expiry_hours')->nullable()->after('download_url'); // hours link is valid, null = no expiry
            $table->unsignedInteger('max_downloads')->nullable()->after('download_expiry_hours'); // null = unlimited
        });
    }

    public function down(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            $table->dropColumn(['download_file', 'download_url', 'download_expiry_hours', 'max_downloads']);
        });
    }
};
