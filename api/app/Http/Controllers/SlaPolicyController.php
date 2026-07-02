<?php

namespace App\Http\Controllers;

use App\Domains\Sla\SlaPolicy;
use App\Domains\Sla\SlaTarget;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlaPolicyController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $policies = SlaPolicy::with('targets.priority')
            ->where('tenant_id', $request->user()->tenant_id)
            ->get();

        return response()->json($policies);
    }

    public function store(Request $request)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'conditions' => 'nullable|array',
            'targets' => 'required|array',
            'targets.*.priority_id' => 'required|exists:priorities,id',
            'targets.*.first_response_time_minutes' => 'required|integer|min:0',
            'targets.*.resolution_time_minutes' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            if ($request->input('is_active', true)) {
                // Desactivar otras si solo queremos una activa por defecto,
                // pero si soportamos múltiples, omitimos este paso.
                // SlaPolicy::where('tenant_id', $request->user()->tenant_id)->update(['is_active' => false]);
            }

            $policy = SlaPolicy::create([
                'tenant_id' => $request->user()->tenant_id,
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->input('is_active', true),
                'conditions' => $request->conditions,
            ]);

            foreach ($request->targets as $target) {
                SlaTarget::create([
                    'sla_policy_id' => $policy->id,
                    'priority_id' => $target['priority_id'],
                    'first_response_time_minutes' => $target['first_response_time_minutes'],
                    'resolution_time_minutes' => $target['resolution_time_minutes'],
                ]);
            }
            DB::commit();

            return response()->json($policy->load('targets.priority'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Request $request, $id)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $policy = SlaPolicy::with('targets.priority')
            ->where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        return response()->json($policy);
    }

    public function update(Request $request, $id)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'conditions' => 'nullable|array',
            'targets' => 'required|array',
            'targets.*.id' => 'nullable|exists:sla_targets,id',
            'targets.*.priority_id' => 'required|exists:priorities,id',
            'targets.*.first_response_time_minutes' => 'required|integer|min:0',
            'targets.*.resolution_time_minutes' => 'required|integer|min:0',
        ]);

        $policy = SlaPolicy::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        DB::beginTransaction();
        try {
            $policy->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->input('is_active', true),
                'conditions' => $request->conditions,
            ]);

            // Actualizar targets
            $existingTargetIds = $policy->targets()->pluck('id')->toArray();
            $incomingTargetIds = array_filter(array_column($request->targets, 'id'));

            // Eliminar los que ya no vienen
            $targetsToDelete = array_diff($existingTargetIds, $incomingTargetIds);
            if (! empty($targetsToDelete)) {
                SlaTarget::whereIn('id', $targetsToDelete)->delete();
            }

            foreach ($request->targets as $target) {
                if (isset($target['id'])) {
                    SlaTarget::where('id', $target['id'])->update([
                        'priority_id' => $target['priority_id'],
                        'first_response_time_minutes' => $target['first_response_time_minutes'],
                        'resolution_time_minutes' => $target['resolution_time_minutes'],
                    ]);
                } else {
                    SlaTarget::create([
                        'sla_policy_id' => $policy->id,
                        'priority_id' => $target['priority_id'],
                        'first_response_time_minutes' => $target['first_response_time_minutes'],
                        'resolution_time_minutes' => $target['resolution_time_minutes'],
                    ]);
                }
            }

            DB::commit();

            return response()->json($policy->fresh('targets.priority'));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Request $request, $id)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $policy = SlaPolicy::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $policy->delete();

        return response()->json(null, 204);
    }
}
