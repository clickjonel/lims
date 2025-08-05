<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SectionController extends Controller
{
   public function selection():JsonResponse
   {
        $sections = Section::get(['section_id','section_name']);

        return response()->json([
            'sections' => $sections
        ]);
   }

public function personnelSelection(Request $request):JsonResponse
{
    $section = Section::find($request->section_id);
    $personnel = $section->personnel()->select('dohis_user.user_id','dohis_user.first_name','dohis_user.middle_name','dohis_user.last_name')->get();

    return response()->json([
        'personnel' => $personnel
    ]);
}

}
