<?php

namespace App\Domains\Automations\Engine;

use Illuminate\Events\Dispatcher;
use App\Domains\Automations\Engine\AutomationEngine;

class AutomationEventSubscriber
{
    protected AutomationEngine $engine;

    public function __construct(AutomationEngine $engine)
    {
        $this->engine = $engine;
    }

    public function handleTicketCreated($event)
    {
        // En MVP, ejecutamos síncronamente o lo podemos mandar a un Job.
        // Si lo mandamos a Job, el motor debe recibir el ID del ticket.
        // Para simplificar y probar, lo procesamos directamente.
        $this->engine->processEvent('ticket.created', $event->ticket);
    }

    public function handleTicketStatusChanged($event)
    {
        $this->engine->processEvent('ticket.status_changed', $event->ticket);
    }

    public function subscribe(Dispatcher $events): array
    {
        // Asumiendo que existen estas clases en App\Domains\Support\Events
        return [
            \App\Domains\Support\Events\TicketCreated::class => 'handleTicketCreated',
            \App\Domains\Support\Events\TicketStatusChanged::class => 'handleTicketStatusChanged',
        ];
    }
}
