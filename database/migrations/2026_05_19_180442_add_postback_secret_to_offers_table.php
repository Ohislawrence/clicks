<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->string('postback_secret', 64)->nullable()->after('postback_url');
        });

        // Generate secrets for all existing offers
        DB::table('offers')->whereNull('postback_secret')->orderBy('id')->each(function ($offer) {
            DB::table('offers')->where('id', $offer->id)->update([
                'postback_secret' => Str::random(48),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('postback_secret');
        });
    }
};
