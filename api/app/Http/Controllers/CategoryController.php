<?php

namespace App\Http\Controllers;

use App\Domains\Support\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();

        return response()->json($categories);
    }
}
