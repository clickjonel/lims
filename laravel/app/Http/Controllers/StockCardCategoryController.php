<?php

namespace App\Http\Controllers;

use App\Models\StockCardCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockCardCategoryController extends Controller
{
    public function selection():JsonResponse
    {
            $categories = StockCardCategory::get(['id','name']);

            return response()->json([
                'categories' => $categories
            ]);
    }
}
