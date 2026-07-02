<?php

namespace App\Domains\Integrations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Integrations\Models\ApiKey;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        $keys = ApiKey::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($keys);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = Str::random(60);

        $apiKey = ApiKey::create([
            'tenant_id' => $request->user()->tenant_id,
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'token' => $token, // En prod, esto se hashea. MVP = plain text
        ]);

        return response()->json([
            'api_key' => $apiKey,
            'plain_text_token' => $token, // El frontend solo lo verá una vez
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $key = ApiKey::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $key->delete();
        
        return response()->json(null, 204);
    }
}
