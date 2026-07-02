<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Support\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
            
        return response()->json($categories);
    }
}
