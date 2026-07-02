<?php

namespace App\Domains\Preferences\DTOs;

class PreferenceUpdateDTO
{
    public function __construct(
        public int $userId,
        public string $category,
        public array $settings
    ) {}

    public static function fromRequest(array $data, int $userId, string $category): self
    {
        return new self(
            $userId,
            $category,
            $data['settings'] ?? []
        );
    }
}
