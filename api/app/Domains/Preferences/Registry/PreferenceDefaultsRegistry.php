<?php

namespace App\Domains\Preferences\Registry;

class PreferenceDefaultsRegistry
{
    public static function getDefaults(string $category): array
    {
        return match ($category) {
            'appearance' => [
                'theme' => 'system',
                'primary_color' => '#f97316',
                'compact_mode' => false,
                'reduced_animations' => false,
                'font_size' => 'medium',
            ],
            'notifications' => [
                'email' => true,
                'in_app' => true,
                'frequency' => 'instant',
                'quiet_hours_enabled' => false,
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '07:00',
            ],
            'tickets' => [
                'default_view' => 'table',
                'show_closed' => false,
                'items_per_page' => 15,
                'auto_open_next' => false,
            ],
            'profile' => [
                'signature' => '',
                'phone' => '',
                'job_title' => '',
            ],
            default => [],
        };
    }

    public static function getAllDefaults(): array
    {
        return [
            'appearance' => self::getDefaults('appearance'),
            'notifications' => self::getDefaults('notifications'),
            'tickets' => self::getDefaults('tickets'),
            'profile' => self::getDefaults('profile'),
        ];
    }
}
