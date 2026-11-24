<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //

     /**
     * get categories
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){
         try {            
            
            // get category data 
             
             $categories   = Category::where(['status' => 1])->get();
             
             if (count($categories) > 0) {
                 foreach($categories as $key => $value){
                    $categoryImagePath = (env('APP_ENV') == 'local') ? asset('storage/category/' . $value->image) : asset('storage/app/public/category/' . $value->image); 
                    $value->image = $categoryImagePath;
                 } 
                 return response()->json(['status' => 'success', 'code' => 200,'data' => $categories, 'message' => 'Data found']);
             } else {
                 return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found'], 404);
             }
         } catch (\Exception $e) {
             // Handle exceptions, if any
             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
         }
    }
}
