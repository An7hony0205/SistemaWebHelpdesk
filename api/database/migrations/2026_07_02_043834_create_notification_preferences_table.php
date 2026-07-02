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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->morphs('configurable'); // configurable_type, configurable_id
            $table->string('event_name');
            $table->json('channels'); // ["email", "slack"]
            $table->timestamps();
            
            $table->unique(['configurable_type', 'configurable_id', 'event_name'], 'notif_pref_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
