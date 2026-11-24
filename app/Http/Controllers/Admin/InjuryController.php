<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Injury;
use Brian2694\Toastr\Facades\Toastr;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class InjuryController extends Controller
{
    // function index(Request $request)
    // {
    //     $injuries = DB::table('injuries')
    //         ->select('*')
    //         ->whereIn('id', function ($query) {
    //             $query->select(DB::raw('MIN(id)'))
    //                 ->from('injuries')
    //                 ->groupBy('iid');
    //         })
    //     ->orderBy('id','desc')->get();
        
    //     // Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($injuries);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

    //     $injuries = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    //     // Set path for pagination links
    //     $injuries->withPath($request->url());
    //     return view('admin-views.injury.index', compact('injuries'));
    // }
    
   public function index(Request $request)
{
    $searchKey = $request->input('search');

    $injuriesQuery = DB::table('injuries')
        ->select('*')
        ->whereIn('id', function ($query) {
            $query->select(DB::raw('MIN(id)'))
                ->from('injuries')
                ->groupBy('iid');
        });

    if ($searchKey) {
        $injuriesQuery->where(function ($q) use ($searchKey) {
            $q->where('injuries.name', 'like', "%{$searchKey}%");
        });
    }

    $injuries = $injuriesQuery->orderBy('id', 'desc')->paginate(10);

    // Set path for pagination links
    $injuries->withPath($request->url());

    return view('admin-views.injury.index', compact('injuries'));
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

        $nameExist = DB::table('injuries')
        ->where('name', $name)
        ->exists();


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }



        $injury = new Injury;
        $injury->name = $name;
        $injury->lang_code = $langName;
        $injury->save();
        $id = $injury->id;


        DB::table('injuries')->where(['id' => $id,'lang_code' => $langName])->update([
            'iid' => "i".$id,
        ]); 
        return response()->json([], 200);
    }

    public function edit($id)
    {
        $injury = injury::find($id);
        return view('admin-views.injury.edit', compact('injury'));
    }

    public function status(Request $request)
    {
        $injury = Injury::find($request->id);

        $iid =  "i".$request->id;
        $injuries = Injury::where('iid', $iid)->get();

        foreach ($injuries as $injury) {
            $injury->status = $request->status;
            $injury->save();
        }

  
        Toastr::success(trans('Injury status updated successully'));
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


            $nameExist = DB::table('injuries')
            ->where('name', $request->name_en)
            ->where('iid', '!=', "i".$id)
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
            $nameExist = DB::table('injuries')
            ->where('name', $request->name_fr)
           ->where('iid', '!=', "i".$id)
            ->exists();

            $name = $request->name_fr;

         }


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }


        $exists = DB::table('injuries')->where(['iid' => "i".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('injuries')->where(['iid' => "i".$id,'lang_code' => $langName])->update([
                'name' => $name
            ]);            

        } else {
            DB::table('injuries')->insertGetId([
                'name' => $name,
                'iid' => "i".$id,
                'lang_code' => $langName,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }

        

        Toastr::success(trans('injury updated successully'));
        return redirect('admin/injury/add-new');
    }

    public function delete($id)
    {

        $iid =  "i".$id;
        $injuries = Injury::where('iid', $iid)->get();
        foreach ($injuries as $injury) {
            $injury->delete();
        }
        Toastr::success(trans('Injury deleted successully'));
        return back();
    }

    public function search(Request $request){
        $key = $request['search'];        
        $injuries = Injury::select('*') ->where('name', 'like', "%$key%")->get();
        return response()->json([
            'view'=>view('admin-views.injury.partials._table',compact('injuries'))->render(),
            'count'=>$injuries->count()
        ]);
    }
}
