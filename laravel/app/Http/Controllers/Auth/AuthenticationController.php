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

        if($user && $user->account_status !== 'Assigned'){

            return response()->json([
                'status' => false,
                'message' => 'Account Deactivated by Administrator'
            ],401);
        }

        if($user){

            $permitted_sections = [25,28];

            // if permanent,supply or ict
            if($user->assignment->employee_status_id === 1 || in_array($user->assignment->section->section_id,$permitted_sections)){
                $token = $user->createToken($user->user_id)->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'user' => $user,
                    'status' => true
                ],200);
            }

            // not permanent
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'You Currently Have No Access to the System, Permanent and Admin Accounts Are Only Permitted Access. For More Information, Please Contact ICTMU Section.',
                ],401);
            }

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
