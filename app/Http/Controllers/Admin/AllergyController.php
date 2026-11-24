<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Allergy;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;



class AllergyController extends Controller
{
    // function index(Request $request)
    // {
    //     $allergies = DB::table('allergies')
    //     ->select('*')
    //     ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, aid FROM allergies GROUP BY aid) as sub'), function ($join) {
    //         $join->on('allergies.created_at', '=', 'sub.min_created_at')
    //              ->on('allergies.aid', '=', 'sub.aid');
    //     })->orderBy('created_at','desc')->get();
    //     // Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($allergies);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

    //     $allergies = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    //     // Set path for pagination links
    //     $allergies->withPath($request->url());
    //     return view('admin-views.allergy.index', compact('allergies'));
    // }
    
    public function index(Request $request)
{
    $searchKey = $request->input('search');

    $query = DB::table('allergies')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, aid FROM allergies GROUP BY aid) as sub'), function ($join) {
            $join->on('allergies.created_at', '=', 'sub.min_created_at')
                 ->on('allergies.aid', '=', 'sub.aid');
        });

    // Apply search filter
    if ($searchKey) {
        $query->where('allergies.name', 'like', "%{$searchKey}%");
    }

    // Paginate the result and keep query string for pagination links
    $allergies = $query->orderBy('allergies.created_at', 'desc')->paginate(10)->withQueryString();

    return view('admin-views.allergy.index', compact('allergies'));
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

         $nameExist = DB::table('allergies')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }

        

        $allergy = new Allergy;
        $allergy->name = $name;
        $allergy->lang_code = $langName;
        $allergy->save();
        $id = $allergy->id;


        DB::table('allergies')->where(['id' => $id,'lang_code' => $langName])->update([
            'aid' => "a".$id,
        ]); 
 
        return response()->json([], 200);
    }

    public function edit($id)
    {
     
        $allergy = Allergy::find($id);     

        return view('admin-views.allergy.edit', compact('allergy'));
    }

    public function status(Request $request)
    {
        $allergy = Allergy::find($request->id);

        $aid =  "a".$request->id;
        $allergies = Allergy::where('aid', $aid)->get();

        foreach ($allergies as $allergy) {
            $allergy->status = $request->status;
            $allergy->save();
        }

       
        
        Toastr::success(trans('Allergy status updated successully'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $langName = $request->lang_code;

        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required'=> "Name  is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


            $nameExist = DB::table('allergies')
            ->where('name', $request->name_en)
            ->where('aid', '!=', "a".$id)
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
            $nameExist = DB::table('allergies')
            ->where('name', $request->name_fr)
           ->where('aid', '!=', "a".$id)
            ->exists();

            $name = $request->name_fr;

         }


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }


        $exists = DB::table('allergies')->where(['aid' => "a".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('allergies')->where(['aid' => "a".$id,'lang_code' => $langName])->update([
                'name' => $name
            ]);            

        } else {
            DB::table('allergies')->insertGetId([
                'name' => $name,
                'aid' => "a".$id,
                'lang_code' => $langName,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }

            
        Toastr::success(trans('Allergy updated successully'));
        return redirect('admin/allergy/add-new');
    }

    public function delete($id)
    {
        $aid =  "a".$id;
        $allergies = Allergy::where('aid', $aid)->get();
        foreach ($allergies as $allergy) {
            $allergy->delete();
        }

        Toastr::success(trans('Allergy deleted successully'));
        return back();
    }

    public function search(Request $request){
        $key = $request['search'];        
        $allergies = Allergy::select('*') ->where('name', 'like', "%$key%")->get();
        return response()->json([
            'view'=>view('admin-views.allergy.partials._table',compact('allergies'))->render(),
            'count'=>$allergies->count()
        ]);
    }
}
