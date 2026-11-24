<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Speciality;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Category;
use App\Models\Disease;
use App\Models\Banner;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Domain;



class SpecialityController extends Controller
{


    function index()
    {
        $domains = Domain::all();
        return view('admin-views.speciality.index', compact('domains'));
    }

    public function store(Request $request)
    {
       


        $langName = $request->lang_code;

        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'name_en' => 'required',
                'domain_id' => 'required',
            ], [
                'name_en.required'=> "Name is required!",
                'domain_id.required'=> "Domain is required!",
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

         $nameExist = DB::table('specialities')
         ->where('name', $name)
         ->exists();


        if ($nameExist) {
            Toastr::error('Name already exist');
            return back();
        }
        
        $speciality = new Speciality;

        $fileName = Helpers::upload('speciality/', 'png', $request->file('image'));


        $speciality->name = $name;
        $speciality->domain_id = $request->domain_id;
        $speciality->lang_code = $langName;
        $speciality->image = $fileName;
        $speciality->save();
        $id = $speciality->id;


        DB::table('specialities')->where(['id' => $id,'lang_code' => $langName])->update([
            'sid' => "s".$id,
        ]); 

        
        Toastr::success('Speciality added successfully');

        return back();
    }

    function edit($id)
    {
       
        $speciality = Speciality::find($id);
        $domains = Domain::all();
        return view('admin-views.speciality.edit',compact('speciality', 'domains'));
    }

    // public function update(Request $request, $id)
    // {
    //    

    //     $langName = $request->lang_code;
    //     $specialityData = Speciality::find($id);
        
    //     if($langName == 'en'){
    //         $validator = Validator::make($request->all(), [
    //             'name_en' => 'required',
    //         ], [
    //             'name_en.required'=> "Name  is required!",
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json(['errors' => Helpers::error_processor($validator)]);
    //         }


    //         $nameExist = DB::table('specialities')
    //         ->where('name', $request->name_en)
    //         ->where('sid', '!=', "s".$id)
    //         ->exists();
    //         $name = $request->name_en;

    //         $fileName = $request->has('image_en') ? Helpers::update('speciality/', $specialityData->image, 'png', $request->file('image_en')) : $specialityData->image;


    //     } else {
    //         $validator = Validator::make($request->all(), [
    //             'name_fr' => 'required',
    //         ], [
    //             'name_fr.required'=> "Name  is required!",
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json(['errors' => Helpers::error_processor($validator)]);
    //         }
    //         $nameExist = DB::table('specialities')
    //         ->where('name', $request->name_fr)
    //       ->where('sid', '!=', "s".$id)
    //         ->exists();

    //         $name = $request->name_fr;
    //         $fileName = $request->has('image_fr') ? Helpers::update('speciality/', $specialityData->image, 'png', $request->file('image_fr')) : $specialityData->image;

    //      }


    //     if ($nameExist) {
    //         Toastr::error('Name already exist');
    //         return back();
    //     }


    //     $exists = DB::table('specialities')->where(['sid' => "s".$id, 'lang_code' => $langName])->exists();

    //     if ($exists) {
    //         DB::table('specialities')->where(['sid' => "s".$id,'lang_code' => $langName])->update([
    //             'name' => $name,
    //             'image' => $fileName,
    //         ]);            

    //     } else {
    //         DB::table('specialities')->insertGetId([
    //             'name' => $name,
    //             'sid' => "s".$id,
    //             'lang_code' => $langName,
    //             'image' => $fileName,
    //             'created_at' => now(),
    //             'updated_at' => now(),

    //         ]);           
    //     }




        
    //     Toastr::success('Speciality updated successfully');

    //     return back();
    // }
    
   public function update(Request $request, $id)
{
    $langCode = $request->lang_code;
    $speciality = Speciality::find($id);

    if (!$speciality) {
        Toastr::error('Speciality not found');
        return back();
    }

    // Determine which name to validate
    $fieldName = ($langCode === 'en') ? 'name_en' : 'name_fr';

    $validator = Validator::make($request->all(), [
        $fieldName => 'required',
        'domain_id' => 'required',
    ], [
        $fieldName . '.required' => 'Name is required!',
        'domain_id.required' => 'Domain is required!',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => Helpers::error_processor($validator)]);
    }

