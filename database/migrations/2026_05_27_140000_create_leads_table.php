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
        Schema::create('leads', function (Blueprint $notifiable) {
            $notifiable->id();
            $notifiable->string('name');
            $notifiable->string('email')->unique();
            $notifiable->string('phone')->nullable();
            $notifiable->string('company')->nullable();
            $notifiable->string('website')->nullable();
            $notifiable->string('status')->default('new'); // new, contacted, interested, converted, rejected
            $notifiable->string('source')->nullable();
            $notifiable->text('notes')->nullable();
            $notifiable->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $notifiable->timestamp('last_contacted_at')->nullable();
            $notifiable->timestamps();
        });

        Schema::create('lead_activities', function (Blueprint $notifiable) {
            $notifiable->id();
            $notifiable->foreignId('lead_id')->constrained()->onDelete('cascade');
            $notifiable->foreignId('user_id')->constrained()->onDelete('cascade');
            $notifiable->string('type'); // note, email, call, status_change
            $notifiable->text('description');
            $notifiable->json('metadata')->nullable();
            $notifiable->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
    }
};
