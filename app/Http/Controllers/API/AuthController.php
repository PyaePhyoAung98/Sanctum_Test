<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        //create token

        $token = $user->createToken('Laravel-8');

        return response()->json([
            'status' => 200,
            'message' => 'Successfully Regisered!',
            'token' => $token->plainTextToken
        ]);
    }
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();

            //user:list -> To use permission//optional|| Laravel-8 is token name
            
            $token = $user->createToken('Laravel-8', ['user:list']);

            return response()->json([
                'status' => 200,
                'message' => 'Successfully Login!',
                'token' => $token->plainTextToken
            ]);
        }
    }
    public function profile(Request $request)
    {
        $user = auth()->user();
        return response()->json([
            'status' => 200,
            'message' => 'Successfully Login!',
            'data' => $user
        ]);
    }
    public function userList(Request $request)
    {
        if (!auth()->user()->tokenCan('user:list')) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized',

            ]);
        }
        $user = User::all();
        return response()->json([
            'status' => 200,
            'message' => 'Successfully Login!',
            'data' => $user
        ]);
    }
    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        return response()->json(['status'=>200,'message'=>'Logout']);

    }
}
