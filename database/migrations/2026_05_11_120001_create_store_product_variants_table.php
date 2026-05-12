<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. "Size: L / Color: Red"
            $table->json('options'); // {"Size": "L", "Color": "Red"}
            $table->decimal('price', 10, 2)->nullable(); // Override product price (null = use product price)
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->nullable(); // null = unlimited
            $table->string('sku')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_product_variants');
    }
};
