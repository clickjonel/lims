<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function selection(Request $request):JsonResponse
    {
        $categories = Category::get();

        return response()->json([
            'categories' => $categories
        ]);
    }
}
