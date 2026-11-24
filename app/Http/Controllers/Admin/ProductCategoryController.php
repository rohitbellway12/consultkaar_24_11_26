<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductCategoryController extends Controller
{


    function index()
    {
        return view('admin-views.product-category.index');
    }

    public function store(Request $request)
    {
        $langName = $request->lang_code;

        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


         

            $name = $request->name_en;
       


        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
            ], [
                'name_fr.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
          

            $name = $request->name_fr;


         }

         $nameExist = DB::table('product_categories')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }

        $fileName = Helpers::upload('category/', 'png', $request->file('image'));



    

        $category = new ProductCategory;
        $category->name = $name;
        $category->lang_code = $langName;
        $category->image = $fileName;
        $category->save();


        $id = $category->id;


        DB::table('product_categories')->where(['id' => $id,'lang_code' => $langName])->update([
            'cid' => "c".$id,
        ]); 

        
        Toastr::success('Category added successfully');

        return back();
    }

    function edit($id)
    {
        $category = ProductCategory::find($id);
        return view('admin-views.product-category.edit',compact('category'));
    }

    public function update(Request $request, $id)
    {

        $langName = $request->lang_code;
        $categoryData = ProductCategory::find($id);
      
        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


            $nameExist = DB::table('product_categories')
            ->where('name', $request->name_en)
            ->where('cid', '!=', "c".$id)
            ->exists();

            $name = $request->name_en;


       


        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
            ], [
                'name_fr.required'=> "Name  is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
            $nameExist = DB::table('product_categories')
            ->where('name', $request->name_fr)
           ->where('cid', '!=', "c".$id)
            ->exists();
          
            $name = $request->name_fr;
           

         }


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }
        $fileName = $request->has('image') ? Helpers::update('category/', $categoryData->image, 'png', $request->file('image')) : $categoryData->image;

        $exists = DB::table('product_categories')->where(['cid' => "c".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('product_categories')->where(['cid' => "c".$id,'lang_code' => $langName])->update([
                'name' => $name,
                'image' => $fileName,
            ]);            

        } else {
            DB::table('product_categories')->insertGetId([
                'name' => $name,
                'cid' => "c".$id,
                'lang_code' => $langName,
                'image' => $fileName,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }


        
        Toastr::success('Category updated successfully');

        return back();
    }

    public function delete(Request $request)
    {

        $cid =  "c".$request->id;
        $categories = ProductCategory::where('cid', $cid)->get();

        foreach ($categories as $category) {
            $category->delete();
        }


        Toastr::success('Category deleted successfully');
        return back();
    }

    function list(Request $request)
    {
        $categories = DB::table('product_categories')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, cid FROM product_categories GROUP BY cid) as sub'), function ($join) {
            $join->on('product_categories.created_at', '=', 'sub.min_created_at')
                 ->on('product_categories.cid', '=', 'sub.cid');
        })->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($categories);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $categories = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $categories->withPath($request->url());
        return view('admin-views.product-category.list',compact('categories'));
    }

    public function search(Request $request){
        $searchKey =  $request['search'];

        $categories = DB::table('product_categories')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, cid FROM product_categories GROUP BY cid) as sub'), function ($join) {
            $join->on('product_categories.created_at', '=', 'sub.min_created_at')
                 ->on('product_categories.cid', '=', 'sub.cid');
        })->orderBy('created_at','desc')->where('name', 'like', "%$searchKey%")->get();

   



        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($categories);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $categories = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $categories->withPath($request->url());

      


        return response()->json([
            'view'=>view('admin-views.product-category.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
        
        
    }  

    public function status(Request $request)
    {


        $cid =  "c".$request->id;
        $categories = ProductCategory::where('cid', $cid)->get();

        foreach ($categories as $category) {
            $category->status = $request->status;
            $category->save();
        }

        Toastr::success('Category status updated!');
        return back();
    }
}
