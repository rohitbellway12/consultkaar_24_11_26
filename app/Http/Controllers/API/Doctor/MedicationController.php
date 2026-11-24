<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class MedicationController extends Controller
{   

    public function index(Request $request){
        try {
            
            // get medication data 


            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $userData = User::find($userId);
            $langCode = $userData->lang_code;

            
            $medications = Medication::where(['status'=> 1,'lang_code' => $langCode])->orderBy('id', 'desc')->get();

            if (count($medications) > 0) {
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found','data' => $medications]);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found','data' => []]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => []]);
        }
    }
}
