<?php

namespace App\Domains\Sla\Listeners;

use App\Domains\Administration\Services\BusinessTimeService;
use App\Domains\Identity\Tenant;
use App\Domains\Sla\SlaPolicy;
use App\Domains\Sla\TicketSla;
use App\Events\TicketCreated;

class TicketCreatedListener
{
    public function __construct(private BusinessTimeService $businessTimeService) {}

    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        // Buscar política activa del tenant
        $policy = SlaPolicy::with('targets')
            ->where('tenant_id', $ticket->tenant_id)
            ->where('is_active', true)
            ->first();

        if (! $policy) {
            return;
        }

        // Buscar el target según la prioridad del ticket
        $target = $policy->targets->where('priority_id', $ticket->priority_id)->first();
        if (! $target) {
            return;
        }

        $now = now();
        $tenant = $ticket->tenant; // Asumimos que podemos obtenerlo, o lo cargamos

        if (! $tenant) {
            $tenant = Tenant::find($ticket->tenant_id);
        }

        $frtDueAt = $this->businessTimeService->addBusinessMinutes($now, $target->first_response_time_minutes, $tenant);
        $rtDueAt = $this->businessTimeService->addBusinessMinutes($now, $target->resolution_time_minutes, $tenant);

        TicketSla::create([
            'tenant_id' => $ticket->tenant_id,
            'ticket_id' => $ticket->id,
            'sla_policy_id' => $policy->id,
            'first_response_due_at' => $frtDueAt,
            'resolution_due_at' => $rtDueAt,
        ]);
    }
}
