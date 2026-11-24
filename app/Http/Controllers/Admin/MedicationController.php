<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Medication;
use Brian2694\Toastr\Facades\Toastr;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicationController extends Controller
{
    // function index(Request $request)
    // {
    //     $medications = DB::table('medications')
    //         ->select('*')
    //         ->whereIn('id', function ($query) {
    //             $query->select(DB::raw('MIN(id)'))
    //                 ->from('medications')
    //                 ->groupBy('mid');
    //         })
    //     ->orderBy('id','desc')->get();
        
    //     // Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($medications);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

    //     $medications = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    //     // Set path for pagination links
    //     $medications->withPath($request->url());
    //     return view('admin-views.medication.index', compact('medications'));
    // }
    
    public function index(Request $request)
{
    $searchKey = $request->input('search');

    $query = DB::table('medications')
        ->select('*')
        ->whereIn('id', function ($subQuery) {
            $subQuery->select(DB::raw('MIN(id)'))
                ->from('medications')
                ->groupBy('mid');
        });

    // Apply search filter
    if ($searchKey) {
        $query->where('name', 'like', "%{$searchKey}%");
    }

   $medications = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();


    return view('admin-views.medication.index', compact('medications'));
}


    public function store(Request $request){        
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

         $nameExist = DB::table('medications')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }

        

        $medication = new Medication;
        $medication->name = $name;
        $medication->lang_code = $langName;
        $medication->save();
        $id = $medication->id;


        DB::table('medications')->where(['id' => $id,'lang_code' => $langName])->update([
            'mid' => "m".$id,
        ]); 
        return response()->json([], 200);
    }

    public function edit($id)
    {
        $medication = Medication::find($id);
        return view('admin-views.medication.edit', compact('medication'));
    }

    public function status(Request $request)
    {
        $medication = Medication::find($request->id);
        $medication->status = $request->status;
        $medication->save();
        Toastr::success(trans('Medication status updated successully'));
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


            $nameExist = DB::table('medications')
            ->where('name', $request->name_en)
            ->where('mid', '!=', "m".$id)
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
            $nameExist = DB::table('medications')
            ->where('name', $request->name_fr)
           ->where('mid', '!=', "m".$id)
            ->exists();

            $name = $request->name_fr;

         }


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }


        $exists = DB::table('medications')->where(['mid' => "m".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('medications')->where(['mid' => "m".$id,'lang_code' => $langName])->update([
                'name' => $name
            ]);            

        } else {
            DB::table('medications')->insertGetId([
                'name' => $name,
                'mid' => "m".$id,
                'lang_code' => $langName,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }
        Toastr::success(trans('Medication updated successully'));
        return redirect('admin/medication/add-new');
    }

    public function delete($id)
    {

        $Medication = Medication::find($id);     
        $Medication->delete();
        Toastr::success(trans('Medication deleted successully'));
        return back();
    }

    public function search(Request $request){
        $key = $request['search'];        
        $medications = Medication::select('*') ->where('name', 'like', "%$key%")->get();
        return response()->json([
            'view'=>view('admin-views.medication.partials._table',compact('medications'))->render(),
            'count'=>$medications->count()
        ]);
    }
}
