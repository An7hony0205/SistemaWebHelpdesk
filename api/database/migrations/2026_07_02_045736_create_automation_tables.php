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
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('trigger_event'); // e.g. ticket.created, ticket.updated
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Ejecutar en orden
            $table->timestamps();
        });

        Schema::create('automation_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('automation_rules')->cascadeOnDelete();
            $table->string('field'); // e.g. status, priority_id, category_id
            $table->string('operator'); // e.g. equals, not_equals, contains, greater_than
            $table->string('value'); // El valor a comparar
            $table->timestamps();
        });

        Schema::create('automation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('automation_rules')->cascadeOnDelete();
            $table->string('action_type'); // e.g. assign_to_user, set_status, add_tag
            $table->json('action_payload'); // Parámetros específicos de la acción
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_actions');
        Schema::dropIfExists('automation_conditions');
        Schema::dropIfExists('automation_rules');
    }
};
