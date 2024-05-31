<?php

namespace App\Http\Controllers;

use App\Models\User;
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


    function regisign_upster(Request $request)
    {
        // return $request->all();

        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d', strtotime('-0 day'));
        $time = date('H:i:s', strtotime('-0 day'));

        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . UserBasic::class],
            'password' => ['required','confirmed', Rules\Password::defaults()],
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }else{
        

            try {
    
                $user = UserBasic::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'image' => "https://t.ly/xxkam",
                    'last_login' => "not set",
                    'phone' => '01707500512',
                    'date' => $date,
                    'time' => $time,
                    'status' => 1,
                ]);

                if($user){
                    $user_info = DB::table('user_basic')->where('email',$request->email,)->first();

                    $UserDetailsData = array(
                        'user_basic_id' => $user_info->id,
                        'first_name' => "not set",
                        'last_name' => "not set",
                        'birthday' => "not set",
                        'phone_number' => "not set",
                        'gender' => "not set",
                        'address' => "not set",
                        'date' => $date,
                        'time' => $time,
                        'status' => 1,
                    );
    
                    DB::table('user_details')->insert($UserDetailsData);
                  
                }else{
                    return response()->json([
                        'msg' => 'User Not Found',
                    ], 400);
                }

               


            } catch (\Exception $err) {
                $user = null;
            }
    
            if($user != null){
                return response()->json(['msg' => 'Registation Completed'], 200);
            }else{
                return response()->json([
                    'msg' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }

        

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
                    'image' => $UserBasic->image,
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

 
    public function me()
    {
        
        return response()->json($this->guard()->user());
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
