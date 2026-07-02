<?php

namespace App\Domains\Dashboard\Providers;

use App\Domains\Identity\Tenant;
use App\Domains\Identity\User;
use Illuminate\Support\Carbon;

interface KpiProviderInterface
{
    /**
     * Get the unique identifier for this KPI widget.
     * e.g., 'tickets_count', 'sla_compliance_rate'
     */
    public function getIdentifier(): string;

    /**
     * Calculate the metric based on the context.
     * Should return a structured array depending on the chart type.
     */
    public function calculate(Tenant $tenant, ?User $user, Carbon $start, Carbon $end): mixed;

    /**
     * Cache Time-To-Live in seconds.
     */
    public function getCacheTtl(): int;
}
