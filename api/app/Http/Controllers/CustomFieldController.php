<?php

namespace App\Http\Controllers;

use App\Domains\Support\CustomField;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $fields = CustomField::orderBy('id')->get();

        return response()->json($fields);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,select,boolean,date',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        $field = CustomField::create([
            'tenant_id' => $request->user()->tenant_id,
            'name' => strtolower(str_replace(' ', '_', $request->name)),
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->options,
            'is_required' => $request->is_required ?? false,
            'is_active' => true,
        ]);

        return response()->json($field, 201);
    }

    public function update(Request $request, $id)
    {
        $field = CustomField::findOrFail($id);

        $request->validate([
            'label' => 'string|max:255',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $field->update($request->only(['label', 'options', 'is_required', 'is_active']));

        return response()->json($field);
    }

    public function destroy(Request $request, $id)
    {
        $field = CustomField::findOrFail($id);
        $field->delete();

        return response()->json(null, 204);
    }
}
