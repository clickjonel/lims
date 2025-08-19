<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function list(Request $request)
    {
        // $notifications = Notification::when($request->section_id,function($query){
        //                         $query->where('section_id')
        //                     })
        //                     ->get();

        // return response()->json([
        //     'notifications' => $notifications
        // ]);
    }
}
