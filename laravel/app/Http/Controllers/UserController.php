<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function selection(Request $request):JsonResponse
    {
        $users = User::where('account_status','Assigned')->where('user_id','!=',1)->get();

        return response()->json([
            'users' => $users
        ]);
    }
}
