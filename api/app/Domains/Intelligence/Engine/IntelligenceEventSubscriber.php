<?php

namespace App\Domains\Intelligence\Engine;

use App\Domains\Intelligence\Jobs\AiClassifyTicketJob;
use App\Domains\Support\Events\TicketCreated;
use Illuminate\Events\Dispatcher;

class IntelligenceEventSubscriber
{
    public function handleTicketCreated($event)
    {
        AiClassifyTicketJob::dispatch($event->ticket);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            TicketCreated::class => 'handleTicketCreated',
        ];
    }
}
