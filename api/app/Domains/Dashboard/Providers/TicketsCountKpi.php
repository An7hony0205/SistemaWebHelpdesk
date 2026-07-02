<?php

namespace App\Domains\Dashboard\Providers;

use App\Domains\Identity\Tenant;
use App\Domains\Identity\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TicketsCountKpi implements KpiProviderInterface
{
    public function getIdentifier(): string
    {
        return 'tickets_count';
    }

    public function calculate(Tenant $tenant, ?User $user, Carbon $start, Carbon $end): mixed
    {
        $query = DB::table('tickets')
            ->where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$start, $end]);

        if ($user) {
            // Si el dashboard es de un usuario, podría ver solo sus tickets asignados.
            // O dependiendo del rol, puede ver todos. Para MVP, si hay user, filtramos.
            if (! $user->hasRole('Admin')) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('assigned_to', $user->id);
                });
            }
        }

        // Consultas agregadas con selectRaw para máximo rendimiento (1 sola query)
        $result = $query->selectRaw('
            COUNT(id) as total,
            SUM(CASE WHEN status_id = (SELECT id FROM statuses WHERE name = "Abierto" LIMIT 1) THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status_id = (SELECT id FROM statuses WHERE name = "Cerrado" LIMIT 1) THEN 1 ELSE 0 END) as closed,
            SUM(CASE WHEN status_id = (SELECT id FROM statuses WHERE name = "Pendiente" LIMIT 1) THEN 1 ELSE 0 END) as pending
        ')->first();

        return [
            'total' => (int) $result->total,
            'open' => (int) $result->open,
            'closed' => (int) $result->closed,
            'pending' => (int) $result->pending,
        ];
    }

    public function getCacheTtl(): int
    {
        return 60; // 1 min (para mantenerlo "en vivo")
    }
}
