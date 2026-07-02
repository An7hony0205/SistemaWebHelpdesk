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
        Schema::create('sla_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sla_policy_id')->constrained('sla_policies')->cascadeOnDelete();
            $table->foreignId('priority_id')->constrained('priorities')->cascadeOnDelete();
            $table->integer('first_response_time_minutes');
            $table->integer('resolution_time_minutes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_targets');
    }
};
