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
        // Approve all existing offers so they remain active
        DB::table('offers')
            ->update([
                'approval_status' => 'approved',
                'reviewed_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to pending status
        DB::table('offers')
            ->update([
                'approval_status' => 'pending',
                'reviewed_at' => null,
                'reviewed_by' => null,
                'updated_at' => now(),
            ]);
    }
};
