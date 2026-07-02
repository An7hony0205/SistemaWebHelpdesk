<?php

namespace App\Repositories;

use App\Domains\Support\Ticket;
use Illuminate\Database\Eloquent\Collection;
use App\Domains\Shared\Contracts\TicketProviderInterface;

class TicketRepository implements TicketRepositoryInterface, TicketProviderInterface
{
    public function getAll(): Collection
    {
        return Ticket::with(['category', 'user'])->get();
    }

    public function findById(int $id): ?Ticket
    {
        return Ticket::with(['category', 'user', 'details'])->find($id);
    }

    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $ticket = $this->findById($id);
        if (!$ticket) {
            return false;
        }
        return $ticket->update($data);
    }

    public function getTicketInfo(int $ticketId): ?array
    {
        $ticket = $this->findById($ticketId);
        if (!$ticket) return null;

        return [
            'id' => $ticket->id,
            'status_id' => $ticket->status_id,
            'priority_id' => $ticket->priority_id,
            'created_at' => $ticket->created_at,
            'assigned_to' => $ticket->assigned_to,
        ];
    }
}
