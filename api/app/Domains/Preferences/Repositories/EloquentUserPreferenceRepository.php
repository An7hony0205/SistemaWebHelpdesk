<?php

namespace App\Domains\Preferences\Repositories;

use App\Domains\Preferences\Models\UserPreference;
use App\Domains\Preferences\Registry\PreferenceDefaultsRegistry;

class EloquentUserPreferenceRepository implements UserPreferenceRepositoryInterface
{
    public function getByCategory(int $userId, string $category): array
    {
        $preference = UserPreference::where('user_id', $userId)
            ->where('category', $category)
            ->first();

        $defaults = PreferenceDefaultsRegistry::getDefaults($category);
        $userSettings = $preference ? $preference->settings : [];

        // Hacemos merge priorizando los ajustes del usuario
        return array_merge($defaults, $userSettings);
    }

    public function getAll(int $userId): array
    {
        $preferences = UserPreference::where('user_id', $userId)->get()->keyBy('category');
        
        $allDefaults = PreferenceDefaultsRegistry::getAllDefaults();
        $result = [];

        foreach ($allDefaults as $category => $defaults) {
            $userSettings = isset($preferences[$category]) ? $preferences[$category]->settings : [];
            $result[$category] = array_merge($defaults, $userSettings);
        }

        return $result;
    }

    public function updateCategory(int $userId, string $category, array $settings): UserPreference
    {
        $preference = UserPreference::firstOrNew([
            'user_id' => $userId,
            'category' => $category,
        ]);

        $currentSettings = $preference->settings ?? [];
        
        // JSON merge
        $preference->settings = array_merge($currentSettings, $settings);
        $preference->save();

        return $preference;
    }

    public function resetCategory(int $userId, string $category): void
    {
        UserPreference::where('user_id', $userId)
            ->where('category', $category)
            ->delete();
    }
}
