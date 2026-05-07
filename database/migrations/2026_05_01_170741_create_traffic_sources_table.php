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
        Schema::create('traffic_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['instagram', 'tiktok', 'youtube', 'twitter', 'facebook', 'website', 'blog', 'other'])->default('website');
            $table->string('name'); // Platform name or website title
            $table->string('url'); // Profile URL or website URL
            $table->integer('followers')->nullable(); // Follower/subscriber count
            $table->integer('monthly_visitors')->nullable(); // For websites/blogs
            $table->text('description')->nullable(); // Additional info
            $table->boolean('is_verified')->default(false); // If platform account is verified
            $table->boolean('is_primary')->default(false); // Main traffic source
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_sources');
    }
};
