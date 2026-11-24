<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;


class BannerController extends Controller
{
    function index(Request $request)
    {
      
        $specialities =  DB::table('specialities')->where('lang_code','en')->where('status',1)->get();  
        $french_specialities =  DB::table('specialities')->where('lang_code','fr')->where('status',1)->get(); 
       
        $banners = DB::table('banners')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, bid FROM banners GROUP BY bid) as sub'), function ($join) {
            $join->on('banners.created_at', '=', 'sub.min_created_at')
                 ->on('banners.bid', '=', 'sub.bid');
        })->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($banners);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();

        $banners = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        // Set path for pagination links
        $banners->withPath($request->url());
    
        return view('admin-views.banner.index', compact('banners','specialities','french_specialities'));
    }

    public function store(Request $request)
    {     
        
        
        $langName = $request->lang_code;

        if($langName == 'en'){
            $validator = Validator::make($request->all(), [
                'speciality_id_en' => 'required',
            ], [
                'speciality_id_en.required'=> "Name is required!",
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }


         

            $specialityId = $request->speciality_id_en;
       


        } else {
            $validator = Validator::make($request->all(), [
                'speciality_id_fr' => 'required',
            ], [
                'speciality_id_fr.required'=> "Name is required!",
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
          

            $specialityId = $request->speciality_id_fr;


         }

    

        $banner = new Banner;
        $banner->image = Helpers::upload('banner/', 'png', $request->file('image'));
        $banner->speciality_id = $specialityId;
        $banner->banner_for = $request->banner_for;
        //-------------------------Rifaul change----------------------------//
        $banner->lab_test = $request->banner_for === 'lab_test' ? $request->lab_test_id : null;
        $banner->save();
        $id = $banner->id;

        DB::table('banners')->where(['id' => $id,'lang_code' => $langName])->update([
            'bid' => "b".$id,
        ]); 
 
        return response()->json([], 200);
    }

    public function edit(Banner $banner)
    {
        $banner = Banner::find($banner->id);
        $specialities =  DB::table('specialities')->where('lang_code','en')->where('status',1)->get();  
        $french_specialities =  DB::table('specialities')->where('lang_code','fr')->where('status',1)->get(); 
        $products = Product::where('status', '1')->orderBy('id','desc')->get();
        return view('admin-views.banner.edit', compact('banner','products','specialities','french_specialities'));
    }

    public function status(Request $request)
    {
        $banner = Banner::find($request->id);
        $banner->status = $request->status;
        $banner->save();
        Toastr::success(trans('messages.banner_status_updated'));
        return back();
    }

    // public function update(Request $request, Banner $banner)
    // {

    //     $langName = $request->lang_code;
      
    //     if($langName == 'en'){
    //         $validator = Validator::make($request->all(), [
    //             'speciality_id_en' => 'required',
    //         ], [
    //             'speciality_id_en.required'=> "Name is required!",
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json(['errors' => Helpers::error_processor($validator)]);
    //         }


     

    //         $specialityId = $request->speciality_id_en;




    //     } else {
    //         $validator = Validator::make($request->all(), [
    //             'speciality_id_fr' => 'required',
    //         ], [
    //             'speciality_id_fr.required'=> "Name  is required!",
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json(['errors' => Helpers::error_processor($validator)]);
    //         }
     
          
    //         $specialityId = $request->speciality_id_fr;
      

    //      }


    //      $id = $banner->id;
    //       $bid = "b".$id;
    //     //  echo($id);


    //      $exists = DB::table('banners')->where(['bid' => "d".$id, 'lang_code' => $langName])->exists();

    //      if ($exists) {
    //          DB::table('banners')->where(['bid' => "b".$id,'lang_code' => $langName])->update([
    //              'banner_for' =>$request->banner_for,
    //              'image' => $request->has('image') ? Helpers::update('disease/', $banner->image, 'png', $request->file('image')) : $banner->image,
    //              'speciality_id' => $specialityId,
    //              'lab_test' => $request->banner_for === 'lab_test' ? $request->lab_test_id : null,
    //          ]);            
 
    //      } else {
    //          DB::table('banners')->insertGetId([
    //             'banner_for' =>$request->banner_for,
    //              'bid' => "b".$id,
    //              'lang_code' => $langName,
    //              'image' => $request->has('image') ? Helpers::update('disease/', $banner->image, 'png', $request->file('image')) : $banner->image,
    //              'speciality_id' => $specialityId,
    //               'lab_test' => $request->banner_for === 'lab_test' ? $request->lab_test_id : null,
    //              'created_at' => now(),
    //              'updated_at' => now(),
 
    //          ]);           
    //      }




 
    //     Toastr::success(trans('messages.banner_updated_successfully'));
    //     return redirect('admin/banner/add-new');
    // }
    //-------------------------------------------------Rifaul change------------------------------------------//
    public function update(Request $request, Banner $banner)
{
    $langName = $request->lang_code;

    if($langName == 'en'){
        $validator = Validator::make($request->all(), [
            'speciality_id_en' => 'required',
        ], [
            'speciality_id_en.required'=> "Name is required!",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $specialityId = $request->speciality_id_en;
    } else {
        $validator = Validator::make($request->all(), [
            'speciality_id_fr' => 'required',
        ], [
            'speciality_id_fr.required'=> "Name  is required!",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $specialityId = $request->speciality_id_fr;
    }

    $id = $banner->id;
    $bid = "b".$id;  // Ensure a consistent bid value

    $exists = DB::table('banners')->where(['bid' => $bid, 'lang_code' => $langName])->exists();

    if ($exists) {
        // Update the existing record
        DB::table('banners')->where(['bid' => $bid, 'lang_code' => $langName])->update([
            'banner_for' => $request->banner_for,
            'image' => $request->has('image') ? Helpers::update('banner/', $banner->image, 'png', $request->file('image')) : $banner->image,
            'speciality_id' => $specialityId,
            'lab_test' => $request->banner_for === 'lab_test' ? $request->lab_test_id : null,
            'updated_at' => now(),
        ]);
    } else {
        // If no existing record, insert a new one
        DB::table('banners')->insertGetId([
            'banner_for' => $request->banner_for,
            'bid' => $bid,
            'lang_code' => $langName,
            'image' => $request->has('image') ? Helpers::update('banner/', $banner->image, 'png', $request->file('image')) : $banner->image,
            'speciality_id' => $specialityId,
            'lab_test' => $request->banner_for === 'lab_test' ? $request->lab_test_id : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    Toastr::success(trans('messages.banner_updated_successfully'));
    return redirect('admin/banner/add-new');
}


    public function delete(Banner $banner)
    {
        if (Storage::disk('public')->exists('banner/' . $banner['image'])) {
            Storage::disk('public')->delete('banner/' . $banner['image']);
        }
        $banner->delete();
        Toastr::success(trans('messages.banner_beleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = $request['search'];
        
        $banners = Banner::join('specialities', 'banners.speciality_id', '=', 'specialities.id')
        ->select('banners.*')
        ->where('specialities.name', 'like', "%$key%")
        ->limit(50)
        ->get();
        return response()->json([
            'view'=>view('admin-views.banner.partials._table',compact('banners'))->render(),
            'count'=>$banners->count()
        ]);
    }
    
    
    
    
    
    
    
    
    
    //-----------------------------------Rifual change------------------------------------//
    public function getLabTests()
{
    $labTests = DB::table('lab_test')->select('id', 'name')->get();
    return response()->json($labTests);
}

}
