<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineCompany;
use App\Models\Order;
use App\Models\Product;
use Faker\Provider\Medical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MedicineController extends Controller
{

  public function __construct()
  {
    \Config::set('auth.defaults.guard', 'userbasic');
  }

  public function medicine(Request $request)
  {

    $medicine = Medicine::with('category')->with('company')->get();
    $categories = Category::all();
    $companyes = MedicineCompany::all();

    if ($medicine->count() != 0) {
      return response()->json([
        'medicine' => $medicine,
        'categories' => $categories,
        'companyes' => $companyes,
      ]);
    } else {
      return response()->json([
        'msg' => 'No Product',
      ]);
    }
  }

  public function medicine_add(Request $request)
  {

    $arrayRequest = [
      'medicine_name' => $request->medicine_name,
      'category_id' => $request->category_id,
      'company_id' => $request->company_id,
      'purchase_date' => $request->purchase_date,
      'purchase_price' => $request->purchase_price,
      'expired_date' => $request->expired_date,
      'stock' => $request->stock,
    ];

    $arrayValidate  = [
      'medicine_name' => 'required',
      'category_id' => 'required',
      'company_id' => 'required',
      'purchase_date' => 'required',
      'purchase_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
      'expired_date' => 'required',
      'stock' => 'required|integer',
    ];

    $response = Validator::make($arrayRequest, $arrayValidate);

    if ($response->fails()) {
      $msg = '';
      foreach ($response->getMessageBag()->toArray() as $item) {
        $msg = $item;
      };

      return response()->json([
        'success' => false,
        'msg' => $msg[0]
      ]);
    }


    $exists_medicine = Medicine::where('medicine_name', $request->medicine_name)->count();

    if ($exists_medicine) {
      return response()->json([
        'msg' => 'Already Add this Medicine',
        'success' => false
      ]);
    } else {

      try {

        $medicine = Medicine::create([
          'medicine_name' => $request->medicine_name,
          'category_id' => $request->category_id,
          'company_id' => $request->company_id,
          'purchase_date' => $request->purchase_date,
          'purchase_price' => $request->purchase_price,
          'selling_price' => $request->selling_price,
          'expired_date' => $request->expired_date,
          'stock' => $request->stock,
        ]);
      } catch (\Exception $err) {
        $medicine = null;
      }

      if ($medicine != null) {
        return response()->json([
          'msg' => 'Save This Medicine',
          'success' => true
        ]);
      } else {
        return response()->json([
          'msg' => 'Internal Server Error',
          'success' => false,
          'err_msg' => $err->getMessage()
        ]);
      }
    }
  }

  public function order_submit(Request $request)
  {
    $arrayRequest = [
      'category_id' => $request->category_id,
      'company_id' => $request->company_id,
      'medicine_id' => $request->medicine_id,
      'order_type' => $request->order_type,
      'quantity' => $request->quantity,
      'purchase_price' => $request->purchasePrice,
      'selling_price' => $request->sellingPrice,
      'expired_date' => $request->expired_date,
  ];

  $arrayValidate  = [
      'category_id' => 'required',
      'company_id' => 'required',
      'medicine_id' => 'required',
      'order_type' => 'required',
      'quantity' => 'required|integer',
      'purchase_price' => 'numeric|regex:/^\d+(\.\d{1,2})?$/',
      'selling_price' => 'numeric|regex:/^\d+(\.\d{1,2})?$/',
  ];

  $response = Validator::make($arrayRequest, $arrayValidate);

  if ($response->fails()) {
      $msg = '';
      foreach ($response->getMessageBag()->toArray() as $item) {
          $msg = $item;
      };

      return response()->json([
          'success' => false,
          'msg' => $msg[0]
      ]);
  }

    $exists_medicine = Medicine::where('id', $request->medicine_id)->count();

    if ($exists_medicine) {

      try {
        DB::beginTransaction();

        $ordersubmit = Order::create([
          'category_id' => $request->category_id,
          'company_id' => $request->company_id,
          'medicine_id' => $request->medicine_id,
          'order_type' => $request->order_type,
          'quantity' => $request->quantity,
          'purchase_price' => $request->purchasePrice,
          'selling_price' => $request->sellingPrice,
          'expired_date' => $request->expired_date,
        ]);

        if ($ordersubmit) {
          $medicine = Medicine::find($request->medicine_id);
          $medicine->selling_price = $request->totalSellingPrice;
          $medicine->purchase_price = $request->totalPurchasePrice;
          $medicine->stock = $request->totalQuantity;
          $medicine->save();
        }

        DB::commit();
      } catch (\Exception $err) {
        $medicine = null;
      }

      if ($medicine != null) {
        return response()->json([
          'msg' => 'Order Completed',
          'success' => true
        ]);
      } else {
        return response()->json([
          'msg' => 'Internal Server Error',
          'success' => false,
          'err_msg' => $err->getMessage()
        ]);
      }
    } else {

      return response()->json([
        'msg' => 'Do not found any medicine',
        'success' => false
      ]);
    }
  }



  public function order_get(Request $request)
  {
    $orders = Order::orderBy('created_at', 'desc')->with('medicine')->with('category')->with('company')->get();

    if ($orders != null) {
      return response()->json([
        'orders' => $orders,
      ]);
    } else {
      return response()->json([
        'msg' => 'No Orders',
      ]);
    }
  }

  public function dashboard(Request $request)
  {
    $medicineCount = Medicine::count();
    $brandCount = MedicineCompany::count();
    $categoryCount = Category::count();
    $orderCount = Order::count();
    $sellOrderCount = Order::where('order_type', 'sell')->count();
    $purchaseOrderCount = Order::where('order_type', 'buy')->count();

    return response()->json([
      'medicineCount' => $medicineCount,
      'orderCount' => $orderCount,
      'sellOrderCount' => $sellOrderCount,
      'purchaseOrderCount' => $purchaseOrderCount,
      'categoryCount' => $categoryCount,
      'brandCount' => $brandCount,
    ]);
  }


  public function medicine_get_item(Request $request)
  {

    $medicine = Medicine::where('id', $request->id)->first();
    $categories = Category::all();
    $companyes = MedicineCompany::all();

    if ($medicine != null) {
      return response()->json([
        'medicine' => $medicine,
        'categories' => $categories,
        'companyes' => $companyes,
      ]);
    } else {
      return response()->json([
        'msg' => 'No Product',
      ]);
    }
  }

  public function medicine_edit(Request $request)
  {


    $arrayRequest = [
      'medicine_name' => $request->medicine_name,
      'category_id' => $request->category_id,
      'company_id' => $request->company_id,
      'purchase_date' => $request->purchase_date,
      'purchase_price' => $request->purchase_price,
      'expired_date' => $request->expired_date,
      'stock' => $request->stock,
    ];

    $arrayValidate  = [
      'medicine_name' => 'required',
      'category_id' => 'required',
      'company_id' => 'required',
      'purchase_date' => 'required',
      'purchase_price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
      'expired_date' => 'required',
      'stock' => 'required|integer',
    ];

    $response = Validator::make($arrayRequest, $arrayValidate);

    if ($response->fails()) {
      $msg = '';
      foreach ($response->getMessageBag()->toArray() as $item) {
        $msg = $item;
      };

      return response()->json([
        'success' => false,
        'msg' => $msg[0]
      ]);
    }

    $medicine = Medicine::find($request->id);

    if (is_null($medicine)) {
      return response()->json([
        'success' => false,
        'msg' => 'Do not Find any Medicine'
      ]);
    } else {

      try {

        $medicine->medicine_name =  $request->medicine_name;
        $medicine->category_id =  $request->category_id;
        $medicine->company_id =  $request->company_id;
        $medicine->purchase_date =  $request->purchase_date;
        $medicine->purchase_price =  $request->purchase_price;
        $medicine->selling_price =  $request->selling_price;
        $medicine->expired_date =  $request->expired_date;
        $medicine->stock =  $request->stock;
        $medicine->save();
      } catch (\Exception $err) {
        $medicine = null;
      }

      if ($medicine != null) {
        return response()->json([
          'success' => true,
          'msg' => 'Updated Medicine'
        ]);
      } else {
        return response()->json([
          'success' => false,
          'msg' => 'Internal Server Error'
        ]);
      }
    }
  }


  public function medicine_delete(Request $request)
  {
    $medicine = Medicine::find($request->id);

    if (is_null($medicine)) {
      return response()->json([
        'error' => "Do not Find any Medicine",
        'status' => 404
      ], 404);
    } else {

      try {

        $medicine->delete();
      } catch (\Exception $err) {
        $medicine = null;
      }

      if ($medicine != null) {
        return response()->json(['success' => 'Deleted This Medicine']);
      } else {
        return response()->json([
          'error' => 'Internal Server Error',
          'err_msg' => $err->getMessage()
        ], 500);
      }
    }
  }


  public function get_company_category(Request $request)
  {

    $categories = Category::all();
    $companyes = MedicineCompany::all();

    if (!is_null($categories) || is_null($companyes)) {
      return response()->json([
        'categories' => $categories,
        'companyes' => $companyes,
      ]);
    } else {
      return response()->json([
        'msg' => 'No Category and Company',
      ]);
    }
  }
}
