<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Surgery;
use Brian2694\Toastr\Facades\Toastr;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SurgeryController extends Controller
{
    // function index(Request $request)
    // {
    //     $surgeries = DB::table('surgeries')
    //         ->select('*')
    //         ->whereIn('id', function ($query) {
    //             $query->select(DB::raw('MIN(id)'))
    //                 ->from('surgeries')
    //                 ->groupBy('sid');
    //         })
    //     ->orderBy('id','desc')->get();
        
    //     // Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($surgeries);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

    //     $surgeries = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        
    //     // Set path for pagination links
    //     $surgeries->withPath($request->url());
    //     return view('admin-views.surgery.index', compact('surgeries'));
    // }

public function index(Request $request)
{
    $searchKey = $request->input('search');

    $surgeriesQuery = DB::table('surgeries')
        ->select('*')
        ->whereIn('id', function ($query) {
            $query->select(DB::raw('MIN(id)'))
                ->from('surgeries')
                ->groupBy('sid');
        });

    if ($searchKey) {
        $surgeriesQuery->where(function ($q) use ($searchKey) {
            $q->where('surgeries.name', 'like', "%{$searchKey}%");
        });
    }

    $surgeries = $surgeriesQuery->orderBy('id', 'desc')->paginate(10); // Use paginate not get()

    // Set path for pagination links
    $surgeries->withPath($request->url());

    return view('admin-views.surgery.index', compact('surgeries'));
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

        $nameExist = DB::table('surgeries')
        ->where('name', $name)
        ->exists();


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }



        $surgery = new Surgery;
        $surgery->name = $name;
        $surgery->lang_code = $langName;
        $surgery->save();
        $id = $surgery->id;


        DB::table('surgeries')->where(['id' => $id,'lang_code' => $langName])->update([
            'sid' => "s".$id,
        ]); 


 
        return response()->json([], 200);
    }

    public function edit($id)
    {
        $surgery = Surgery::find($id);
        return view('admin-views.surgery.edit', compact('surgery'));
    }

    public function status(Request $request)
    {
        $sid =  "s".$request->id;
        $surgeries = Surgery::where('sid', $sid)->get();
        foreach ($surgeries as $surgery) {
            $surgery->status = $request->status;
            $surgery->save();
        }

   
        Toastr::success(trans('Surgery status updated successully'));
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


            $nameExist = DB::table('surgeries')
            ->where('name', $request->name_en)
            ->where('sid', '!=', "s".$id)
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
            $nameExist = DB::table('surgeries')
            ->where('name', $request->name_fr)
        ->where('sid', '!=', "s".$id)
            ->exists();

            $name = $request->name_fr;

        }


        if ($nameExist) {
            return response()->json(['errors' => [["message" => 'Name already exist']]]);
        }


        $exists = DB::table('surgeries')->where(['sid' => "s".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('surgeries')->where(['sid' => "s".$id,'lang_code' => $langName])->update([
                'name' => $name
            ]);            

        } else {
            DB::table('surgeries')->insertGetId([
                'name' => $name,
                'sid' => "s".$id,
                'lang_code' => $langName,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }

  
        Toastr::success(trans('Surgery updated successully'));
        return redirect('admin/surgery/add-new');
    }

    public function delete($id)
    {

        $sid =  "s".$id;
        $surgeries = Surgery::where('sid', $sid)->get();
        foreach ($surgeries as $surgery) {
            $surgery->delete();
        }

        Toastr::success(trans('Surgery deleted successully'));
        return back();
    }

    public function search(Request $request){
        $key = $request['search'];        
        $surgeries = Surgery::select('*') ->where('name', 'like', "%$key%")->get();
        return response()->json([
            'view'=>view('admin-views.surgery.partials._table',compact('surgeries'))->render(),
            'count'=>$surgeries->count()
        ]);
    }
}
