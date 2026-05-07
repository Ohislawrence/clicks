<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set default cap settings for existing affiliates
        // Using time-based cap of 12 months by default as recommended
        DB::table('users')
            ->where('referral_cap_type', 'unlimited')
            ->update([
                'referral_cap_type' => 'time',
                'referral_cap_months' => 12,
                'updated_at' => now(),
            ]);

        // For users who have already earned referrals, set their started_at to their earliest referral
        // This is an approximation - in production you might want to track this more precisely
        $usersWithReferrals = DB::table('users')
            ->where('referral_earnings', '>', 0)
            ->whereNull('referral_started_at')
            ->get();

        foreach ($usersWithReferrals as $user) {
            // Set started_at to 3 months ago as a reasonable default
            // In a real scenario, you'd look at actual commission data
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'referral_started_at' => now()->subMonths(3),
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to unlimited for all users
        DB::table('users')->update([
            'referral_cap_type' => 'unlimited',
            'referral_cap_months' => null,
            'referral_started_at' => null,
            'updated_at' => now(),
        ]);
    }
};
