<?php

namespace App\Domains\Integrations\Jobs;

use App\Domains\Integrations\Models\WebhookDeliveryLog;
use App\Domains\Integrations\Models\WebhookEndpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $endpoint;

    public $eventName;

    public $payload;

    public function __construct(WebhookEndpoint $endpoint, string $eventName, array $payload)
    {
        $this->endpoint = $endpoint;
        $this->eventName = $eventName;
        $this->payload = $payload;
    }

    public function handle()
    {
        if (! $this->endpoint->is_active) {
            return;
        }

        $startTime = microtime(true);
        $payloadJson = json_encode($this->payload);

        $headers = [
            'Content-Type' => 'application/json',
            'X-Helpdesk-Event' => $this->eventName,
        ];

        // MVP: HMAC signature
        if ($this->endpoint->secret) {
            $signature = hash_hmac('sha256', $payloadJson, $this->endpoint->secret);
            $headers['X-Helpdesk-Signature'] = $signature;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10) // 10 seconds timeout
                ->post($this->endpoint->url, $this->payload);

            $executionTime = round((microtime(true) - $startTime) * 1000);

            WebhookDeliveryLog::create([
                'endpoint_id' => $this->endpoint->id,
                'event_name' => $this->eventName,
                'payload' => $this->payload,
                'response_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 5000), // Limit length
                'execution_time_ms' => $executionTime,
            ]);

            // En un futuro: Si $response->failed(), podríamos lanzar excepción para que el Job se reintente

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000);

            WebhookDeliveryLog::create([
                'endpoint_id' => $this->endpoint->id,
                'event_name' => $this->eventName,
                'payload' => $this->payload,
                'response_status' => 0,
                'response_body' => 'Exception: '.substr($e->getMessage(), 0, 1000),
                'execution_time_ms' => $executionTime,
            ]);
        }
    }
}
