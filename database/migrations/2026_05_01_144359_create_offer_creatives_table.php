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
        Schema::create('offer_creatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['banner', 'image', 'text', 'video'])->default('image');
            $table->string('name');
            $table->string('file_path')->nullable(); // For banners, images, videos
            $table->text('content')->nullable(); // For text ads
            $table->integer('width')->nullable(); // For banners/images
            $table->integer('height')->nullable(); // For banners/images
            $table->string('size')->nullable(); // File size (e.g., "1.5 MB")
            $table->string('format')->nullable(); // File format (e.g., "JPG", "PNG", "MP4")
            $table->integer('clicks_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_creatives');
    }
};
