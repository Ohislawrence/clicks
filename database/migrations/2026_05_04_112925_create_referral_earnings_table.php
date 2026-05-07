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
        Schema::create('referral_earnings', function (Blueprint $table) {
            $table->id();

            // Parent affiliate who referred the sub-affiliate
            $table->foreignId('parent_affiliate_id')->constrained('users')->onDelete('cascade');

            // Sub-affiliate who generated the conversion
            $table->foreignId('sub_affiliate_id')->constrained('users')->onDelete('cascade');

            // The commission that triggered this referral earning
            $table->foreignId('commission_id')->nullable()->constrained()->onDelete('set null');

            // Referral commission amount earned
            $table->decimal('amount', 12, 2);

            // Whether this earning was blocked due to cap
            $table->boolean('is_capped')->default(false);

            // Cap type at time of earning (for tracking/reporting)
            $table->string('cap_reason')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('parent_affiliate_id');
            $table->index('sub_affiliate_id');
            $table->index(['parent_affiliate_id', 'created_at']);
            $table->index('is_capped');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_earnings');
    }
};
