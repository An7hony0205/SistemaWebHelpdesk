<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogTicketCreation implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(TicketCreated $event): void
    {
        Log::info('Nuevo ticket creado en sistema', [
            'ticket_id' => $event->ticket->id,
            'title' => $event->ticket->title,
            'user_id' => $event->ticket->user_id,
        ]);
    }
}
