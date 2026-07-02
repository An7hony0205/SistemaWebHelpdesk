<?php

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Providers\KpiProviderInterface;
use App\Domains\Identity\Tenant;
use App\Domains\Identity\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * @var array<string, KpiProviderInterface>
     */
    protected array $providers = [];

    public function registerProvider(KpiProviderInterface $provider)
    {
        $this->providers[$provider->getIdentifier()] = $provider;
    }

    public function getMetric(string $identifier, Tenant $tenant, ?User $user, Carbon $start, Carbon $end)
    {
        if (!isset($this->providers[$identifier])) {
            throw new \Exception("KPI Provider not found: {$identifier}");
        }

        $provider = $this->providers[$identifier];
        $cacheKey = $this->buildCacheKey($identifier, $tenant, $user, $start, $end);

        return Cache::tags(["tenant:{$tenant->id}:kpis"])->remember(
            $cacheKey,
            $provider->getCacheTtl(),
            function () use ($provider, $tenant, $user, $start, $end) {
                return $provider->calculate($tenant, $user, $start, $end);
            }
        );
    }

    private function buildCacheKey(string $identifier, Tenant $tenant, ?User $user, Carbon $start, Carbon $end): string
    {
        $userId = $user ? $user->id : 'all';
        return "kpi:{$identifier}:tenant:{$tenant->id}:user:{$userId}:{$start->toDateString()}:{$end->toDateString()}";
    }

    public function getAvailableWidgets(): array
    {
        return array_keys($this->providers);
    }
}
