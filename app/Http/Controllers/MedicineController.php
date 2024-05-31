<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicineController extends Controller
{

  public function __construct()
  {
    \Config::set('auth.defaults.guard', 'userbasic');
  }

  public function medicine(Request $request)
  {

    $medicine = Medicine::all();

    if ($medicine->count() != 0) {
      return response()->json(['medicine' => $medicine]);
    } else {
      return response()->json([
        'msg' => 'No Product',
      ]);
    }
  
  }

  public function medicine_add(Request $request)
  {

      $exists_medicine = Medicine::where('medicine_name',$request->medicine_name)->count();

    if ($exists_medicine) {
      return response()->json(['error' => 'Already Add this Medicine']);
    } else {

      try {

        $medicine = Medicine::create([
          'medicine_name' => $request->medicine_name,
          'category' => $request->category,
          'brand_name' => $request->brand_name,
          'purchase_date' => $request->purchase_date,
          'price' => $request->price,
          'expired_date' => $request->expired_date,
          'stock' => $request->stock,
        ]);

      } catch (\Exception $err) {
        $medicine = null;
      }

      if ($medicine != null) {
        return response()->json(['success' => 'Save This Medicine']);
      } else {
        return response()->json([
          'msg' => 'Internal Server Error',
          'err_msg' => $err->getMessage()
        ], 500);
      }
    }
  }

  public function medicine_get_item(Request $request){

    $medicine = Medicine::where('id',$request->id)->first();

    if ($medicine != null) {
      return response()->json(['medicine' => $medicine]);
    } else {
      return response()->json([
        'msg' => 'No Product',
      ]);
    }

  }

  public function medicine_edit(Request $request)
  {

    $medicine = Medicine::find($request->id);

    if (is_null($medicine)) {
      return response()->json([
        'error' => "Do not Find any Medicine",
        'status' => 404
      ], 404);
    } else {

      $validator = Validator::make($request->all(), [
        'medicine_name' => 'required',
        'category' => 'required',
        'brand_name' => 'required',
        'purchase_date' => 'required',
        'price' => 'required',
        'expired_date' => 'required',
        'stock' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
      } else {

        try {

          $medicine->medicine_name =  $request->medicine_name;
          $medicine->category =  $request->category;
          $medicine->brand_name =  $request->brand_name;
          $medicine->purchase_date =  $request->purchase_date;
          $medicine->price =  $request->price;
          $medicine->expired_date =  $request->expired_date;
          $medicine->stock =  $request->stock;
          $medicine->save();

        } catch (\Exception $err) {
          $medicine = null;
        }

        if ($medicine != null) {
          return response()->json(['success' => 'Updated Medicine'], 200);
        } else {
          return response()->json([
            'error' => 'Internal Server Error',
            'err_msg' => $err->getMessage()
          ], 500);
        }
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
}
