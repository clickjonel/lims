<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function list(Request $request)
    {
        $section_id = $request->section_id ?? null;
        $user_id = $request->user_id ?? null;

        // $user = User::with(['assignment'])->find($user_id);
        $notifications = $notifications = Notification::where('user_id', $user_id)
                                            ->whereNull('section_id')
                                            ->orWhere(function ($query) use ($section_id) {
                                                $query->where('section_id', $section_id)
                                                    ->whereNull('user_id');
                                            })->get();
     

        return response()->json([
            'notifications' => $notifications,
            'total' => $notifications->count()
        ]);
    }
}
