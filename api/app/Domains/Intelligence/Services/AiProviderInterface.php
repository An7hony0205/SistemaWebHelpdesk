<?php

namespace App\Domains\Intelligence\Services;

interface AiProviderInterface
{
    /**
     * Set the API key dynamically based on Tenant configuration
     */
    public function setApiKey(string $apiKey): void;

    /**
     * Use LLM to classify a ticket into a category and priority
     *
     * @return array [ 'category_id' => int, 'priority_id' => int, 'reason' => string ]
     */
    public function classifyTicket(string $title, string $content, array $availableCategories, array $availablePriorities): array;

    /**
     * Extract keywords from text for search deflection
     *
     * @return array of strings
     */
    public function extractKeywords(string $text): array;
}
