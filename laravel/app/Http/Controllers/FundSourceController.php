<?php

namespace App\Http\Controllers;

use App\Models\FundSource;
use Illuminate\Http\Request;

class FundSourceController extends Controller
{
    public function selection()
    {
        $fund_sources = FundSource::get(['id','name']);

        return response()->json([
            'fund_sources' => $fund_sources
        ]);
    }
}
