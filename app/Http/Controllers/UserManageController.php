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

        $all_user = UserBasic::where('role', 'user')->get();

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
}
