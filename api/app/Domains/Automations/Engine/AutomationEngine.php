<?php

namespace App\Domains\Automations\Engine;

use App\Domains\Automations\Actions\AssignTicketAction;
use App\Domains\Automations\Actions\UpdateTicketStatusAction;
use App\Domains\Automations\Models\AutomationRule;
use App\Domains\Support\Models\Ticket;
use Illuminate\Support\Facades\Log;

class AutomationEngine
{
    /**
     * Map of action types to their respective classes
     */
    protected array $actionHandlers = [
        'assign_to_user' => AssignTicketAction::class,
        'set_status' => UpdateTicketStatusAction::class,
    ];

    public function processEvent(string $eventName, Ticket $ticket): void
    {
        // 1. Fetch active rules for this tenant and event
        $rules = AutomationRule::with(['conditions', 'actions'])
            ->where('tenant_id', $ticket->tenant_id)
            ->where('is_active', true)
            ->where('trigger_event', $eventName)
            ->orderBy('priority', 'asc')
            ->get();

        foreach ($rules as $rule) {
            if ($this->evaluateConditions($rule, $ticket)) {
                $this->executeActions($rule, $ticket);
            }
        }
    }

    protected function evaluateConditions(AutomationRule $rule, Ticket $ticket): bool
    {
        if ($rule->conditions->isEmpty()) {
            return true; // No conditions = always match
        }

        // MVP: ALL conditions must pass (AND logic)
        foreach ($rule->conditions as $condition) {
            $fieldValue = $this->getFieldValue($ticket, $condition->field);

            if (! $this->evaluate($fieldValue, $condition->operator, $condition->value)) {
                return false; // Fast fail
            }
        }

        return true;
    }

    protected function getFieldValue(Ticket $ticket, string $field)
    {
        // MVP: direct properties mapping
        return $ticket->{$field} ?? null;
    }

    protected function evaluate($actualValue, string $operator, string $expectedValue): bool
    {
        // Type casting for robust comparison
        $actualString = (string) $actualValue;

        switch ($operator) {
            case 'equals':
                return $actualString === $expectedValue;
            case 'not_equals':
                return $actualString !== $expectedValue;
            case 'contains':
                return str_contains($actualString, $expectedValue);
            case 'greater_than':
                return (int) $actualValue > (int) $expectedValue;
            default:
                return false;
        }
    }

    protected function executeActions(AutomationRule $rule, Ticket $ticket): void
    {
        foreach ($rule->actions as $actionData) {
            if (isset($this->actionHandlers[$actionData->action_type])) {
                $handlerClass = $this->actionHandlers[$actionData->action_type];
                $handler = new $handlerClass;

                try {
                    $handler->execute($ticket, $actionData->action_payload);
                } catch (\Exception $e) {
                    Log::error("Automation Action Error (Rule ID: {$rule->id}): ".$e->getMessage());
                }
            }
        }
    }
}
