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
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->uuid('tracking_code')->unique();
            $table->string('custom_slug')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('click_count')->default(0);
            $table->integer('conversion_count')->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['affiliate_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};
