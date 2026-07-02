<?php

namespace App\Domains\Intelligence\Engine;

use Illuminate\Events\Dispatcher;
use App\Domains\Intelligence\Jobs\AiClassifyTicketJob;

class IntelligenceEventSubscriber
{
    public function handleTicketCreated($event)
    {
        AiClassifyTicketJob::dispatch($event->ticket);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            \App\Domains\Support\Events\TicketCreated::class => 'handleTicketCreated',
        ];
    }
}
