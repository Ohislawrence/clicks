<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            // Advertiser's own customer/subscriber ID — used to link recurring payments
            // back to the original affiliate attribution without needing a new click.
            $table->string('customer_id')->nullable()->after('transaction_id');
            $table->index('customer_id');

            // 1 = initial conversion, 2 = first renewal, 3 = second renewal, etc.
            $table->unsignedInteger('recurring_occurrence')->default(1)->after('customer_id');

            // Points to the first conversion (occurrence #1) for renewals
            $table->foreignId('parent_conversion_id')
                ->nullable()
                ->after('recurring_occurrence')
                ->constrained('conversions')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropForeign(['parent_conversion_id']);
            $table->dropIndex(['customer_id']);
            $table->dropColumn(['customer_id', 'recurring_occurrence', 'parent_conversion_id']);
        });
    }
};
