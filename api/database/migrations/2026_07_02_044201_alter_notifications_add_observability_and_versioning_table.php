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
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->integer('version')->default(1)->after('body_template');
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->integer('retry_count')->default(0)->after('sent_at');
            $table->float('processing_time_ms')->nullable()->after('retry_count');
        });
    }

    public function down(): void
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropColumn('version');
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropColumn(['retry_count', 'processing_time_ms']);
        });
    }
};
