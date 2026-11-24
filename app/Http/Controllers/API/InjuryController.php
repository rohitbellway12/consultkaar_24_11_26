<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Injury;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class InjuryController extends Controller
{
    public function index(Request $request){
        try {
            
            // get injury data 

            // Get customer_id from the token
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $userData = User::find($userId);
            $langCode = $userData->lang_code;
            
            $injuries = Injury::where(['status'=> 1,'lang_code' => $langCode])->orderBy('id', 'desc')->get();


            if (count($injuries) > 0) {
                return response()->json(['status' => 'success', 'code' => 200,'message' => 'Data found','data' => $injuries ]);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found','data' => []]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => []]);
        }
    }
}
