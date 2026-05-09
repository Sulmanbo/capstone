<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: threat_events
 *
 * Records active defense triggers:
 * - Brute force lockouts
 * - Injection attempt blocks (403)
 * - Privilege escalation violations
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threat_events', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();  // offending user if known

            $table->enum('threat_type', [
                'brute_force',
                'injection',
                'privilege_escalation',
                'session_anomaly',
                'other',
            ]);

            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['active', 'resolved'])->default('active');

            $table->string('event_label', 150)->nullable();   // human-readable title
            $table->text('description')->nullable();          // details of what was detected
            $table->string('source_ip', 45)->nullable();
            $table->string('target_route', 300)->nullable();  // the URL that was targeted

            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('threat_type');
            $table->index('severity');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threat_events');
    }
};
