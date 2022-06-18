<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    //register user
    public function Register(UserRequest $rquest) {
        $user = new User();
        $user->name = $rquest->name;
        $user->email = $rquest->email;
        $user->password = Hash::make($rquest->password);
        $user->role = $rquest->role;

        $user->save();

        return response()->json([
            'message' => 'user created successfully'
        ], 200);
    }
    //Login user
    public function Login(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Login information is invalid.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('authToken', [$user->role])->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    //Logout user
    public function Logout(){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
