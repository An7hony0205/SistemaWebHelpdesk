<?php

namespace App\Domains\Automations\Actions;

use App\Domains\Support\Models\Ticket;
use Illuminate\Support\Facades\Log;

class AssignTicketAction implements ActionInterface
{
    public function execute(Ticket $ticket, array $payload): void
    {
        if (isset($payload['user_id'])) {
            $ticket->assigned_to = $payload['user_id'];
            $ticket->save();
            
            // Log for automation trace
            Log::info("Automation assigned ticket {$ticket->id} to user {$payload['user_id']}");
            
            // This natively triggers TicketAssigned event from Support Bounded Context if we have Model Observers
        }
    }
}
