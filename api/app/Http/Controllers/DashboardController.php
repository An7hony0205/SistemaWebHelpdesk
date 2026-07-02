<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Dashboard\Services\DashboardService;
use App\Domains\Dashboard\Models\DashboardLayout;
use App\Domains\Identity\Tenant;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function layout(Request $request)
    {
        // MVP: Busca layout custom del usuario, luego del Tenant, o devuelve un default
        $layout = DashboardLayout::where('configurable_type', \App\Domains\Identity\User::class)
            ->where('configurable_id', $request->user()->id)
            ->first();

        if (!$layout) {
            $layout = DashboardLayout::where('configurable_type', Tenant::class)
                ->where('configurable_id', $request->user()->tenant_id)
                ->first();
        }

        if (!$layout) {
            // Default estático
            return response()->json([
                'widgets_layout' => [
                    ['id' => 'tickets_count', 'type' => 'cards'],
                    ['id' => 'sla_compliance', 'type' => 'gauge'],
                    ['id' => 'tickets_by_status', 'type' => 'doughnut'],
                ]
            ]);
        }

        return response()->json($layout);
    }

    public function updateLayout(Request $request)
    {
        $request->validate([
            'widgets_layout' => 'required|array'
        ]);

        $layout = DashboardLayout::updateOrCreate(
            [
                'configurable_type' => \App\Domains\Identity\User::class,
                'configurable_id' => $request->user()->id,
            ],
            [
                'widgets_layout' => $request->widgets_layout,
                'is_default' => false,
            ]
        );

        return response()->json($layout);
    }

    public function metrics(Request $request)
    {
        $request->validate([
            'widgets' => 'required|array', // ['tickets_count', 'sla_compliance']
            'period' => 'string|in:today,week,month,year',
        ]);

        $period = $request->period ?? 'month';
        $start = match($period) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
        $end = Carbon::now();

        $tenant = Tenant::find($request->user()->tenant_id);
        $user = $request->user();

        $results = [];

        foreach ($request->widgets as $widgetId) {
            try {
                $results[$widgetId] = $this->dashboardService->getMetric($widgetId, $tenant, $user, $start, $end);
            } catch (\Exception $e) {
                $results[$widgetId] = ['error' => $e->getMessage()];
            }
        }

        return response()->json($results);
    }
}
