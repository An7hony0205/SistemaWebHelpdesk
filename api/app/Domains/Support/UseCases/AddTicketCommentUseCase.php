<?php

namespace App\Domains\Support\UseCases;

use App\Domains\Support\Ticket;
use App\Domains\Support\TicketComment;
use Illuminate\Support\Facades\Event;
use App\Events\TicketCommentAdded;

class AddTicketCommentUseCase
{
    public function execute(Ticket $ticket, int $userId, int $tenantId, string $description, bool $isInternal = false): TicketComment
    {
        $comment = TicketComment::create([
            'tenant_id' => $tenantId,
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'description' => strip_tags($description, '<p><br><b><i><ul><li><img>'),
            'is_internal' => $isInternal,
        ]);

        Event::dispatch(new TicketCommentAdded($comment));

        return $comment;
    }
}
