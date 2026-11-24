<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LabController extends Controller
{


    function index()
    {
     
        return view('admin-views.lab.index');
    }

    public function store(Request $request)
    {
        $langName = $request->lang_code;

        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
                'email'   => 'required|regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,4}$/|unique:labs',
            ], [
                'name_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
            $name = $request->name_en;
            $description = $request->description_en;
       


        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
                'email'   => 'required|regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,4}$/|unique:labs',
            ], [
                'name_fr.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
          

            $name = $request->name_fr;
            $description = $request->description_fr;


         }

         $nameExist = DB::table('labs')->where('name', $name)->exists();


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }




    

        $Lab = new Lab;
        $Lab->name = $name;
        $Lab->description = $description;
        $Lab->email = $request->email;
        $Lab->password = bcrypt($request->password);
        $Lab->address = $request->address;
        $Lab->lang_code = $langName;
        $Lab->save();


        $id = $Lab->id;


        DB::table('labs')->where(['id' => $id,'lang_code' => $langName])->update([
            'lid' => "l".$id,
        ]); 

        
        Toastr::success('Lab added successfully');

        return back();
    }

    function edit($id)
    {
        $lab = Lab::find($id);
        return view('admin-views.lab.edit',compact('lab'));
    }

    public function update(Request $request, $id)
{
    $langName = $request->lang_code;
    $labData = Lab::where('lid', "l" . $id)->first();

    if (!$labData) {
        Toastr::error('Lab not found!');
        return back();
    }

    // Validation
    if ($langName == 'en') {
        $validator = Validator::make($request->all(), [
            'name_en' => 'required',
        ], [
            'name_en.required' => "Name is required!",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $nameExist = DB::table('labs')
            ->where('name', $request->name_en)
            ->where('lid', '!=', "l" . $id)
            ->exists();

        $name = $request->name_en;
        $description = $request->description_en;
    } else {
        $validator = Validator::make($request->all(), [
            'name_fr' => 'required',
        ], [
            'name_fr.required' => "Name is required!",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $nameExist = DB::table('labs')
            ->where('name', $request->name_fr)
            ->where('lid', '!=', "l" . $id)
            ->exists();

        $name = $request->name_fr;
        $description = $request->description_fr;
    }

    if ($nameExist) {
        Toastr::error('Name already exists');
        return back();
    }

    // **Ensure All Fields Are Updated Correctly**
    $updateData = [
        'name' => $name,
        'description' => $description,
        'updated_at' => now(),
    ];

    // Check and update optional fields
    if ($request->filled('email')) {
        $updateData['email'] = $request->email;
    }
    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }
    if ($request->filled('address')) {
        $updateData['address'] = $request->address;
    }

    // Check if the language-specific record exists
    $exists = DB::table('labs')->where(['lid' => "l" . $id, 'lang_code' => $langName])->exists();

    if ($exists) {
        // **Ensure all fields update properly**
        $updated = DB::table('labs')
            ->where(['lid' => "l" . $id, 'lang_code' => $langName])
            ->update($updateData);

        if ($updated) {
            Toastr::success('Lab updated successfully');
        } else {
            Toastr::warning('No changes detected');
        }
    } else {
        // Insert new record if it doesn't exist
        $updateData['lid'] = "l" . $id;
        $updateData['lang_code'] = $langName;
        $updateData['created_at'] = now();

        DB::table('labs')->insert($updateData);
        Toastr::success('Lab added successfully');
    }

    return back();
}



    public function delete(Request $request)
    {

        $lid =  "l".$request->id;
        $categories = Lab::where('lid', $lid)->get();

        foreach ($categories as $Lab) {
            $Lab->delete();
        }


        Toastr::success('Lab deleted successfully');
        return back();
    }

    function list(Request $request)
    {
        $labs = DB::table('labs')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, lid FROM labs GROUP BY lid) as sub'), function ($join) {
            $join->on('labs.created_at', '=', 'sub.min_created_at')
                 ->on('labs.lid', '=', 'sub.lid');
        })->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($labs);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $labs = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $labs->withPath($request->url());
        return view('admin-views.lab.list',compact('labs'));
    }

    public function search(Request $request){
        $searchKey = $request->search;
        $labs = DB::table('labs')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, lid FROM labs GROUP BY lid) as sub'), function ($join) {
            $join->on('labs.created_at', '=', 'sub.min_created_at')
                 ->on('labs.lid', '=', 'sub.lid');
        })->orderBy('created_at','desc')->where('name', 'like', "%$searchKey%")->get();




        
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($labs);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $labs = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $labs->withPath($request->url());

        return response()->json([
            'view'=>view('admin-views.lab.partials._table',compact('labs'))->render(),
            'count'=>$labs->count()
        ]);
        
        
    }  

    public function status(Request $request)
    {


        $lid =  "l".$request->id;
        $categories = Lab::where('lid', $lid)->get();

        foreach ($categories as $Lab) {
            $Lab->status = $request->status;
            $Lab->save();
        }

        Toastr::success('Lab status updated!');
        return back();
    }
}
