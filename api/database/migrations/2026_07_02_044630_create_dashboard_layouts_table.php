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
        Schema::create('dashboard_layouts', function (Blueprint $table) {
            $table->id();
            $table->morphs('configurable'); // configurable_type, configurable_id (Tenant, User)
            $table->json('widgets_layout'); // Array of widgets with their position and config
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->unique(['configurable_type', 'configurable_id'], 'dashboard_layout_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_layouts');
    }
};
