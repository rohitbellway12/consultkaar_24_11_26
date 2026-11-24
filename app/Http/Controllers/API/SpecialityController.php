<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Speciality;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;


class SpecialityController extends Controller
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
            
            // get speciality data 

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $userData = User::find($userId);
            $langCode = $userData->lang_code;
             
             $specialities   = Speciality::where(['status' => 1,'lang_code' => $langCode])->orderBy('name', 'asc')->get();
             
             if (count($specialities) > 0) {
                 foreach($specialities as $key => $value){
                    $specialityImagePath = (env('APP_ENV') == 'local') ? asset('storage/speciality/' . $value->image) : asset('storage/app/public/speciality/' . $value->image); 
                    $value->image = $specialityImagePath;
                 } 
                 return response()->json(['status' => 'success', 'code' => 200,'data' => $specialities , 'message' => 'Data found']);
             } else {
                 return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found'], 404);
             }
         } catch (\Exception $e) {
             // Handle exceptions, if any
             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
         }
    }
}
