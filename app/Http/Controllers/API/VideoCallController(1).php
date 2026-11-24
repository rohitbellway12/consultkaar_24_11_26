<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
#use TomatoPHP\LaravelAgora\Services\Token\RtcTokenBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
#use Taylanunutmaz\AgoraTokenBuilder\RtcTokenBuilder;
use App\Models\User;
use App\CentralLogics\Helpers;
use Yasser\Agora\RtcTokenBuilder;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class VideoCallController extends Controller
{
   
    
    
    public function generateToken(Request $request)
{
    //   echo "<pre>";
    // print_r($request->all());
    // echo "</pre>";
    $token = JWTAuth::getToken();
    
    $user = JWTAuth::toUser($token);
    $userId = (isset($user) && !empty($user)) ? $user->id : '';

    // $appID = '98afcf84bd645dd80a27d28b47ccba3';
    // $appCertificate = '342a529afa3b402495b707c754cad29a';
    $appID = '3152a19901174b2ea29cba1a0ffdcef6';
    $appCertificate = '610892804e95494eb99cf61c9737f81e';
    // $appID = '05f1690d913e4bddbad1613f9fb71a18';
    // $appCertificate = 'fb5f7d9224ac4b049921cab359ce4bf3';
    
    $channelName = "video_call_session".uniqid();
    $doctorId = $request->doctor_id;
    $appointmentId = $request->appointment_id; // <--- Naya input
    $channelName = $request->channel_name;
    $user = 0 ;
    $role = RtcTokenBuilder::RoleAttendee;
    $expireTimeInSeconds = 3600;
    $currentTimestamp = now()->getTimestamp();
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
    

    $doctorData = User::find($doctorId);
    
    if(empty($doctorData)){
          return response()->json(['status' => 'error', 'code' => 404,'message' => 'Doctor not found']);
    }
    
    $doctorName = $doctorData->name;
    // $consultationFee = $doctorData->consultation_fee;
    $doctorFcmToken = $doctorData->fcm_token;
    
    $agoraToken = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);

 $callType = $request->type ?? 'video';
 
   switch($callType){
            case 'video':
                $consultationFee = $doctorData->video_fee ?? 0;
                break;
            case 'audio':
                $consultationFee = $doctorData->audio_fee ?? 0;
                break;
            default:
                $consultationFee = 0;
                break;
        }
 
 
    // Call request insert karna
    $requestId = DB::table('call_requests')->insertGetId([
            'user_id' => $userId,
            'doctor_id' => $doctorId,
            'call_status' => 'pending',
            'call_rate' => $consultationFee ?? 0,
             'call_type' => $callType,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
    if(!empty($appointmentId)){
        DB::table('appointments')
            ->where('id', $appointmentId)
            ->update(['call_request_id' => $requestId]);
    }
    
    $description = $agoraToken . "~".$doctorId."~".$channelName."~".$doctorId."~".$doctorName."~".$consultationFee."~".$requestId;

    $data = [
        'title' => 'Call',
        'description' => $description,
        'image' => '',
        'type'=> '',
        'request_id' => $requestId, // <--- Data me bhi add kar diya safety ke liye
        'appointment_id' => $appointmentId
    ];
    
    $response = Helpers::send_push_notif_to_device($doctorFcmToken,$data);
    
    return response()->json(['status' => 'success', 'code' => 200,'message' => 'Token generated successfully','data' => $data]);
}
    
    
    public function getRequest(Request $request) {
        
        $requestId = $request->request_id;
  
        $requestData =  DB::table('call_requests')->where('id', $requestId)->first();


        
        
        return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data Found',
                'data' => $requestData
            ]);    
                            
                    
    }
    
   
public function endCall(Request $request)
{
    // ✅ Validation
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

    // ✅ Update user wallet
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

  

}
?>