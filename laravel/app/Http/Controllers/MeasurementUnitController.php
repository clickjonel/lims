<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeasurementUnitController extends Controller
{
    public function selection():JsonResponse
    {
        $measurement_units = MeasurementUnit::get(['id','name']);

        return response()->json([
            'measurement_units' => $measurement_units
        ]);
    }
}
