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
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->foreignId('status_id')->nullable()->constrained('statuses');
            $table->foreignId('priority_id')->nullable()->constrained('priorities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->default('Abierto');
            $table->dropForeign(['status_id']);
            $table->dropForeign(['priority_id']);
            $table->dropColumn(['status_id', 'priority_id']);
        });
    }
};
