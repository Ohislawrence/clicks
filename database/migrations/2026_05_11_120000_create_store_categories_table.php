<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product categories per store
        Schema::create('store_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['store_id', 'slug']);
        });

        // Pivot: products <-> categories
        Schema::create('store_product_category', function (Blueprint $table) {
            $table->foreignId('store_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_category_id')->constrained()->onDelete('cascade');
            $table->primary(['store_product_id', 'store_category_id']);
        });

        // Add tags column to store_products
        Schema::table('store_products', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
        Schema::dropIfExists('store_product_category');
        Schema::dropIfExists('store_categories');
    }
};
