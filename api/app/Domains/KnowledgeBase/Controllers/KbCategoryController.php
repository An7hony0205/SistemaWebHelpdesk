<?php

namespace App\Domains\KnowledgeBase\Controllers;

use App\Domains\KnowledgeBase\Models\KbCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KbCategoryController extends Controller
{
    public function index(Request $request)
    {
        // Recursivamente trae hijos. Para el MVP basta con with('children').
        $categories = KbCategory::with('children')
            ->where('tenant_id', $request->user()->tenant_id)
            ->whereNull('parent_id') // Top level
            ->get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:kb_categories,id',
            'description' => 'nullable|string',
        ]);

        $category = KbCategory::create([
            'tenant_id' => $request->user()->tenant_id,
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
        ]);

        return response()->json($category, 201);
    }
}
