<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

  public function __construct()
  {
    \Config::set('auth.defaults.guard', 'userbasic');
  }

  public function product(Request $request)
  {

    $all_product = Product::all();
    if ($all_product->count() != 0) {
      return response()->json(['product' => $all_product], 200);
    } else {
      return response()->json([
        'msg' => 'No Product',
      ]);
    }
  }
  public function product_add(Request $request)
  {

    // return response()->json([
    //   'response' =>  $request->name,
    // ]);

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'number' => 'required',
      'desc' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    } else {

      try {

        $product = Product::create([
          'name' => $request->name,
          'number' => $request->number,
          'desc' => $request->desc,

        ]);
      } catch (\Exception $err) {
        $product = null;
      }

      if ($product != null) {
        return response()->json(['success' => 'Product Upload Completed'], 200);
      } else {
        return response()->json([
          'error' => 'Internal Server Error',
          'err_msg' => $err->getMessage()
        ], 500);
      }
    }
  }
  public function product_edit(Request $request)
  {

    $product = Product::find($request->id);

    if (is_null($product)) {
      return response()->json([
        'msg' => "Do not Find any Product",
        'status' => 404
      ], 404);
    } else {

      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'number' => 'required',
        'desc' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
      } else {

        try {

          $product->name =  $request->name;
          $product->number =  $request->number;
          $product->desc =  $request->desc;
          $product->save();
        } catch (\Exception $err) {
          $product = null;
        }

        if ($product != null) {
          return response()->json(['success' => 'Product Upload Completed'], 200);
        } else {
          return response()->json([
            'error' => 'Internal Server Error',
            'err_msg' => $err->getMessage()
          ], 500);
        }
      }
    }
  }

  public function product_delete(Request $request)
  {
    $product = Product::find($request->id);

    if (is_null($product)) {
      return response()->json([
        'msg' => "Do not Find any Product",
        'status' => 404
      ], 404);
    } else {

      try {

        $product->delete();
      } catch (\Exception $err) {
        $product = null;
      }

      if ($product != null) {
        return response()->json(['success' => 'Deleted This Product'], 200);
      } else {
        return response()->json([
          'error' => 'Internal Server Error',
          'err_msg' => $err->getMessage()
        ], 500);
      }
    }
  }
}
