<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            // credit = money added to wallet, debit = money taken from wallet
            $table->enum('type', ['deposit', 'offer_allocation', 'offer_refund', 'offer_topup']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('reference')->unique()->nullable(); // Paystack reference for deposits
            $table->foreignId('offer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable(); // Paystack response data
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
