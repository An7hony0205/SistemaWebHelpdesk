<?php

namespace App\Domains\Integrations\Engine;

use App\Domains\Integrations\Jobs\DispatchWebhookJob;
use App\Domains\Integrations\Models\WebhookEndpoint;
use App\Domains\Support\Events\TicketCreated;
use App\Domains\Support\Events\TicketStatusChanged;
use Illuminate\Events\Dispatcher;

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
            TicketCreated::class => 'handleTicketCreated',
            TicketStatusChanged::class => 'handleTicketStatusChanged',
        ];
    }
}
