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
        Schema::table('conversions', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false)->after('status');
            $table->text('manual_notes')->nullable()->after('is_manual');
            $table->string('conversion_id')->nullable()->after('id');
            $table->decimal('conversion_amount', 12, 2)->default(0)->after('conversion_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn(['is_manual', 'manual_notes', 'conversion_id', 'conversion_amount']);
        });
    }
};
