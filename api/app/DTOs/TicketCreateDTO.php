<?php

namespace App\DTOs;

class TicketCreateDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly int $categoryId,
        public readonly string $title,
        public readonly string $description,
        public readonly ?int $priorityId = null
    ) {
    }

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            userId: $userId,
            categoryId: $data['category_id'],
            title: $data['title'],
            description: $data['description'],
            priorityId: $data['priority_id'] ?? null
        );
    }
}
