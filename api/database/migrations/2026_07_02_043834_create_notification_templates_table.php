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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('event_name'); // ej: App\Events\TicketCreated
            $table->string('channel'); // ej: email, slack
            $table->string('locale')->default('es');
            $table->string('subject_template')->nullable();
            $table->text('body_template');
            $table->timestamps();

            $table->unique(['tenant_id', 'event_name', 'channel', 'locale'], 'notif_tpl_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
