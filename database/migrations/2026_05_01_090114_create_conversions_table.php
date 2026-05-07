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
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('click_id')->nullable()->constrained('clicks')->onDelete('set null');
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->foreignId('affiliate_link_id')->constrained('affiliate_links')->onDelete('cascade');
            
            // Conversion data
            $table->string('transaction_id')->unique()->nullable();
            $table->decimal('conversion_value', 12, 2); // Sale amount or lead value
            $table->decimal('commission_amount', 10, 2);
            $table->enum('commission_model', ['pps', 'ppl', 'revshare']);
            
            // Status tracking
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->text('rejection_reason')->nullable();
            
            // Tracking method
            $table->enum('tracking_method', ['cookie', 'postback', 'manual'])->default('cookie');
            $table->string('postback_data')->nullable();
            
            // Timestamps
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['affiliate_id', 'status', 'created_at']);
            $table->index(['offer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};
