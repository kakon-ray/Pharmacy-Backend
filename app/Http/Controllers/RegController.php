<?php

namespace App\Http\Controllers;


use App\Customs\Services\EmailVerificationService;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserBasic;
use App\Models\UserbasicTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

        $user_exist_userbasic = UserBasic::where('email', $request->email)->count();
        $user_exist_userbasictemp = UserbasicTemp::where('email', $request->email)->count();


        if ($user_exist_userbasic) {
            return response()->json([
                'msg' => 'User already exists',
            ]);
        } else if ($user_exist_userbasictemp) {
            return response()->json([
                'msg' => 'Already send email verification link',
            ]);
        } else {


            try {

                $user = UserbasicTemp::create([
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

                if ($user) {
                    $this->service->sendVerificationLink($user);
                    return response()->json([
                        'msg' => 'Signup Completed Please verify your email',
                    ]);
                } else {
                    return response()->json([
                        'msg' => 'Internal server Error',
                    ]);
                }
            } catch (\Exception $err) {
                $user = null;
            }

            if ($user != null) {
                return response()->json(['msg' => 'Registation Completed'], 200);
            } else {
                return response()->json([
                    'msg' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }
    }

    function email_verified(Request $request)
    {
        $already_email_verification = UserBasic::where('email', $request->email)->whereNotNull('email_verified_at')->count();

        if($already_email_verification){
            return response()->json([
                'success' => 'Your email is verified',
            ]);
        }

        $exist_token = DB::table('email_verifications')->where('token', $request->token)->where('email', $request->email)->count();

        if ($exist_token) {

            $userBasic = UserbasicTemp::where('email', $request->email)->first();

            try {
                $user = UserBasic::create([
                    'name' => $userBasic->name,
                    'email' => $userBasic->email,
                    'password' => $userBasic->password,
                    'email_verified_at' => Carbon::now(),
                    'image' => "https://t.ly/xxkam",
                    'last_login' => "not set",
                    'phone' => '01707500512',
                    'date' => $userBasic->date,
                    'time' => $userBasic->time,
                    'status' => 1,
                ]);

                if ($user) {
                    $deleteToken = DB::table('email_verifications')->where('token', $request->token)->where('email', $request->email)->delete();
                    $deleteUserBsicTemp = UserbasicTemp::where('email', $request->email)->delete();

                    $user_info = DB::table('user_basic')->where('email', $request->email,)->first();

                    $UserDetailsData = array(
                        'user_basic_id' => $user_info->id,
                        'first_name' => "not set",
                        'last_name' => "not set",
                        'birthday' => "not set",
                        'phone_number' => "not set",
                        'gender' => "not set",
                        'address' => "not set",
                        'date' => $user_info->date,
                        'time' => $user_info->time,
                        'status' => 1,
                    );

                    DB::table('user_details')->insert($UserDetailsData);

                    return response()->json([
                        'msg' => 'Your email is verified',
                    ]);
                }
            } catch (\Exception $err) {
                return response()->json([
                    'error' => $err,
                ]);
            }
        }else {
            return response()->json([
                'msg' => 'Token is expired',
            ]);
        }
    }
}