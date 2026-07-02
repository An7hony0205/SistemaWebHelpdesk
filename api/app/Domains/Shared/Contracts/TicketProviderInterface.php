<?php

namespace App\Domains\Shared\Contracts;

interface TicketProviderInterface
{
    /**
     * Devuelve información genérica de un ticket por su ID.
     */
    public function getTicketInfo(int $ticketId): ?array;
}
