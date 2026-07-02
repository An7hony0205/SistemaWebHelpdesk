<?php

namespace App\Domains\Integrations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Support\Models\Ticket;
use App\Domains\Identity\Tenant;
use Illuminate\Support\Facades\Log;

class InboundWebhookController extends Controller
{
    /**
     * Endpoint sin auth tradicional, se valida mediante un token/secreto en la URL
     * POST /api/webhooks/inbound/{tenant_uuid}
     */
    public function handle(Request $request, $tenantUuid)
    {
        // En MVP simulamos buscando el tenant por un UUID o ID directamente si lo encriptamos
        $tenant = Tenant::findOrFail($tenantUuid);

        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        try {
            $ticket = Ticket::create([
                'tenant_id' => $tenant->id,
                'title' => $request->title,
                'content' => $request->content,
                // Hardcode status y priority por defecto en base al tenant
                'status_id' => \Illuminate\Support\Facades\DB::table('statuses')->where('name', 'Abierto')->value('id'),
                'priority_id' => \Illuminate\Support\Facades\DB::table('priorities')->where('name', 'Media')->value('id'),
            ]);

            Log::info("Inbound webhook created ticket {$ticket->id} for tenant {$tenant->id}");
            
            return response()->json(['message' => 'Ticket created', 'ticket_id' => $ticket->id], 201);
        } catch (\Exception $e) {
            Log::error("Inbound webhook failed: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
