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
        Schema::create('blacklists', function (Blueprint $table) {
            $table->id();
            
            // Blacklist type
            $table->enum('type', [
                'ip',              // Single IP address
                'ip_range',        // IP range (CIDR notation)
                'user_agent',      // User agent pattern
                'referrer',        // Referrer domain
                'device_fingerprint', // Device fingerprint hash
                'country',         // Country code
                'asn',            // Autonomous System Number
            ]);
            
            // Value to match
            $table->string('value', 500);
            
            // Pattern matching type
            $table->enum('match_type', ['exact', 'contains', 'regex', 'wildcard'])->default('exact');
            
            // Scope (global or specific entities)
            $table->enum('scope', ['global', 'offer', 'affiliate'])->default('global');
            $table->foreignId('scope_id')->nullable()->constrained('users')->onDelete('cascade'); // For affiliate scope
            $table->foreignId('offer_id')->nullable()->constrained('offers')->onDelete('cascade'); // For offer scope
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Severity
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // Action to take
            $table->enum('action', ['block', 'flag', 'reduce_quality'])->default('block');
            $table->integer('quality_penalty')->default(0); // Points to deduct from quality score
            
            // Reason and notes
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            
            // Expiry (for temporary bans)
            $table->timestamp('expires_at')->nullable();
            
            // Tracking
            $table->integer('hit_count')->default(0); // How many times this rule was triggered
            $table->timestamp('last_hit_at')->nullable();
            
            // Audit
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['type', 'is_active']);
            $table->index(['value', 'type']);
            $table->index(['scope', 'scope_id', 'offer_id']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklists');
    }
};
