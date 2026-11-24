<?php

namespace App\Http\Controllers\API\Doctor;
use App\Http\Controllers\Controller;
use TomatoPHP\LaravelAgora\Services\Token\RtcTokenBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class VideoCallController extends Controller
{
   public function manageCallRequest(Request $request){
    
        
            try {
             
                $callRequest = DB::table('call_requests')->where('id', $request->request_id)->first();
        
                if (!$callRequest) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Call request not found.',
                        'code' => 404
                    ], 404);
                }
        
        
                $status = $request->status;
             
                if($status == 'accept'){
                    $doctorId = $callRequest->doctor_id;
                    $doctorData = User::find($doctorId);
                    $doctorData->availability_status = 'Busy';
                    $doctorData->save();
                    
                    
                    DB::table('call_requests')->where('id', $request->request_id)->update([
                        'call_status' => 'progress'
                    ]);    
                    
                    
                    return response()->json([
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Call request accepted successfully.'
                    ], 200);    
                } else {
                    
                    DB::table('call_requests')->where('id', $request->request_id)->update([
                        'call_status' => 'cancel',
                    ]);
                    
                    return response()->json([
                    'status' => 'success',
                     'code' => 200,
                    'message' => 'Call request rejected  successfully.'
                ], 200);
                }
        
                
            } catch (\Exception $e) {
            
                // Rollback the transaction in case of an error
                     return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        
            }
    }
    
  
    public function endCall(Request $request)
{

    if (!$request->request_id) {
        return response()->json([
            'status' => 'error',
            'message' => 'request_id is required'
        ], 422);
    }

    $requestId = $request->request_id;
    $amount    = $request->amount_used ?? 0;
    $totalMin  = $request->total_min ?? 0;

    $requestData = DB::table('call_requests')->where('id', $requestId)->first();

    if (!$requestData) {
        return response()->json([
            'status' => 'error',
            'message' => 'Call request not found'
        ], 404);
    }

    $userId   = $requestData->user_id;
    $doctorId = $requestData->doctor_id;

    $userData = User::find($userId);
    if ($userData) {
        $userData->wallet_amount -= $amount;
        $userData->save();
    }

    $doctorData = User::find($doctorId);
    if ($doctorData) {
        $doctorData->availability_status = 'Available';
        $doctorData->wallet_amount += $amount;
        $doctorData->save();
    }


    DB::table('call_requests')
        ->where('id', $requestId)
        ->update([
            'call_status' => 'complete',
            'deduction'   => $amount,
            'total_min'   => $totalMin
        ]);

    return response()->json([
        'status'  => 'success',
        'code'    => 200,
        'message' => 'Call ended successfully'
    ]);
}

    
    public function getCallHistory(Request $request){
        try {
            // Get requested data
            $page = $request->get('page');
            $orderBy =  'desc';
            $orderColumn = 'created_at';
            $perPage = 10; 
            $searchKey = $request->get('search_key');
            
            
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';
            
        
            // Define the validation rules
            $validationRules = [
                'page' => 'required',
            ];
    
            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'page.required' => 'Page is required.',
            ]);
    
            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            $appointments =  DB::table('call_requests as call')
                    ->join('users as astr', 'astr.id', '=', 'call.doctor_id')
                    ->join('users as u', 'u.id', '=', 'call.user_id')
                    ->select('u.name as userName', 'u.email as user_email', 'astr.name as doctor_name','astr.email as doctor_email', 'call.*','u.gender')
                    ->orderBy('call.id', 'DESC')
                    ->where(array('call.doctor_id' => $userId))
                    ->paginate($perPage, ['*'], 'page', $page);
    
               
            
            
            if($appointments->total() > 0){
                $status = "success";
                $message = "Data found";
            } else {
                $status = "error";
                $message = "No data found";
            }
    
            $responseData = [
                'status' => $status,
                'message' => $message,
                'code' => 200,
                'data' => [
                    'data' => $appointments->items(),
                    'paginator' => [
                        'totalDocs' => $appointments->total(),
                        'limit' => $appointments->perPage(),
                        'page' => $appointments->currentPage(),
                        'totalPages' => $appointments->lastPage(),
                        'hasPrevPage' => $appointments->previousPageUrl() ? true : false,
                        'hasNextPage' => $appointments->nextPageUrl() ? true : false,
                    ],
                ]
            ];
    
            return response()->json($responseData);
    
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
}
?>