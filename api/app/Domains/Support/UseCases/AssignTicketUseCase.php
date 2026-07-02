<?php

namespace App\Domains\Support\UseCases;

use App\Domains\Support\Ticket;
use App\Events\TicketAssigned;
use Illuminate\Support\Facades\Event;

class AssignTicketUseCase
{
    public function execute(Ticket $ticket, int $assignedToUserId): Ticket
    {
        $ticket->update([
            'assigned_to' => $assignedToUserId,
        ]);

        Event::dispatch(new TicketAssigned($ticket));

        return $ticket;
    }
}
