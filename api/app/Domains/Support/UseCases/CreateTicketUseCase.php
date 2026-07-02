<?php

namespace App\Domains\Support\UseCases;

use App\Domains\Administration\Status;
use App\Domains\Support\Ticket;
use App\DTOs\TicketCreateDTO;
use App\Events\TicketCreated;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Support\Facades\Event;

class CreateTicketUseCase
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ) {}

    public function execute(TicketCreateDTO $dto): Ticket
    {
        $statusAbierto = Status::where('name', 'Abierto')->first();

        $ticket = $this->ticketRepository->create([
            'user_id' => $dto->userId,
            'category_id' => $dto->categoryId,
            'title' => $dto->title,
            'description' => $dto->description,
            'priority_id' => $dto->priorityId,
            'status_id' => $statusAbierto?->id,
            'is_active' => true,
        ]);

        Event::dispatch(new TicketCreated($ticket));

        return $ticket;
    }
}