    $name = $request->input($fieldName);

    // Check if name already exists for another speciality
    $nameExists = DB::table('specialities')
        ->where('name', $name)
        ->where('sid', '!=', 's' . $id)
        ->exists();

    if ($nameExists) {
        Toastr::error('Name already exists');
        return back();
    }

    $image = $request->hasFile('image')
        ? Helpers::update('speciality/', $speciality->image, 'png', $request->file('image'))
        : $speciality->image;

    $langEntryExists = DB::table('specialities')->where([
        'sid' => 's' . $id,
        'lang_code' => $langCode,
    ])->exists();

    $data = [
        'name' => $name,
        'image' => $image,
        'domain_id' => $request->domain_id,
        'updated_at' => now(),
    ];

   
    if ($langEntryExists) {
        // Update
        DB::table('specialities')->where([
            'sid' => 's' . $id,
            'lang_code' => $langCode,
        ])->update($data);
    } else {
        // Insert new
        $data['sid'] = 's' . $id;
        $data['lang_code'] = $langCode;
        $data['created_at'] = now();
        DB::table('specialities')->insert($data);
    }

    Toastr::success('Speciality updated successfully');
    return back();
}


    // public function delete(Request $request)
    // {


    //     $sid =  "s".$request->id;
    //     $specialities = Speciality::where('sid', $sid)->get();

    //     foreach ($specialities as $speciality) {
    //         $speciality->delete();
    //     }

    
    //     Toastr::success('Speciality deleted successfully');
    //     return back();
    // }

public function delete(Request $request)
{
    $sid = "s" . $request->id;

    // Get all specialities that match the sid
    $specialities = Speciality::where('sid', $sid)->get();

    if ($specialities->isEmpty()) {
        Toastr::error('Speciality not found');
        return back();
    }

    // Get speciality IDs
    $specialityIds = $specialities->pluck('id')->toArray();

    // Delete diseases linked to those specialities
    Disease::whereIn('speciality_id', $specialityIds)->delete();
      Banner::whereIn('speciality_id', $specialityIds)->delete();

    // Delete the specialities themselves
    foreach ($specialities as $speciality) {
        $speciality->delete();
    }

    Toastr::success('Speciality and related diseases deleted successfully');
    return back();
}



  public function list(Request $request)
{
    $searchKey = $request->input('search');

    $query = DB::table('specialities')
        ->select('specialities.*', 'domains.name as domain_name')
        ->leftJoin('domains', 'specialities.domain_id', '=', 'domains.id')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, sid FROM specialities GROUP BY sid) as sub'), function ($join) {
            $join->on('specialities.created_at', '=', 'sub.min_created_at')
                 ->on('specialities.sid', '=', 'sub.sid');
        });

    // Apply search filter
    if ($searchKey) {
        $query->where(function ($q) use ($searchKey) {
            $q->where('specialities.id', 'like', "%{$searchKey}%")
              ->orWhere('specialities.name', 'like', "%{$searchKey}%");
        });
    }

    // Paginate with query string
    $specialities = $query->orderBy('specialities.created_at', 'desc')
                          ->paginate(10)
                          ->withQueryString();

    return view('admin-views.speciality.list', compact('specialities'));
}


    public function search(Request $request){
        $key = explode(' ', $request['search']);
        
         $specialities = Speciality::with('domain')
       
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->Where('id', 'like', "%{$value}%");
                // Add the condition to search by customer name
                $q->orWhere('name', 'like', "%{$value}%");
            
            }
        })
     
        ->limit(50)->get();
         return response()->json([
            'view'=>view('admin-views.speciality.partials._table',compact('specialities'))->render(),
            'count'=>$specialities->count()
        ]);
    }  

    public function status(Request $request)
    {
        $speciality = Speciality::find($request->id);
        $sid =  "s".$request->id;
        $specialities = Speciality::where('sid', $sid)->get();

        foreach ($specialities as $speciality) {
            $speciality->status = $request->status;
            $speciality->save();
        }
        Toastr::success('Speciality status updated!');
        return back();
    }
}
