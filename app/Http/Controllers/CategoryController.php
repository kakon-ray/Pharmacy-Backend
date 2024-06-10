<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        \Config::set('auth.defaults.guard', 'userbasic');
    }

    public function category()
    {
        $category = Category::all();

        if ($category->count() != 0) {
            return response()->json(['categories' => $category]);
        } else {
            return response()->json([
                'msg' => 'No Product',
            ]);
        }
    }

    public function category_add(Request $request)
    {

        $slug = Str::slug($request->category_name, '-');
        $category = Category::where('category_slug', $slug)->count();

        if ($category) {
            return response()->json(['error' => 'Already Add this Category']);
        } else {

            try {

                $category = Category::create([
                    'category_name' => $request->category_name,
                    'category_slug' => $slug,
                ]);

            } catch (\Exception $err) {
                $category = null;
            }
            
            if ($category != null) {
                return response()->json(['success' => 'Save This Category']);
            } else {
                return response()->json([
                    'msg' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }
    }

    public function category_delete(Request $request)
    {
        $category = Category::find($request->id);

        if (is_null($category)) {
            return response()->json([
                'error' => "Do not find any category",
                'status' => 404
            ], 404);
        } else {

            try {

                $category->delete();
            } catch (\Exception $err) {
                $category = null;
            }

            if ($category != null) {
                return response()->json(['success' => 'Deleted this category']);
            } else {
                return response()->json([
                    'error' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }
    }

    public function category_get_item(Request $request)
    {

        $category = Category::where('id', $request->id)->first();

        if ($category != null) {
            return response()->json(['category' => $category]);
        } else {
            return response()->json([
                'msg' => 'No Category',
            ]);
        }
    }

    public function category_edit(Request $request)
    {

        $categorys = Category::find($request->id);

        if (is_null($categorys)) {
            return response()->json([
                'msg' => "Do not find any category",
                'success' => false
            ]);
        } else {

            $validator = Validator::make($request->all(), [
                'category_name' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            } else {
         

                try {

                    $slug = Str::slug($request->category_name, '-');
                    $check_category = Category::where('category_slug', $slug)->count();
    
                    if ($check_category) {
                        return response()->json([
                            'msg' => 'Already exists this category',
                            'success' => false,
                        ]);
                    }

                    $categorys->category_name =  $request->category_name;
                    $categorys->category_slug =  $slug;
                    $categorys->save();

                } catch (\Exception $err) {
                    $categorys = null;
                }

                if ($categorys != null) {
                    return response()->json([
                        'msg' => 'Updated category',
                        'success' => true,
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
    }
}
