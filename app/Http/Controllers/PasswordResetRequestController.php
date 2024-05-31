<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// password reset to import
use Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\UserBasic;

class PasswordResetRequestController extends Controller
{
    function reset_password_submit(Request $request)
    {

        $usere_check = UserBasic::where('email', $request->email)->count();

        if($usere_check){

            $already_maild = DB::table('password_resets')->where('email', $request->email)->count();

            if($already_maild){
                return response()->json(['msg' => 'Already send this email']);
            }

            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);
    
            Mail::send('auth.mailforget', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Password');
            });
    
            return response()->json(['msg' => 'Send Email']);
        }else{
            return response()->json(['msg' => 'User Not Found']);
        }

    }

    function show_reset_password_form($token){
        // return redirect('http://localhost:3000/reset-password/?token=' . $token);
        return redirect('http://localhost:3000/admin/passwordreset/resetform/?token=' . $token);
    }

    function new_password_submit(Request $request)
    {
        // return response()->json($request->all());

        $request->validate([
            'email' => 'required|email|exists:user_basic',
            'password' => 'required|string|min:8|',
            'confirm_password' => 'required',
        ]);

        $updatepassword = DB::table('password_resets')->where('email',$request->email,)->where('token',$request->token)->first();

        if (!$updatepassword) {
            return response()->json(['error' => 'Invalid']);
        } else {
            $responce = UserBasic::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

            if ($responce) {
                DB::table('password_resets')->where('email', $request->email)->delete();
                return response()->json(['success' => 'Password Reset Successfully']);
            }
        }
    }

}
