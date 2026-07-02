<?php

namespace App\Domains\Sla\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domains\Sla\TicketSla;
use Illuminate\Support\Facades\Event;

class SlaEscalationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = now();

        // Buscar tickets con SLA activo que hayan incumplido FRT (y aún no estén marcados como breach)
        $frtBreached = TicketSla::whereNull('first_response_completed_at')
            ->where('first_response_breached', false)
            ->where('first_response_due_at', '<', $now)
            ->get();

        foreach ($frtBreached as $sla) {
            $sla->update(['first_response_breached' => true]);
            // Event::dispatch(new SlaBreached($sla, 'FRT'));
        }

        // Buscar tickets que hayan incumplido RT (y aún no estén marcados como breach)
        $rtBreached = TicketSla::whereNull('resolution_completed_at')
            ->where('resolution_breached', false)
            ->where('resolution_due_at', '<', $now)
            ->get();

        foreach ($rtBreached as $sla) {
            $sla->update(['resolution_breached' => true]);
            // Event::dispatch(new SlaBreached($sla, 'RT'));
        }
    }
}
