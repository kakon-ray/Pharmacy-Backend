<?php

namespace App\Http\Controllers;

use App\Models\UserBasic;
use Illuminate\Http\Request;

class UserManageController extends Controller
{
    public function __construct()
    {
        \Config::set('auth.defaults.guard', 'userbasic');
    }

    public function get_user(Request $request)
    {

        $all_user = UserBasic::get();

        if ($all_user) {
            return response()->json([
                'users' => $all_user,
                'success' => true,
            ]);
        }else{
            return response()->json([
                'users' => 'User not found',
                'success' => false,
            ]);
        }
    }


    public function userpermission(Request $request)
    {
        $user = UserBasic::find($request->id);
        if(!$user){
            return response()->json([
                'msg' => 'User not found',
                'success' => false,
            ]);
        }else{
            $user->role = 'admin';
        }
      
        $responce = $user->save();

        if($responce){
            $all_user = UserBasic::get();
            return response()->json([
                'msg' => 'Permission Sucessfully',
                'users' => $all_user,
                'success' => true,
            ]);
        }
        
    }

    public function canclepermission(Request $request)
    {
        $user = UserBasic::find($request->id);
        if(!$user){
            return response()->json([
                'msg' => 'User not found',
                'success' => false,
            ]);
        }else{
            $user->role = 'user';
        }
      
        $responce = $user->save();
        
        if($responce){
            $all_user = UserBasic::get();
            return response()->json([
                'msg' => 'Permission Cancle Sucessfully',
                'users' => $all_user,
                'success' => true,
            ]);
        }

    }
}
