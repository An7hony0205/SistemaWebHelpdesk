<?php

namespace App\Domains\Integrations\Engine;

use Illuminate\Events\Dispatcher;
use App\Domains\Integrations\Models\WebhookEndpoint;
use App\Domains\Integrations\Jobs\DispatchWebhookJob;

class IntegrationEventSubscriber
{
    public function handleTicketCreated($event)
    {
        $this->dispatchWebhooks('ticket.created', $event->ticket->tenant_id, [
            'ticket_id' => $event->ticket->id,
            'title' => $event->ticket->title,
            'status_id' => $event->ticket->status_id,
            'priority_id' => $event->ticket->priority_id,
        ]);
    }

    public function handleTicketStatusChanged($event)
    {
        $this->dispatchWebhooks('ticket.status_changed', $event->ticket->tenant_id, [
            'ticket_id' => $event->ticket->id,
            'new_status_id' => $event->ticket->status_id,
        ]);
    }

    protected function dispatchWebhooks(string $eventName, int $tenantId, array $payload)
    {
        $endpoints = WebhookEndpoint::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->whereHas('events', function ($query) use ($eventName) {
                $query->where('event_name', $eventName);
            })
            ->get();

        foreach ($endpoints as $endpoint) {
            DispatchWebhookJob::dispatch($endpoint, $eventName, $payload);
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            \App\Domains\Support\Events\TicketCreated::class => 'handleTicketCreated',
            \App\Domains\Support\Events\TicketStatusChanged::class => 'handleTicketStatusChanged',
        ];
    }
}
