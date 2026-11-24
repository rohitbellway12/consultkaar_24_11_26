<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabTestCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LabTestCategoryController extends Controller
{


    function index()
    {
        return view('admin-views.lab-test-category.index');
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

         $nameExist = DB::table('lab_test_categories')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }



        $fileName = Helpers::upload('category/', 'png', $request->file('image'));
    

        $category = new LabTestCategory;
        $category->name = $name;
        $category->lang_code = $langName;
        $category->image = $fileName;
        $category->save();


        $id = $category->id;


        DB::table('lab_test_categories')->where(['id' => $id,'lang_code' => $langName])->update([
            'cid' => "c".$id,
        ]); 

        
        Toastr::success('Category added successfully');

        return back();
    }

    function edit($id)
    {
        $category = LabTestCategory::find($id);
        return view('admin-views.lab-test-category.edit',compact('category'));
    }

    public function update(Request $request, $id)
    {

        $langName = $request->lang_code;
        $categoryData = LabTestCategory::find($id);
      
        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


            $nameExist = DB::table('lab_test_categories')
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
            $nameExist = DB::table('lab_test_categories')
            ->where('name', $request->name_fr)
           ->where('cid', '!=', "c".$id)
            ->exists();
          
            $name = $request->name_fr;
      

         }


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }


        $exists = DB::table('lab_test_categories')->where(['cid' => "c".$id, 'lang_code' => $langName])->exists();
        $fileName = $request->has('image') ? Helpers::update('category/', $categoryData->image, 'png', $request->file('image')) : $categoryData->image;
        if ($exists) {
            DB::table('lab_test_categories')->where(['cid' => "c".$id,'lang_code' => $langName])->update([
                'name' => $name,
                'image' => $fileName,
            ]);            

        } else {
            DB::table('lab_test_categories')->insertGetId([
                'name' => $name,
                'cid' => "c".$id,
                'image' => $fileName,
                'lang_code' => $langName,
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
        $categories = LabTestCategory::where('cid', $cid)->get();

        foreach ($categories as $category) {
            $category->delete();
        }


        Toastr::success('Category deleted successfully');
        return back();
    }

    // function list(Request $request)
    // {
    //     $categories = DB::table('lab_test_categories')
    //     ->select('*')
    //     ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, cid FROM lab_test_categories GROUP BY cid) as sub'), function ($join) {
    //         $join->on('lab_test_categories.created_at', '=', 'sub.min_created_at')
    //              ->on('lab_test_categories.cid', '=', 'sub.cid');
    //     })->orderBy('created_at','desc')->get();
    //     // Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($categories);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

    //     $categories = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    //     // Set path for pagination links
    //     $categories->withPath($request->url());
       
    //     return view('admin-views.lab-test-category.list',compact('categories'));
    // }
    
    public function list(Request $request)
{
    $searchKey = $request->input('search');

    $query = DB::table('lab_test_categories')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, cid FROM lab_test_categories GROUP BY cid) as sub'), function ($join) {
            $join->on('lab_test_categories.created_at', '=', 'sub.min_created_at')
                 ->on('lab_test_categories.cid', '=', 'sub.cid');
        });

    // Search filter apply karein
    if ($searchKey) {
        $query->where(function ($q) use ($searchKey) {
            $q->where('lab_test_categories.name', 'like', "%{$searchKey}%");
        });
    }

    // Paginate results (Manual get() hata ke paginate)
    $categories = $query->orderBy('lab_test_categories.created_at', 'desc')->paginate(10)->withQueryString();

    return view('admin-views.lab-test-category.list', compact('categories'));
}


    public function search(Request $request){
        $searchKey = $request->search;
        $categories = DB::table('lab_test_categories')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, cid FROM lab_test_categories GROUP BY cid) as sub'), function ($join) {
            $join->on('lab_test_categories.created_at', '=', 'sub.min_created_at')
                 ->on('lab_test_categories.cid', '=', 'sub.cid');
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
            'view'=>view('admin-views.lab-test-category.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
        
        
    }  

    public function status(Request $request)
    {


        $cid =  "c".$request->id;
        $categories = LabTestCategory::where('cid', $cid)->get();

        foreach ($categories as $category) {
            $category->status = $request->status;
            $category->save();
        }

        Toastr::success('Category status updated!');
        return back();
    }
}
