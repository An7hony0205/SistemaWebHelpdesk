<?php

namespace App\Domains\Automations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Automations\Models\AutomationRule;
use Illuminate\Support\Facades\DB;

class AutomationRuleController extends Controller
{
    public function index(Request $request)
    {
        $rules = AutomationRule::with(['conditions', 'actions'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('priority', 'asc')
            ->get();
            
        return response()->json($rules);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string',
            'conditions' => 'array',
            'actions' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $rule = AutomationRule::create([
                'tenant_id' => $request->user()->tenant_id,
                'name' => $request->name,
                'description' => $request->description,
                'trigger_event' => $request->trigger_event,
                'is_active' => $request->is_active ?? true,
                'priority' => $request->priority ?? 0,
            ]);

            if ($request->has('conditions')) {
                foreach ($request->conditions as $cond) {
                    $rule->conditions()->create([
                        'field' => $cond['field'],
                        'operator' => $cond['operator'],
                        'value' => $cond['value'],
                    ]);
                }
            }

            foreach ($request->actions as $act) {
                $rule->actions()->create([
                    'action_type' => $act['action_type'],
                    'action_payload' => $act['action_payload'] ?? [],
                ]);
            }

            DB::commit();
            return response()->json($rule->load(['conditions', 'actions']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $rule = AutomationRule::with(['conditions', 'actions'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);
            
        return response()->json($rule);
    }

    public function update(Request $request, $id)
    {
        $rule = AutomationRule::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        
        // MVP: Support toggle quickly
        if ($request->has('is_active') && count($request->all()) === 1) {
            $rule->update(['is_active' => $request->is_active]);
            return response()->json($rule);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string',
            'conditions' => 'array',
            'actions' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $rule->update([
                'name' => $request->name,
                'description' => $request->description,
                'trigger_event' => $request->trigger_event,
                'is_active' => $request->is_active ?? $rule->is_active,
                'priority' => $request->priority ?? $rule->priority,
            ]);

            // Recreate conditions and actions (simplest way to update nested resources in MVP)
            $rule->conditions()->delete();
            $rule->actions()->delete();

            if ($request->has('conditions')) {
                foreach ($request->conditions as $cond) {
                    $rule->conditions()->create([
                        'field' => $cond['field'],
                        'operator' => $cond['operator'],
                        'value' => $cond['value'],
                    ]);
                }
            }

            foreach ($request->actions as $act) {
                $rule->actions()->create([
                    'action_type' => $act['action_type'],
                    'action_payload' => $act['action_payload'] ?? [],
                ]);
            }

            DB::commit();
            return response()->json($rule->load(['conditions', 'actions']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $rule = AutomationRule::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $rule->delete();
        return response()->json(null, 204);
    }

    public function dictionary()
    {
        return response()->json([
            'events' => [
                ['id' => 'ticket.created', 'label' => 'Ticket Creado'],
                ['id' => 'ticket.status_changed', 'label' => 'Estado Cambiado'],
            ],
            'operators' => [
                ['id' => 'equals', 'label' => 'Es igual a'],
                ['id' => 'not_equals', 'label' => 'No es igual a'],
                ['id' => 'contains', 'label' => 'Contiene'],
                ['id' => 'greater_than', 'label' => 'Mayor que'],
            ],
            'fields' => [
                ['id' => 'status_id', 'label' => 'Estado (ID)'],
                ['id' => 'priority_id', 'label' => 'Prioridad (ID)'],
                ['id' => 'category_id', 'label' => 'Categoría (ID)'],
                ['id' => 'title', 'label' => 'Título'],
            ],
            'actions' => [
                ['id' => 'assign_to_user', 'label' => 'Asignar a Usuario', 'fields' => ['user_id']],
                ['id' => 'set_status', 'label' => 'Cambiar Estado', 'fields' => ['status_id']],
            ]
        ]);
    }
}
