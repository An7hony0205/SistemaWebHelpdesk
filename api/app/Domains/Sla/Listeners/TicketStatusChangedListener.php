<?php

namespace App\Domains\Sla\Listeners;

use App\Domains\Administration\Services\BusinessTimeService;
use App\Domains\Identity\Tenant;
use App\Domains\Sla\TicketSla;
use App\Events\TicketStatusUpdated;

class TicketStatusChangedListener
{
    public function __construct(private BusinessTimeService $businessTimeService) {}

    public function handle(TicketStatusUpdated $event): void
    {
        $ticketSla = TicketSla::where('ticket_id', $event->ticket->id)->first();
        if (! $ticketSla) {
            return;
        }

        if ($event->newStatusName === 'Esperando Cliente' && ! $ticketSla->paused_at) {
            // Pausar SLA
            $ticketSla->update([
                'paused_at' => now(),
            ]);
        } elseif (in_array($event->newStatusName, ['Abierto', 'Resuelto', 'Cerrado']) && $ticketSla->paused_at) {
            // Reanudar SLA
            $now = now();

            $tenant = Tenant::find($ticketSla->tenant_id);
            // Por simplicidad calculamos diferencia en minutos brutos y luego lo ajustamos.
            // Lo ideal sería una función `diffInBusinessMinutes` en BusinessTimeService.
            // Aqui haremos un cálculo directo
            $pausedMinutes = $ticketSla->paused_at->diffInMinutes($now);

            $ticketSla->update([
                'accumulated_pause_minutes' => $ticketSla->accumulated_pause_minutes + $pausedMinutes,
                'paused_at' => null,
                'resolution_due_at' => $this->businessTimeService->addBusinessMinutes($ticketSla->resolution_due_at, $pausedMinutes, $tenant),
                'first_response_due_at' => $ticketSla->first_response_completed_at ? $ticketSla->first_response_due_at : $this->businessTimeService->addBusinessMinutes($ticketSla->first_response_due_at, $pausedMinutes, $tenant),
            ]);
        }

        // Marcar Resolution Time si el ticket fue resuelto
        if (in_array($event->newStatusName, ['Resuelto', 'Cerrado']) && ! $ticketSla->resolution_completed_at) {
            $ticketSla->update([
                'resolution_completed_at' => now(),
                'resolution_breached' => now()->greaterThan($ticketSla->resolution_due_at),
            ]);
        }
    }
}
