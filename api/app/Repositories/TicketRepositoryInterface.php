<?php

namespace App\Repositories;

use App\Domains\Support\Ticket;
use Illuminate\Database\Eloquent\Collection;

interface TicketRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?Ticket;

    public function create(array $data): Ticket;

    public function update(int $id, array $data): bool;
}
