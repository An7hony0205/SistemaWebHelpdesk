<?php

namespace App\Domains\Automations\Actions;

use App\Domains\Support\Models\Ticket;

interface ActionInterface
{
    /**
     * Executes the automation action on the given ticket
     */
    public function execute(Ticket $ticket, array $payload): void;
}
