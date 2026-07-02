<?php

namespace App\Domains\Dashboard\Providers;

use App\Domains\Identity\Tenant;
use App\Domains\Identity\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SlaComplianceKpi implements KpiProviderInterface
{
    public function getIdentifier(): string
    {
        return 'sla_compliance';
    }

    public function calculate(Tenant $tenant, ?User $user, Carbon $start, Carbon $end): mixed
    {
        $query = DB::table('ticket_slas')
            ->join('tickets', 'ticket_slas.ticket_id', '=', 'tickets.id')
            ->where('tickets.tenant_id', $tenant->id)
            ->whereBetween('tickets.created_at', [$start, $end]);

        if ($user && !$user->hasRole('Admin')) {
            $query->where(function($q) use ($user) {
                $q->where('tickets.user_id', $user->id)
                  ->orWhere('tickets.assigned_to', $user->id);
            });
        }

        // Breached = resolution_breached is true
        $result = $query->selectRaw('
            COUNT(ticket_slas.id) as total_sla,
            SUM(CASE WHEN resolution_breached = 1 THEN 1 ELSE 0 END) as breached,
            SUM(CASE WHEN resolution_completed_at IS NOT NULL AND resolution_breached = 0 THEN 1 ELSE 0 END) as complied
        ')->first();

        $total = (int) $result->total_sla;
        $complied = (int) $result->complied;
        $breached = (int) $result->breached;

        $complianceRate = $total > 0 ? round(($complied / $total) * 100, 2) : 100;

        return [
            'compliance_rate' => $complianceRate,
            'complied' => $complied,
            'breached' => $breached,
        ];
    }

    public function getCacheTtl(): int
    {
        return 300; // 5 min
    }
}
