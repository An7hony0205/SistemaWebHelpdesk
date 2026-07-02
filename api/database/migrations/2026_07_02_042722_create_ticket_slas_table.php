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
        Schema::create('ticket_slas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('ticket_id'); // Referencia lógica al bounded context Support
            $table->foreignId('sla_policy_id')->constrained('sla_policies');
            
            $table->timestamp('first_response_due_at')->nullable();
            $table->timestamp('first_response_completed_at')->nullable();
            $table->boolean('first_response_breached')->default(false);
            
            $table->timestamp('resolution_due_at')->nullable();
            $table->timestamp('resolution_completed_at')->nullable();
            $table->boolean('resolution_breached')->default(false);
            
            $table->timestamp('paused_at')->nullable();
            $table->integer('accumulated_pause_minutes')->default(0);
            
            $table->timestamps();
            
            $table->index('ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_slas');
    }
};
