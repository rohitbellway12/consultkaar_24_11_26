<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\Speciality;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DiseaseController extends Controller
{


    function index()
    {
        $specialities =  DB::table('specialities')->where('lang_code','en')->where('status',1)->get();  
        $french_specialities =  DB::table('specialities')->where('lang_code','fr')->where('status',1)->get(); 
        return view('admin-views.disease.index',compact('specialities','french_specialities'));
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
            $specialityId = $request->speciality_id_en;
       


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
            $specialityId = $request->speciality_id_fr;


         }

         $nameExist = DB::table('diseases')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }


        $disease = new Disease;
        $disease->name = $name;
        $disease->image = Helpers::upload('disease/', 'png', $request->file('image'));
        $disease->speciality_id = $specialityId;
        $disease->lang_code = $langName;
        $disease->save();
        $id = $disease->id;


        DB::table('diseases')->where(['id' => $id,'lang_code' => $langName])->update([
            'did' => "d".$id,
        ]); 

        
        Toastr::success('Disease added successfully');

        return back();
    }

    function edit($id)
    {
        $disease = Disease::find($id);
        $specialities =  DB::table('specialities')->where('lang_code','en')->where('status',1)->get();         
        $french_specialities =  DB::table('specialities')->where('lang_code','fr')->where('status',1)->get(); 

   
        return view('admin-views.disease.edit',compact('disease','specialities','french_specialities'));
    }

    public function update(Request $request, $id)
    {
        $langName = $request->lang_code;
        $disease = Disease::find($id);
      
        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
            ], [
                'name_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


            $nameExist = DB::table('diseases')
            ->where('name', $request->name_en)
            ->where('did', '!=', "d".$id)
            ->exists();

            $name = $request->name_en;
            $specialityId = $request->speciality_id_en;




        } else {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required',
            ], [
                'name_fr.required'=> "Name  is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
            $nameExist = DB::table('diseases')
            ->where('name', $request->name_fr)
           ->where('did', '!=', "d".$id)
            ->exists();
          
            $name = $request->name_fr;
            $specialityId = $request->speciality_id_fr;
      

         }


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }


        $exists = DB::table('diseases')->where(['did' => "d".$id, 'lang_code' => $langName])->exists();

        if ($exists) {
            DB::table('diseases')->where(['did' => "d".$id,'lang_code' => $langName])->update([
                'name' => $name,
                'image' => $request->has('image') ? Helpers::update('disease/', $disease->image, 'png', $request->file('image')) : $disease->image,
                'speciality_id' => $specialityId
            ]);            

        } else {
            DB::table('diseases')->insertGetId([
                'name' => $name,
                'did' => "d".$id,
                'lang_code' => $langName,
                'image' => $request->has('image') ? Helpers::update('disease/', $disease->image, 'png', $request->file('image')) : $disease->image,
                'speciality_id' => $specialityId,
                'created_at' => now(),
                'updated_at' => now(),

            ]);           
        }


        
        Toastr::success('Disease updated successfully');

        

        return back();
    }

    public function delete(Request $request)
    {
        $disease = Disease::find($request->id);
        $disease->delete();
        Toastr::success('Disease deleted successfully');
        return back();
    }

    // function list(Request $request)
    // {
    //     $diseases = DB::table('diseases')
    //     ->select('*')
    //     // ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, did as sub'), function ($join) {
    //     //     $join->on('diseases.created_at', '=', 'sub.min_created_at')
    //     //          ->on('diseases.did', '=', 'sub.did');})
    //     ->orderBy('created_at','desc')->get();
    //     // Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($diseases);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();
    
    //     $diseases = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    //     // Set path for pagination links
    //     $diseases->withPath($request->url());
    //     // dd($diseases);
    //     return view('admin-views.disease.list',compact('diseases'));
    // }

  public function list(Request $request)
{

    $searchKey = $request->input('search');

    $query = DB::table('diseases')
        ->select('*');

    if ($searchKey) {
        $query->where(function ($q) use ($searchKey) {
            $q->where('diseases.name', 'like', "%{$searchKey}%");
        });
    }

    $diseases = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

    return view('admin-views.disease.list', compact('diseases'));
}



    public function search(Request $request){
        $searchKey = $request['search'];
        $diseases = Disease::where(function ($q) use ($searchKey) {           
                    $q->orWhere('diseases.name', 'like', "%{$searchKey}%");
                     })
            ->select("diseases.name as disease_name","specialities.name as specialitiy_name","diseases.image as disease_image","diseases.id","diseases.speciality_id as speciality_id","diseases.status as status")         
            ->leftJoin('specialities', 'diseases.speciality_id', '=', 'specialities.id')
            ->orWhere('specialities.name', 'like',  "%{$searchKey}%")
            ->limit(50)->get();
           
        
            return response()->json([
            'view'=>view('admin-views.disease.partials._table',compact('diseases'))->render(),
            'count'=>$diseases->count()
        ]);
        
        
    }  

    public function status(Request $request)
    {
        $disease = Disease::find($request->id);
        $disease->status = $request->status;
        $disease->save();
        Toastr::success('Disease status updated!');
        return back();
    }
}
