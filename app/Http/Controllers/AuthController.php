<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBasic;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function __construct()
    {
        \Config::set('auth.defaults.guard','userbasic');
    }
    

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $UserBasic = DB::table('user_basic')->where('email', $request->email)->first();

        if ($UserBasic == true) {
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            if (!$token = auth()->attempt($validator->validated())) {
                return response()->json(['error' => 'Unauthorize']);
            } else {
                $user_basic = UserBasic::find($UserBasic->id);
                $user_basic->remember_token = $token;
                $user_basic->save();

                $userData=[
                    'success' => true,
                    'message' => 'Login Success',
                    'token' => $token,
                    'id' => $UserBasic->id,
                    'name' => $UserBasic->name,
                    'email' => $UserBasic->email,
                    'token_type' => 'bearer'
                  ];

                  return response()->json($userData);
            }
        } else {
            return response()->json([
                'success' => false,
                'ErrorMessage' => 'email address not match',
            ]);
        }
    }

 
    public function me(Request $request)
    {
        if($request->token){
            $user = UserBasic::where('remember_token',$request->token)->first();
            if($user){
                return response()->json(['user' => $user]);
            }else{
                return response()->json(['error' => 'Unauthenticated User']);
            }
            
        }else{
            return response()->json(['error' => 'Unauthenticated User']);
        }

    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }


    public function guard()
    {
        return Auth::guard();
    }
}
