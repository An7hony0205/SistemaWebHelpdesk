<?php

namespace App\Domains\Automations\Actions;

use App\Domains\Support\Models\Ticket;
use Illuminate\Support\Facades\Log;

class UpdateTicketStatusAction implements ActionInterface
{
    public function execute(Ticket $ticket, array $payload): void
    {
        if (isset($payload['status_id'])) {
            $ticket->status_id = $payload['status_id'];
            $ticket->save();
            
            Log::info("Automation changed ticket {$ticket->id} status to {$payload['status_id']}");
        }
    }
}
