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
        Schema::table('clicks', function (Blueprint $table) {
            // Quality Score (0-100)
            $table->integer('quality_score')->default(50)->after('is_valid');
            $table->json('fraud_indicators')->nullable()->after('quality_score');
            
            // Advanced Fraud Detection Flags
            $table->boolean('is_vpn')->default(false)->after('fraud_indicators');
            $table->boolean('is_proxy')->default(false)->after('is_vpn');
            $table->boolean('is_datacenter')->default(false)->after('is_proxy');
            $table->boolean('is_bot_detected')->default(false)->after('is_datacenter');
            $table->boolean('is_suspicious_pattern')->default(false)->after('is_bot_detected');
            
            // Device Fingerprint
            $table->string('device_fingerprint', 64)->nullable()->after('is_suspicious_pattern');
            $table->json('device_details')->nullable()->after('device_fingerprint');
            
            // Behavioral Analysis
            $table->integer('click_velocity_score')->default(0)->after('device_details'); // Clicks per minute
            $table->integer('conversion_time_score')->default(0)->after('click_velocity_score'); // Seconds to conversion
            $table->integer('engagement_score')->default(0)->after('conversion_time_score'); // Mouse movements, scroll depth
            
            // Risk Level
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low')->after('engagement_score');
            $table->text('risk_reasons')->nullable()->after('risk_level');
            
            // Review Status
            $table->boolean('needs_manual_review')->default(false)->after('risk_reasons');
            $table->timestamp('reviewed_at')->nullable()->after('needs_manual_review');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'quality_score',
                'fraud_indicators',
                'is_vpn',
                'is_proxy',
                'is_datacenter',
                'is_bot_detected',
                'is_suspicious_pattern',
                'device_fingerprint',
                'device_details',
                'click_velocity_score',
                'conversion_time_score',
                'engagement_score',
                'risk_level',
                'risk_reasons',
                'needs_manual_review',
                'reviewed_at',
                'reviewed_by',
            ]);
        });
    }
};
