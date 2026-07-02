<?php

namespace App\Domains\Preferences\Repositories;

use App\Domains\Preferences\Models\UserPreference;

interface UserPreferenceRepositoryInterface
{
    public function getByCategory(int $userId, string $category): array;
    public function getAll(int $userId): array;
    public function updateCategory(int $userId, string $category, array $settings): UserPreference;
    public function resetCategory(int $userId, string $category): void;
}
