<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Allergy;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AllergyController extends Controller
{   

    public function index(Request $request){
        try {
            
            // get allergy data 

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $userData = User::find($userId);
            $langCode = $userData->lang_code;
            
            $allergies = Allergy::where(['status'=> 1,'lang_code' => $langCode])->orderBy('id', 'desc')->get();


            if (count($allergies) > 0) {
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found','data' => $allergies ,]);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found','data' => []]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => []]);
        }
    }
}
