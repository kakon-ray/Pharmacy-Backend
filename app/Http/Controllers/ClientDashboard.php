<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserBasic;
use Illuminate\Support\Facades\Validator;

class ClientDashboard extends Controller
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


    public function service_order_dashboard()
    {
        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d', strtotime('-0 day'));
        $time = date('H:i:s', strtotime('-0 day'));

        $jsonData = file_get_contents('php://input');
        $post = json_decode(file_get_contents('php://input'), TRUE);

        if ($jsonData == "") {
            $result = array(
                'resultinvalid' => 'invalid request'
            );
            return response()->json($result);
        } else {
            $user_basic_id = $post['user_id'];

            $ServiceOrder = DB::select("select * from service_order WHERE user_basic_id='$user_basic_id'");

            $ServiceOrderArray = array();

            foreach ($ServiceOrder as $ServiceOrderDetails) {

                $sevice_items_id = $ServiceOrderDetails->sevice_items_id;

                $sevice_items_details = DB::select("select service_id, category_id, slug, title, image from sevice_items WHERE id='$sevice_items_id'");

                $ServiceOrderArray[] = [
                    'service_order_id' => $ServiceOrderDetails->id,
                    'order_price' => $ServiceOrderDetails->order_price,
                    'payment_status' => $ServiceOrderDetails->payment_status,
                    'order_status' => $ServiceOrderDetails->order_status,
                    'order_date' => $ServiceOrderDetails->order_date,
                    'sevice_items' => $sevice_items_details
                ];
            }
            return response()->json($ServiceOrderArray);
            // print_r($ServiceOrder);
        }
    }
    public function manage_order()
    {
        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d', strtotime('-0 day'));
        $time = date('H:i:s', strtotime('-0 day'));

        $jsonData = file_get_contents('php://input');
        $post = json_decode(file_get_contents('php://input'), TRUE);

        if ($jsonData == "") {
            $result = array(
                'resultinvalid' => 'invalid request'
            );
            return response()->json($result);
        } else {
            $user_basic_id = $post['user_id'];
            $ServiceOrder = DB::select("select * from service_order WHERE user_basic_id='$user_basic_id'");
            $ServiceOrderArray = array();

            foreach ($ServiceOrder as $ServiceOrderDetails) {
                $sevice_items_id = $ServiceOrderDetails->sevice_items_id;

                $sevice_items_details = DB::select("select service_id, category_id, title, image from sevice_items WHERE id='$sevice_items_id'");

                $ServiceOrderArray[] = [
                    'id' => $ServiceOrderDetails->id,
                    'order_id' => $ServiceOrderDetails->order_id,
                    'order_price' => $ServiceOrderDetails->order_price,
                    'payment_status' => $ServiceOrderDetails->payment_status,
                    'order_status' => $ServiceOrderDetails->order_status,
                    'order_date' => $ServiceOrderDetails->order_date,
                    'order_time' => $ServiceOrderDetails->order_time,
                    'sevice_items' => $sevice_items_details

                ];
            }
            return response()->json($ServiceOrderArray);
            // print_r($ServiceOrder);
        }
    }

    public function user_profile()
    {
        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d', strtotime('-0 day'));
        $time = date('H:i:s', strtotime('-0 day'));

        $jsonData = file_get_contents('php://input');
        $post = json_decode(file_get_contents('php://input'), TRUE);

        if ($jsonData == "") {
            $result = array(
                'resultinvalid' => 'invalid request'
            );
            return response()->json($result);
        } else {
            $user_basic_id = $post['user_id'];

            $UserInfo = DB::table('user_basic')
                ->where('user_basic.id', $user_basic_id)
                ->leftJoin('user_details', 'user_details.user_basic_id', '=', 'user_basic.id')
                ->get();

            // print_r($UserInfo);

            foreach ($UserInfo as $UserInfoData) {
                return response()->json([
                    'name' => $UserInfoData->name,
                    'email' => $UserInfoData->email,
                    'image' => $UserInfoData->image,
                    'first_name' => $UserInfoData->first_name,
                    'last_name' => $UserInfoData->last_name,
                    'birthday' => $UserInfoData->birthday,
                    'phone_number' => $UserInfoData->phone_number,
                    'gender' => $UserInfoData->gender,
                    'address' => $UserInfoData->address
                ]);
            }
        }
    }
    public function user_profile_update(Request $request)
    {
        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d', strtotime('-0 day'));
        $time = date('H:i:s', strtotime('-0 day'));

        $jsonData = file_get_contents('php://input');
        $post = json_decode(file_get_contents('php://input'), TRUE);

        if ($jsonData == "") {
            $result = array(
                'resultinvalid' => 'invalid request'
            );
            return response()->json($result);
        } else {
            $user_basic_id = $post['user_id'];
        }
    }

    public function service_requirements($service_order_id)
    {
        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d', strtotime('-0 day'));
        $time = date('H:i:s', strtotime('-0 day'));

        $ServiceOrder = DB::select("select * from service_order WHERE id='$service_order_id'");

        foreach ($ServiceOrder as $ServiceOrderData) {
            $sevice_items_id = $ServiceOrderData->sevice_items_id;
        }

        $ServiceItem = DB::select("select * from sevice_items WHERE id='$sevice_items_id'");
        foreach ($ServiceItem as $ServiceItemData) {
            $GetServiceId = $ServiceItemData->service_id;
        }

        $ServiceRequirements = DB::select("select * from service_requirements WHERE service_id='$GetServiceId'");
        $ServiceRequirementsArray = array();
        foreach ($ServiceRequirements as $ServiceRequirementsData) {
            $ServiceRequirementsArray[] = [
                'questions' => $ServiceRequirementsData->questions,
                'field_type' => $ServiceRequirementsData->field_type
            ];
        }
        return response()->json($ServiceRequirementsArray);
    }

    public function notifications()
    {
        $jsonData = file_get_contents('php://input');
        $post = json_decode(file_get_contents('php://input'), TRUE);

        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d', strtotime('-0 day'));
        $time = date('H:i:s', strtotime('-0 day'));

        if ($jsonData == "") {
            $result = array(
                'resultinvalid' => 'invalid request'
            );
            return response()->json($result);
        } else {
            $user_basic_id = $post['user_id'];
            $Notification = DB::select("select * from user_notification WHERE user_basic_id='$user_basic_id' AND status=1 ORDER BY id DESC");

            $NotificationArray = array();

            foreach ($Notification as $NotificationDetails) {

                $NotificationArray[] = [
                    'id' => $NotificationDetails->id,
                    'notification' => $NotificationDetails->notification_details,
                    'date' => $NotificationDetails->date,
                    'time' => $NotificationDetails->time,
                ];
            }
            return response()->json($NotificationArray);
            // print_r($NotificationArray);
        }
    }
    public function checkout()
    {
        $jsonData = file_get_contents('php://input');
        $post = json_decode(file_get_contents('php://input'), TRUE);

        if ($jsonData == "") {
            $result = array(
                'resultinvalid' => 'invalid request'
            );
            return response()->json($result);
        } else {
            $sevice_package_id = $post['sevice_package_id'];
            $ServicePackage = DB::select("select * from sevice_package WHERE id='$sevice_package_id'");

            foreach ($ServicePackage as $ServicePackageData) {
                $sevice_package_id = $ServicePackageData->id;
                $sevice_items_id = $ServicePackageData->sevice_items_id;

                $package_details = DB::select("select package_item from sevice_package_details WHERE sevice_package_id='$sevice_package_id'");
                $service_item = DB::select("select title, details, image from sevice_items WHERE id='$sevice_items_id'");

                $packageArray = [
                    'package_name' => $ServicePackageData->package_name,
                    'package_text' => $ServicePackageData->package_text,
                    'package_price' => $ServicePackageData->package_price,
                    'delivery_time' => $ServicePackageData->delivery_time,
                    'revision' => $ServicePackageData->revision,
                    'package_details' => $package_details,
                    'service_item' => $service_item
                ];
                return response()->json($packageArray);
            }
        }
    }

    public function user_history()
    {
    }
}
