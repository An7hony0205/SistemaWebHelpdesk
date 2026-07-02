<?php

namespace App\Domains\Dashboard\Providers;

use App\Domains\Identity\Tenant;
use App\Domains\Identity\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TicketsByStatusKpi implements KpiProviderInterface
{
    public function getIdentifier(): string
    {
        return 'tickets_by_status';
    }

    public function calculate(Tenant $tenant, ?User $user, Carbon $start, Carbon $end): mixed
    {
        $query = DB::table('tickets')
            ->join('statuses', 'tickets.status_id', '=', 'statuses.id')
            ->where('tickets.tenant_id', $tenant->id)
            ->whereBetween('tickets.created_at', [$start, $end]);

        if ($user && !$user->hasRole('Admin')) {
            $query->where(function($q) use ($user) {
                $q->where('tickets.user_id', $user->id)
                  ->orWhere('tickets.assigned_to', $user->id);
            });
        }

        $result = $query->select('statuses.name', DB::raw('COUNT(tickets.id) as count'))
            ->groupBy('statuses.name')
            ->get();

        $labels = [];
        $data = [];

        foreach ($result as $row) {
            $labels[] = $row->name;
            $data[] = (int) $row->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getCacheTtl(): int
    {
        return 300; // 5 min
    }
}
