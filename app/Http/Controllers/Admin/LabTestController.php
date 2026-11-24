<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabTest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LabTestController extends Controller
{


    function index()
    {
        $categories =  DB::table('lab_test_categories')->where('lang_code','en')->get();  
        $french_categories =  DB::table('lab_test_categories')->where('lang_code','fr')->get();  
        return view('admin-views.lab-test.index', compact('categories','french_categories'));
    }

    public function store(Request $request)
    {
        $langName = $request->lang_code;

        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
                 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ], [
                'name_en.required'=> "Name is required!",
                'image.image' => 'The file must be an image.',
                'image.mimes' => 'Only jpeg, png, jpg, and gif formats are allowed.',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


         

            $name = $request->name_en;
            $categoryId = $request->category_id_en;
            $description = $request->description_en;
            $imageInputName = 'image';
       


        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
                'image_fr' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ], [
                'name_fr.required' => "Name is required!",
                'image_fr.image' => 'The file must be an image.',
                'image_fr.mimes' => 'Only jpeg, png, jpg, and gif formats are allowed.',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
          

            $name = $request->name_fr;
            $categoryId = $request->category_id_fr;
            $description = $request->description_fr;
            $imageInputName = 'image_fr';


         }

         $nameExist = DB::table('lab_test')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }


        // Image upload handling
    $imagePath = null;
    if ($request->hasFile($imageInputName)) {
        $image = $request->file($imageInputName);
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('public/lab_tests', $imageName);
    }

    

        $LabTest = new LabTest;
        $LabTest->name = $name;
        $LabTest->category_id = $categoryId;
        $LabTest->description = $description;
        $LabTest->price = $request->price;
        $LabTest->lang_code = $langName;
         $LabTest->image = $imagePath;
        $LabTest->save();


        $id = $LabTest->id;


        DB::table('lab_test')->where(['id' => $id,'lang_code' => $langName])->update([
            'ltid' => "lt".$id,
        ]); 

        
        Toastr::success('Lab Test added successfully');

        return back();
    }

    function edit($id)
    {
        $labTest = LabTest::find($id);
        $categories =  DB::table('lab_test_categories')->where('lang_code','en')->get();  
        $french_categories =  DB::table('lab_test_categories')->where('lang_code','fr')->get();  
        return view('admin-views.lab-test.edit',compact('labTest','categories','french_categories'));
    }

    public function update(Request $request, $id)
    {

        $langName = $request->lang_code;
        $LabTestData = LabTest::find($id);
      
        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
                'image_en' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ], [
                'name_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


            $nameExist = DB::table('lab_test')
            ->where('name', $request->name_en)
            ->where('ltid', '!=', "lt".$id)
            ->exists();

            $name = $request->name_en;
            $categoryId = $request->category_id_en;
            $description = $request->description_en;
            $imageFile = $request->file('image_en');




        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
                 'image_fr' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ], [
                'name_fr.required'=> "Name  is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
            $nameExist = DB::table('lab_test')
            ->where('name', $request->name_fr)
           ->where('ltid', '!=', "lt".$id)
            ->exists();
          
            $name = $request->name_fr;
            $categoryId = $request->category_id_fr;
            $description = $request->description_fr;
            $imageFile = $request->file('image_fr');
      

         }


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }
        
        // Handle image upload if a new file is uploaded
        if ($imageFile) {
            $imagePath = $imageFile->store('lab_tests', 'public'); // Store in storage/app/public/lab_tests
        } else {
            // Keep existing image if no new image is uploaded
            $existingRecord = DB::table('lab_test')->where(['ltid' => "lt".$id, 'lang_code' => $langName])->first();
            $imagePath = $existingRecord ? $existingRecord->image : null;
        }


        $exists = DB::table('lab_test')->where(['ltid' => "lt".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('lab_test')->where(['ltid' => "lt".$id,'lang_code' => $langName])->update([
                'name' => $name,
                'category_id' => $categoryId,
                'description' => $description,
                'price' => $request->price,
                'image' => $imagePath,
                'updated_at' => now(),
            ]);            

        } else {
            DB::table('lab_test')->insertGetId([
                'name' => $name,
                'category_id' => $categoryId,
                'description' => $description,
                'price' => $request->price,
                'ltid' => "lt".$id,
                'lang_code' => $langName,
                'image' => $imagePath,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }


        
        Toastr::success('Lab Test updated successfully');

        return back();
    }

    public function delete(Request $request)
    {

        $ltid =  "lt".$request->id;
        $categories = LabTest::where('ltid', $ltid)->get();

        foreach ($categories as $LabTest) {
            $LabTest->delete();
        }


        Toastr::success('Lab Test deleted successfully');
        return back();
    }

    function list(Request $request)
    {
        $lab_tests = DB::table('lab_test')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, ltid FROM lab_test GROUP BY ltid) as sub'), function ($join) {
            $join->on('lab_test.created_at', '=', 'sub.min_created_at')
                 ->on('lab_test.ltid', '=', 'sub.ltid');
        })->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($lab_tests);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $lab_tests = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $lab_tests->withPath($request->url());
        return view('admin-views.lab-test.list',compact('lab_tests'));
    }

    public function search(Request $request){
        $searchKey = $request->search;

        $categories = DB::table('lab_test')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, ltid FROM lab_test GROUP BY ltid) as sub'), function ($join) {
            $join->on('lab_test.created_at', '=', 'sub.min_created_at')
                 ->on('lab_test.ltid', '=', 'sub.ltid');
        })->where('name', 'like', "%$searchKey%")->orderBy('created_at','desc')->get();


        
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($categories);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $categories = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $categories->withPath($request->url());

        return response()->json([
            'view'=>view('admin-views.lab-test.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
        
        
    }  

    public function status(Request $request)
    {


        $ltid =  "lt".$request->id;
        $categories = LabTest::where('ltid', $ltid)->get();

        foreach ($categories as $LabTest) {
            $LabTest->status = $request->status;
            $LabTest->save();
        }

        Toastr::success('Lab Test status updated!');
        return back();
    }


}