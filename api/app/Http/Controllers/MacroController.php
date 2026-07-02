<?php

namespace App\Http\Controllers;

use App\Domains\Support\Macro;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MacroController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // El trait BelongsToTenant ya filtra automáticamente por tenant
        $macros = Macro::orderBy('title')->get();

        return response()->json($macros);
    }

    public function store(Request $request)
    {
        // En una app real se añadiría policy: $this->authorize('create', Macro::class);
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $macro = Macro::create([
            'tenant_id' => $request->user()->tenant_id,
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => true,
        ]);

        return response()->json($macro, 201);
    }

    public function show(Request $request, $id)
    {
        $macro = Macro::findOrFail($id);

        return response()->json($macro);
    }

    public function update(Request $request, $id)
    {
        $macro = Macro::findOrFail($id);

        $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
        ]);

        $macro->update($request->only(['title', 'content', 'is_active']));

        return response()->json($macro);
    }

    public function destroy(Request $request, $id)
    {
        $macro = Macro::findOrFail($id);
        $macro->delete();

        return response()->json(null, 204);
    }
}
