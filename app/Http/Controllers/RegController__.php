<?php

namespace App\Http\Controllers;


use App\Customs\Services\EmailVerificationService;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserBasic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegController extends Controller
{

    public function __construct(private EmailVerificationService $service)
    {
        
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
                    $this->service->sendVerificationLink($user);
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

    function email_verified(Request $request){
        
    }


}
