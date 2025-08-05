<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function login(Request $request):JsonResponse
    {   
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::with(['assignment.section'])->where('username',$credentials['username'])->where('password',md5($credentials['password']))->first();

        if($user->account_status !== 'Assigned'){

            return response()->json([
                'status' => false,
                'message' => 'Account Deactivated by Administrator'
            ],401);
        }

        if($user){
            $token = $user->createToken($user->user_id)->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'status' => true
            ],200);
        }

        else{
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials, Please Try Again With Correct Dohis Credentials'
            ],401);
        }

    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to logout: ' . $e->getMessage()
            ], 500);
        }
    }

}
