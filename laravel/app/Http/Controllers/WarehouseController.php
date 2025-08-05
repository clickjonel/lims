<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function selection():JsonResponse
    {
            $warehouses = Warehouse::get(['id','name']);

            return response()->json([
                'warehouses' => $warehouses
            ]);
    }
}
