<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('code')->index(); // Unique per store
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']); // discount type
            $table->decimal('value', 10, 2); // % amount or fixed NGN amount
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Minimum cart value
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Cap for percentage discounts
            $table->unsignedInteger('max_uses')->nullable(); // null = unlimited
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_discount_codes');
    }
};
