<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductTypeController extends Controller
{


    function index()
    {
        
        return view('admin-views.product-type.index');
    }

    public function store(Request $request)
    {
        // dd($request->all());
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
            $unit = $request->unit_en;
       


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
 $unit = $request->unit_fr;

         }

         $nameExist = DB::table('product_types')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }




    

        $category = new ProductType;
        $category->name = $name;
        $category->unit=  $unit;
        $category->lang_code = $langName;
        $category->save();


        $id = $category->id;


        DB::table('product_types')->where(['id' => $id,'lang_code' => $langName])->update([
            'ptid' => "pt".$id,
        ]); 

        
        Toastr::success('Product Type added successfully');

        return back();
    }

    function edit($id)
    {
        $category = ProductType::find($id);
        return view('admin-views.product-type.edit',compact('category'));
    }

    public function update(Request $request, $id)
    {
// dd($request->all());
        $langName = $request->lang_code;
        $categoryData = ProductType::find($id);
      
        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


            $nameExist = DB::table('product_types')
            ->where('name', $request->name_en)
            ->where('ptid', '!=', "pt".$id)
            ->exists();

            $name = $request->name_en;
            $unit= $request->unit_en;

            $fileName = $request->has('image_en') ? Helpers::update('speciality/', $categoryData->image, 'png', $request->file('image_en')) : $categoryData->image;


        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
            ], [
                'name_fr.required'=> "Name  is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
            $nameExist = DB::table('product_types')
            ->where('name', $request->name_fr)
           ->where('ptid', '!=', "pt".$id)
            ->exists();
          
            $name = $request->name_fr;
            $unit = $request->unit_fr;
         }


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }


        $exists = DB::table('product_types')->where(['ptid' => "pt".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('product_types')->where(['ptid' => "pt".$id,'lang_code' => $langName])->update([
                'name' => $name,
                'unit'=> $unit,
            ]);            

        } else {
            DB::table('product_types')->insertGetId([
                'name' => $name,
                'unit'=> $unit,
                'ptid' => "pt".$id,
                'lang_code' => $langName,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }


        
        Toastr::success('Product Type updated successfully');

        return back();
    }

    public function delete(Request $request)
    {

        $ptid =  "pt".$request->id;
        $categories = ProductType::where('ptid', $ptid)->get();

        foreach ($categories as $category) {
            $category->delete();
        }


        Toastr::success('Product Type deleted successfully');
        return back();
    }

    // function list(Request $request)
    // {
    //     $categories = DB::table('product_types')
    //     ->select('*')
    //     ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, ptid FROM product_types GROUP BY ptid) as sub'), function ($join) {
    //         $join->on('product_types.created_at', '=', 'sub.min_created_at')
    //              ->on('product_types.ptid', '=', 'sub.ptid');
    //     })->orderBy('created_at','desc')->get();
    //     // Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($categories);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

    //     $categories = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    //     // Set path for pagination links
    //     $categories->withPath($request->url());
    //     return view('admin-views.product-type.list',compact('categories'));
    // }

public function list(Request $request)
{
    $searchKey = $request->input('search');

    // Start building the query
    $query = DB::table('product_types')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, ptid FROM product_types GROUP BY ptid) as sub'), function ($join) {
            $join->on('product_types.created_at', '=', 'sub.min_created_at')
                 ->on('product_types.ptid', '=', 'sub.ptid');
        });

    // Filter only by 'name' field if search exists
    if ($searchKey) {
        $query->where('product_types.name', 'like', "%{$searchKey}%");
    }

    // Order and paginate with query string
    $categories = $query->orderBy('product_types.created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();

    return view('admin-views.product-type.list', compact('categories'));
}




    public function search(Request $request){
        $searchKey =  $request['search'];

        $categories = DB::table('product_types')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, ptid FROM product_types GROUP BY ptid) as sub'), function ($join) {
            $join->on('product_types.created_at', '=', 'sub.min_created_at')
                 ->on('product_types.ptid', '=', 'sub.ptid');
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
            'view'=>view('admin-views.product-type.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
        
        
    }  

    public function status(Request $request)
    {


        $ptid =  "pt".$request->id;
        $categories = ProductType::where('ptid', $ptid)->get();

        foreach ($categories as $category) {
            $category->status = $request->status;
            $category->save();
        }

        Toastr::success('Product Type status updated!');
        return back();
    }
}
