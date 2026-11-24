<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Product;

class ProductCategoryController extends Controller
{
    //

     /**
     * get categories
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    // public function index(Request $request){
    //      try {            
            
    //         // get category data 

    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = $user->id;

    //         $userData = User::find($userId);
    //         $langCode = $userData->lang_code;
             
    //          $categories   = ProductCategory::where(['status' => 1,'lang_code' => $langCode])->get();
             
    //          if (count($categories) > 0) {
    //              foreach($categories as $key => $value){
    //                 $categoryImagePath = (env('APP_ENV') == 'local') ? asset('storage/category/' . $value->image) : asset('storage/app/public/category/' . $value->image); 
    //                 $value->image = $categoryImagePath;
    //              } 
    //              return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found','data' => $categories]);
    //          } else {
    //              return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found','data' => []], 200);
    //          }
    //      } catch (\Exception $e) {
    //          // Handle exceptions, if any
    //          return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => []], 500);
    //      }
    // }
 public function index(Request $request)
{
    try {
        $userId = $request->get('vendor_id');

        if ($userId) {
            $request->validate([
                'vendor_id' => 'exists:users,id'
            ]);

            $userData = User::find($userId);

            // Check if user is vendor
            if ($userData->role_id != 4) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Access denied. Only vendors are allowed.',
                    'data' => []
                ]);
            }

            $langCode = $userData->lang_code;

            $categoryIds = Product::where('user_id', $userId)
                ->pluck('category_id')
                ->unique()
                ->toArray();

            if (empty($categoryIds)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 200,
                    'message' => 'No data found',
                    'data' => []
                ]);
            }

            $categories = ProductCategory::whereIn('id', $categoryIds)
                ->where('status', 1)
                ->where('lang_code', $langCode)
                ->get();
        } else {
            // No vendor_id given, return all active categories
            $categories = ProductCategory::where('status', 1)->get();
        }

        foreach ($categories as $category) {
            $category->image = (env('APP_ENV') === 'local')
                ? asset('storage/category/' . $category->image)
                : asset('storage/app/public/category/' . $category->image);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data found',
            'data' => $categories
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'code' => 500,
            'message' => $e->getMessage(),
            'data' => []
        ], 500);
    }
}



}
