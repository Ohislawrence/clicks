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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Subscription
            $table->foreignId('store_plan_id')->constrained()->onDelete('restrict');
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->date('subscription_start_date');
            $table->date('subscription_end_date');
            $table->enum('subscription_status', ['active', 'expired', 'cancelled'])->default('active');
            $table->boolean('expiry_reminder_sent')->default(false);

            // Theme
            $table->foreignId('store_theme_id')->constrained()->onDelete('restrict');
            $table->json('theme_customization')->nullable();

            // Payment Configuration
            $table->enum('payment_method', ['link', 'api']);
            $table->string('payment_provider')->nullable();
            $table->text('payment_link')->nullable();
            $table->text('payment_public_key')->nullable();
            $table->text('payment_secret_key')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_image')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
