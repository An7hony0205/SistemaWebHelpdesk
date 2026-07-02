<?php

namespace App\Domains\Sla\Listeners;

use App\Domains\Sla\TicketSla;
use App\Events\TicketCommentAdded;

class TicketCommentAddedListener
{
    public function handle(TicketCommentAdded $event): void
    {
        $comment = $event->comment;

        // Si el comentario es público y hecho por un Agente/Soporte/Admin
        if (! $comment->is_internal && $comment->user->hasRole(['Soporte', 'Admin'])) {
            $ticketSla = TicketSla::where('ticket_id', $comment->ticket_id)
                ->whereNull('first_response_completed_at')
                ->first();

            if ($ticketSla) {
                $ticketSla->update([
                    'first_response_completed_at' => now(),
                    'first_response_breached' => now()->greaterThan($ticketSla->first_response_due_at),
                ]);
            }
        }
    }
}
