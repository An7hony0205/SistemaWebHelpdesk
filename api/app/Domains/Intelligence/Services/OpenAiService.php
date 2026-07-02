<?php

namespace App\Domains\Intelligence\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService implements AiProviderInterface
{
    protected ?string $apiKey = null;

    protected string $model = 'gpt-4o-mini';

    protected string $baseUrl = 'https://api.openai.com/v1';

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function classifyTicket(string $title, string $content, array $availableCategories, array $availablePriorities): array
    {
        if (! $this->apiKey) {
            throw new \Exception('OpenAI API key not configured for this tenant.');
        }

        $systemPrompt = 'You are an expert IT Helpdesk agent. Your task is to classify a new ticket based on its title and content. 
        You MUST choose exactly ONE category ID and ONE priority ID from the provided lists.
        Available Categories: '.json_encode($availableCategories).'
        Available Priorities: '.json_encode($availablePriorities)."
        Return your response ONLY as a valid JSON object with keys: 'category_id' (integer), 'priority_id' (integer), and 'reason' (string, max 20 words explaining why).";

        $userPrompt = "Title: {$title}\n\nContent: {$content}";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.1, // Low temperature for consistent classification
                ]);

            if ($response->successful()) {
                $result = $response->json();

                // (In a complete implementation we would log tokens here for billing)
                // $promptTokens = $result['usage']['prompt_tokens'];

                $content = json_decode($result['choices'][0]['message']['content'], true);

                return [
                    'category_id' => $content['category_id'] ?? null,
                    'priority_id' => $content['priority_id'] ?? null,
                    'reason' => $content['reason'] ?? 'Clasificado automáticamente por IA',
                ];
            }

            Log::error('OpenAI API Error: '.$response->body());

            return [];
        } catch (\Exception $e) {
            Log::error('OpenAI Exception: '.$e->getMessage());

            return [];
        }
    }

    public function extractKeywords(string $text): array
    {
        if (! $this->apiKey) {
            return [];
        }

        $systemPrompt = 'Extract the 3 to 5 most important search keywords from the following IT support text. Return ONLY a JSON array of strings.';

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $text],
                    ],
                    'response_format' => ['type' => 'json_object'], // Note: GPT-4o-mini supports JSON mode
                ]);

            if ($response->successful()) {
                $contentStr = $response->json()['choices'][0]['message']['content'];
                // Since we asked for a JSON array, sometimes it returns {"keywords": ["x", "y"]}
                // We'll parse aggressively
                $content = json_decode($contentStr, true);
                if (isset($content['keywords']) && is_array($content['keywords'])) {
                    return $content['keywords'];
                } elseif (is_array($content)) {
                    return array_values($content)[0] ?? []; // Fallback
                }
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
