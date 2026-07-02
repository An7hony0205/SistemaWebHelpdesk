<?php

namespace App\Console\Commands;

use App\Domains\Preferences\Models\UserPreference;
use Illuminate\Console\Command;

class MigratePreferencesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preferences:migrate';

    protected $description = 'Migra de notification_preferences a user_preferences';

    public function handle()
    {
        $this->info('Iniciando migración de preferencias...');

        $oldPrefs = \DB::table('notification_preferences')->get();

        $count = 0;
        foreach ($oldPrefs as $old) {
            $settings = [
                'email' => (bool) $old->email,
                'in_app' => (bool) $old->in_app,
                'quiet_hours_enabled' => (bool) $old->quiet_hours_enabled,
                'quiet_hours_start' => $old->quiet_hours_start,
                'quiet_hours_end' => $old->quiet_hours_end,
            ];

            UserPreference::updateOrCreate(
                [
                    'user_id' => $old->user_id,
                    'category' => 'notifications',
                ],
                [
                    'settings' => $settings,
                ]
            );
            $count++;
        }

        $this->info("Migradas $count preferencias exitosamente.");
    }
}
