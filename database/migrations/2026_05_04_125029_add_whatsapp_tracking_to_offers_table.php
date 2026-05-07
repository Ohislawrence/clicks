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
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('enable_whatsapp_tracking')->default(false)->after('postback_url');
            $table->string('whatsapp_number', 20)->nullable()->after('enable_whatsapp_tracking');
            $table->text('whatsapp_message_template')->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['enable_whatsapp_tracking', 'whatsapp_number', 'whatsapp_message_template']);
        });
    }
};
