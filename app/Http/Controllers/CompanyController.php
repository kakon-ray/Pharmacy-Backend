<?php

namespace App\Http\Controllers;

use App\Models\MedicineCompany;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function __construct()
    {
        \Config::set('auth.defaults.guard', 'userbasic');
    }

    public function company()
    {
        $companys = MedicineCompany::all();

        if ($companys->count() != 0) {
            return response()->json(['companys' => $companys]);
        } else {
            return response()->json([
                'msg' => 'No company',
            ]);
        }
    }

    public function company_add(Request $request)
    {
        

        $slug = Str::slug($request->company_name, '-');
        $company = MedicineCompany::where('company_slug', $slug)->count();

        if ($company) {
            return response()->json(['error' => 'Already add this company']);
        } else {

            try {

                $createCompany = MedicineCompany::create([
                    'company_name' => $request->company_name,
                    'company_slug' => $slug,
                ]);

            } catch (\Exception $err) {
                $createCompany = null;
            }
            
            if ($createCompany != null) {
                return response()->json(['success' => 'Save this company']);
            } else {
                return response()->json([
                    'msg' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }
    }

    public function company_delete(Request $request)
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

    public function company_get_item(Request $request)
    {

        $company = MedicineCompany::where('id', $request->id)->first();

        if ($company != null) {
            return response()->json(['company' => $company]);
        } else {
            return response()->json([
                'msg' => 'No Company',
                'success' => true,
            ]);
        }
    }

    public function company_edit(Request $request)
    {

        $comapny = MedicineCompany::find($request->id);

        if (is_null($comapny)) {
            return response()->json([
                'msg' => "Do not find any company",
                'success' => false
            ]);
        } else {

            if (!$request->company_name) {
                return response()->json([
                    'msg' => "Company is required",
                    'success' => false
                ]);
            } else {
         

                try {

                    $slug = Str::slug($request->company_name, '-');
                    $check_company = MedicineCompany::where('company_name', $slug)->count();
    
                    if ($check_company) {
                        return response()->json([
                            'msg' => 'Already exists this company',
                            'success' => false,
                        ]);
                    }

                    $comapny->company_name =  $request->company_name;
                    $comapny->company_slug =  $slug;
                    $comapny->save();

                } catch (\Exception $err) {
                    $comapny = null;
                }

                if ($comapny != null) {
                    return response()->json([
                        'msg' => 'Updated comapny',
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
