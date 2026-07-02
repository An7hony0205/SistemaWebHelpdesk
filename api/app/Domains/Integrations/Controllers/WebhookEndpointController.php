<?php

namespace App\Domains\Integrations\Controllers;

use App\Domains\Integrations\Models\WebhookEndpoint;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookEndpointController extends Controller
{
    public function index(Request $request)
    {
        $endpoints = WebhookEndpoint::with('events')
            ->where('tenant_id', $request->user()->tenant_id)
            ->get();

        return response()->json($endpoints);
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'description' => 'nullable|string',
            'events' => 'required|array',
        ]);

        $endpoint = WebhookEndpoint::create([
            'tenant_id' => $request->user()->tenant_id,
            'url' => $request->url,
            'secret' => Str::random(40),
            'description' => $request->description,
        ]);

        foreach ($request->events as $eventName) {
            $endpoint->events()->create(['event_name' => $eventName]);
        }

        return response()->json($endpoint->load('events'), 201);
    }

    public function logs(Request $request, $id)
    {
        $endpoint = WebhookEndpoint::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        $logs = $endpoint->logs()->orderBy('created_at', 'desc')->take(50)->get();

        return response()->json($logs);
    }

    public function destroy(Request $request, $id)
    {
        $endpoint = WebhookEndpoint::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $endpoint->delete();

        return response()->json(null, 204);
    }
}
