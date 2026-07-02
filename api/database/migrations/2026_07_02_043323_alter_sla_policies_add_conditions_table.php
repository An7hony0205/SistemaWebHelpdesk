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
        Schema::table('sla_policies', function (Blueprint $table) {
            $table->json('conditions')->nullable()->after('is_active')->comment('Reglas configurables para asignar SLA (ej: category_id, type)');
        });
    }

    public function down(): void
    {
        Schema::table('sla_policies', function (Blueprint $table) {
            $table->dropColumn('conditions');
        });
    }
};
