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
        Schema::create('link_rotation_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('rotation_strategy', ['sequential', 'weighted', 'random', 'performance'])->default('random');
            $table->boolean('is_active')->default(true);
            
            // Split Testing
            $table->boolean('enable_split_test')->default(false);
            $table->integer('split_test_duration_days')->nullable();
            $table->timestamp('split_test_started_at')->nullable();
            $table->timestamp('split_test_ends_at')->nullable();
            
            // Performance Tracking
            $table->integer('total_clicks')->default(0);
            $table->integer('total_conversions')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('group_cr', 8, 2)->default(0);
            $table->decimal('group_epc', 12, 2)->default(0);
            
            // Auto-optimization
            $table->boolean('auto_optimize')->default(false);
            $table->integer('optimization_threshold_clicks')->default(100);
            $table->timestamp('last_optimized_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['affiliate_id', 'offer_id']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_rotation_groups');
    }
};
