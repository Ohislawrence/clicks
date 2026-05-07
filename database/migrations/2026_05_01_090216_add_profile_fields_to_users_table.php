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
        Schema::table('users', function (Blueprint $table) {
            // Common fields
            $table->string('phone')->nullable()->after('email');
            $table->string('company_name')->nullable()->after('name');
            $table->text('bio')->nullable();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            
            // Affiliate specific
            $table->enum('payout_frequency', ['bi-weekly', 'monthly', 'on-demand'])->default('monthly');
            $table->decimal('balance', 12, 2)->default(0); // Available balance
            $table->decimal('pending_balance', 12, 2)->default(0); // Pending approval
            $table->decimal('lifetime_earnings', 12, 2)->default(0);
            
            // Payment details (encrypted)
            $table->json('payment_details')->nullable();
            
            // Social media (for affiliates/influencers)
            $table->string('instagram_handle')->nullable();
            $table->string('tiktok_handle')->nullable();
            $table->string('youtube_channel')->nullable();
            $table->string('twitter_handle')->nullable();
            $table->integer('follower_count')->nullable();
            
            // Advertiser specific
            $table->decimal('advertiser_balance', 12, 2)->default(0); // Wallet balance for ads
            
            // Status and verification
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'company_name', 'bio', 'website', 'country',
                'payout_frequency', 'balance', 'pending_balance', 'lifetime_earnings',
                'payment_details', 'instagram_handle', 'tiktok_handle', 
                'youtube_channel', 'twitter_handle', 'follower_count',
                'advertiser_balance', 'is_verified', 'is_active', 'verified_at'
            ]);
        });
    }
};
