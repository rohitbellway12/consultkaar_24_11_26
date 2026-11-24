<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Speciality;
use App\Models\Disease;
use Tymon\JWTAuth\Facades\JWTAuth;

class DiseaseController extends Controller
{
    //

     /**
     * get specialities
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){
         try {            
            
            // get disease data 

           
             
              $diseases   = Disease::where(['status' => 1])->get();
             if (count($diseases) > 0) {
                 foreach($diseases as $key => $value){
                    $diseaseImagePath = (env('APP_ENV') == 'local') ? asset('storage/disease/' . $value->image) : asset('storage/app/public/disease/' . $value->image); 
                    $value->image = $diseaseImagePath;
                    $speciality_data = Speciality::find($value->speciality_id);
                    $speciality_name = ($speciality_data) ? $speciality_data->name : 'N/A';
                    $value->speciality_name = $speciality_name;
                 } 
                 return response()->json(['status' => 'success', 'code' => 200,'data' => $diseases, 'message' => 'Data found']);
             } else {
                 return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found'], 200);
             }
         } catch (\Exception $e) {
             // Handle exceptions, if any
             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
         }
    }
}
