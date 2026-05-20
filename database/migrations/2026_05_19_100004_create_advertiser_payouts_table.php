<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertiser_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'rejected'])->default('pending');
            $table->string('payment_method')->default('bank_transfer');
            $table->text('payment_details')->nullable(); // Encrypted JSON: bank name, account number, etc.
            $table->string('reference')->nullable()->unique();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertiser_payouts');
    }
};
