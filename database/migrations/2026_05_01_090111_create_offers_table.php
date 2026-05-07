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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertiser_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('offer_categories')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('terms_and_conditions')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('thumbnail')->nullable();
            
            // Commission settings
            $table->enum('commission_model', ['pps', 'ppl', 'revshare'])->default('pps');
            $table->decimal('commission_rate', 10, 2); // For PPS/PPL: fixed amount, for RevShare: percentage
            $table->integer('cookie_duration')->default(30); // days
            
            // Access control
            $table->enum('access_type', ['open', 'request'])->default('open');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // Tracking
            $table->string('postback_url')->nullable();
            $table->text('conversion_pixel')->nullable();
            
            // Stats (cached for performance)
            $table->integer('total_clicks')->default(0);
            $table->integer('total_conversions')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
