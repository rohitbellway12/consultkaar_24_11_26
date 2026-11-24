<?php

// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use App\Models\User;
// use Yasser\Agora\RtcTokenBuilder;
// use Yasser\Agora\RtmTokenBuilder;
// use Illuminate\Support\Facades\Validator;

// class LiveStreamController extends Controller
// {
//     // Credentials from VideoCallController
//     private $appID = '3152a19901174b2ea29cba1a0ffdcef6';
//     private $appCertificate = '610892804e95494eb99cf61c9737f81e';

//     // 1. Generate RTC Token
//     public function generateRtcToken(Request $request) {
//         $validator = Validator::make($request->all(), [
//             'channel_name' => 'required',
//             'role' => 'required|in:broadcaster,audience', // Broadcaster = Host, Audience = Viewer
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Validation failed',
//                 'code' => 422,
//                 'data' => ['errors' => $validator->errors()]
//             ], 422);
//         }

//         $channelName = $request->channel_name;
//         $uid = $request->uid ?? 0; // 0 means Agora will auto-assign, or pass user's numeric ID
//         $roleInput = $request->role;
//         $expireTimeInSeconds = $request->expire_seconds ?? 3600;

//         $role = ($roleInput == 'broadcaster') ? RtcTokenBuilder::RolePublisher : RtcTokenBuilder::RoleSubscriber;
        
//         $currentTimestamp = now()->getTimestamp();
//         $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

//         $token = RtcTokenBuilder::buildTokenWithUserAccount(
//             $this->appID, 
//             $this->appCertificate, 
//             $channelName, 
//             $uid, 
//             $role, 
//             $privilegeExpiredTs
//         );

//         return response()->json([
//             'status' => 'success',
//             'message' => 'RTC token generated successfully',
//             'code' => 200,
//             'data' => [
//                 'app_id' => $this->appID,
//                 'token' => $token,
//                 'channel_name' => $channelName,
//                 'uid' => $uid,
//                 'expire_at' => $privilegeExpiredTs
//             ]
//         ]);
//     }

//     // 2. Generate RTM Token
//     public function generateRtmToken(Request $request) {
//         $validator = Validator::make($request->all(), [
//             'user_id' => 'required',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Validation failed',
//                 'code' => 422,
//                 'data' => ['errors' => $validator->errors()]
//             ], 422);
//         }

//         $userAccount = (string) $request->user_id;
//         $expireTimeInSeconds = $request->expire_seconds ?? 3600;
        
//         $currentTimestamp = now()->getTimestamp();
//         $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
//         $role = RtmTokenBuilder::RoleRtmUser;

//         $token = RtmTokenBuilder::buildToken(
//             $this->appID,
//             $this->appCertificate,
//             $userAccount,
//             $role,
//             $privilegeExpiredTs
//         );

//         return response()->json([
//             'status' => 'success',
//             'message' => 'RTM token generated successfully',
//             'code' => 200,
//             'data' => [
//                 'app_id' => $this->appID,
//                 'rtm_token' => $token,
//                 'user_id' => $userAccount,
//                 'expire_at' => $privilegeExpiredTs
//             ]
//         ]);
//     }

//     // 3. Start Live Session
//     public function startLive(Request $request) {
//         $validator = Validator::make($request->all(), [
//             'title' => 'nullable|string',
//         ]);

//          if ($validator->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Validation failed',
//                 'code' => 422,
//                 'data' => ['errors' => $validator->errors()]
//             ], 422);
//         }

//         $user = auth()->user();
//         $doctorId = $user ? $user->id : $request->doctor_id;

//         if (!$doctorId) {
//              return response()->json([
//                 'status' => 'error',
//                 'message' => 'Doctor ID is required',
//                 'code' => 400,
//                 'data' => null
//             ], 400); 
//         }

//         // Generate unique channel name
//         $channelName = 'live_' . $doctorId . '_' . time();
        
//         // Create Live Session
//         $sessionId = DB::table('live_sessions')->insertGetId([
//             'doctor_id' => $doctorId,
//             'channel_name' => $channelName,
//             'status' => 'live',
//             'title' => $request->title,
//             'created_at' => now(),
//             'updated_at' => now()
//         ]);

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Live session started successfully',
//             'code' => 200,
//             'data' => [
//                 'live_session_id' => $sessionId,
//                 'channel_name' => $channelName,
//                 'doctor_id' => $doctorId,
//                 'session_status' => 'live'
//             ]
//         ]);
//     }

//     // 4. End Live Session
//     public function endLive(Request $request) {
//         $validator = Validator::make($request->all(), [
//             'live_session_id' => 'required'
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Validation failed',
//                 'code' => 422,
//                 'data' => ['errors' => $validator->errors()]
//             ], 422);
//         }

//         DB::table('live_sessions')
//             ->where('id', $request->live_session_id)
//             ->update([
//                 'status' => 'ended', 
//                 'ended_at' => now(),
//                 'updated_at' => now()
//             ]);

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Live session ended successfully',
//             'code' => 200,
//             'data' => null
//         ]);
//     }

//     // 5. Get Active Live Sessions
//     public function getActiveLives() {
//         $sessions = DB::table('live_sessions')
//             ->join('users', 'live_sessions.doctor_id', '=', 'users.id')
//             ->where('live_sessions.status', 'live')
//             ->select(
//                 'live_sessions.id as live_session_id',
//                 'live_sessions.channel_name',
//                 'live_sessions.title',
//                 'live_sessions.viewer_count',
//                 'live_sessions.started_at',
//                 'users.id as doctor_id',
//                 'users.name as doctor_name',
//                 'users.image as doctor_image'
//             )
//             ->orderBy('live_sessions.created_at', 'desc')
//             ->get();

//         if ($sessions->isEmpty()) {
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'No active live sessions found',
//                 'code' => 200,
//                 'data' => []
//             ]);
//         }

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Active live sessions retrieved successfully',
//             'code' => 200,
//             'data' => $sessions
//         ]);
//     }

//     // 6. Join/Leave tracking (Optional Basic)
//     public function joinLive(Request $request) {
//          if($request->has('live_session_id')) {
//              DB::table('live_sessions')->where('id', $request->live_session_id)->increment('viewer_count');
//          }
//          return response()->json([
//             'status' => 'success',
//             'message' => 'Successfully joined live session',
//             'code' => 200,
//             'data' => ['action' => 'joined']
//         ]);
//     }

//     public function leaveLive(Request $request) {
//          if($request->has('live_session_id')) {
//              DB::table('live_sessions')->where('id', $request->live_session_id)->decrement('viewer_count');
//          }
//          return response()->json([
//             'status' => 'success',
//             'message' => 'Successfully left live session',
//             'code' => 200,
//             'data' => ['action' => 'left']
//         ]);
//     }
// }
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Yasser\Agora\RtcTokenBuilder;
use Yasser\Agora\RtmTokenBuilder;
use Illuminate\Support\Facades\Validator;

class LiveStreamController extends Controller
{
    // Credentials from VideoCallController
    private $appID = '3152a19901174b2ea29cba1a0ffdcef6';
    private $appCertificate = '610892804e95494eb99cf61c9737f81e';

    // 1. Generate RTC Token
    public function generateRtcToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'channel_name' => 'required',
            'role' => 'required|in:broadcaster,audience', // Broadcaster = Host, Audience = Viewer
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'code' => 422,
                'data' => ['errors' => $validator->errors()]
            ], 422);
        }

        $channelName = $request->channel_name;
        $uid = $request->uid ?? 0; // 0 means Agora will auto-assign, or pass user's numeric ID
        $roleInput = $request->role;
        $expireTimeInSeconds = $request->expire_seconds ?? 3600;

        $role = ($roleInput == 'broadcaster') ? RtcTokenBuilder::RolePublisher : RtcTokenBuilder::RoleSubscriber;
        
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUserAccount(
            $this->appID, 
            $this->appCertificate, 
            $channelName, 
            $uid, 
            $role, 
            $privilegeExpiredTs
        );

        return response()->json([
            'status' => 'success',
            'message' => 'RTC token generated successfully',
            'code' => 200,
            'data' => [
                'app_id' => $this->appID,
                'token' => $token,
                'channel_name' => $channelName,
                'uid' => $uid,
                'expire_at' => $privilegeExpiredTs
            ]
        ]);
    }

    // 2. Generate RTM Token
    public function generateRtmToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'code' => 422,
                'data' => ['errors' => $validator->errors()]
            ], 422);
        }

        $userAccount = (string) $request->user_id;
        $expireTimeInSeconds = $request->expire_seconds ?? 3600;
        
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        $role = RtmTokenBuilder::RoleRtmUser;

        $token = RtmTokenBuilder::buildToken(
            $this->appID,
            $this->appCertificate,
            $userAccount,
            $role,
            $privilegeExpiredTs
        );

        return response()->json([
            'status' => 'success',
            'message' => 'RTM token generated successfully',
            'code' => 200,
            'data' => [
                'app_id' => $this->appID,
                'rtm_token' => $token,
                'user_id' => $userAccount,
                'expire_at' => $privilegeExpiredTs
            ]
        ]);
    }

    // 3. Start Live Session
    public function startLive(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'doctor_id' => 'nullable' // Fallback for testing
        ]);

         if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'code' => 422,
                'data' => ['errors' => $validator->errors()]
            ], 422);
        }

        $user = auth()->user();
        $doctorId = $user ? $user->id : $request->doctor_id;

        if (!$doctorId) {
             return response()->json([
                'status' => 'error',
                'message' => 'Doctor ID is required',
                'code' => 400,
                'data' => null
            ], 400); 
        }

        // IMPORTANT: End any existing live sessions for this doctor first
        // This prevents multiple "ghost" sessions if the doctor didn't end the previous one properly
        DB::table('live_sessions')
            ->where('doctor_id', $doctorId)
            ->where('status', 'live')
            ->update([
                'status' => 'ended',
                'ended_at' => now(),
                'updated_at' => now()
            ]);

        // Generate unique channel name
        $channelName = 'live_' . $doctorId . '_' . time();
        
        // Create Live Session
        $sessionId = DB::table('live_sessions')->insertGetId([
            'doctor_id' => $doctorId,
            'channel_name' => $channelName,
            'status' => 'live',
            'title' => $request->title,
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Live session started successfully',
            'code' => 200,
            'data' => [
                'live_session_id' => $sessionId,
                'channel_name' => $channelName,
                'doctor_id' => $doctorId,
                'session_status' => 'live'
            ]
        ]);
    }

    // 4. End Live Session (Only for Doctor/Host)
    public function endLive(Request $request) {
        $validator = Validator::make($request->all(), [
            'live_session_id' => 'nullable',
            'doctor_id' => 'nullable' // Passed if not using auth headers
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'code' => 422,
                'data' => null
            ], 422);
        }

        $user = auth()->user();
        $currentUserId = $user ? $user->id : $request->doctor_id;

        if (!$currentUserId) {
            return response()->json([
                'status' => 'error',
                'message' => 'User/Doctor ID is required to end session',
                'code' => 400
            ], 400);
        }

        $query = DB::table('live_sessions');

        if ($request->live_session_id) {
            $session = DB::table('live_sessions')->where('id', $request->live_session_id)->first();
            
            if (!$session) {
                return response()->json(['status' => 'error', 'message' => 'Session not found', 'code' => 404], 404);
            }

            // PERMISSION CHECK: Only the host doctor can end the entire session
            if ($session->doctor_id != $currentUserId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not the host. To leave the stream, please use the leave endpoint instead of end.',
                    'code' => 403
                ], 403);
            }

            $query->where('id', $session->id);
        } else {
            // Find active session for this doctor
            $query->where('doctor_id', $currentUserId)->where('status', 'live');
            $session = (clone $query)->first();
        }

        if (!$session) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active live session found for you to end',
                'code' => 404
            ], 404);
        }

        $query->update([
            'status' => 'ended', 
            'ended_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Live session ended successfully for everyone',
            'code' => 200,
            'data' => ['live_session_id' => $session->id]
        ]);
    }

    // 5. Get Active Live Sessions
    public function getActiveLives() {
        $sessions = DB::table('live_sessions')
            ->join('users', 'live_sessions.doctor_id', '=', 'users.id')
            ->where('live_sessions.status', 'live')
            // Defensive: Only show sessions started in the last 24 hours to avoid ghost sessions
            ->where('live_sessions.created_at', '>=', now()->subHours(24))
            ->select(
                'live_sessions.id as live_session_id',
                'live_sessions.channel_name',
                'live_sessions.title',
                'live_sessions.viewer_count',
                'live_sessions.started_at',
                'users.id as doctor_id',
                'users.name as doctor_name',
                'users.image as doctor_image'
            )
            ->orderBy('live_sessions.created_at', 'desc')
            ->get();

        if ($sessions->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No active live sessions found',
                'code' => 200,
                'data' => []
            ]);
        }

        // Transform image paths
        $sessions->transform(function($session) {
            if ($session->doctor_image) {
                $session->doctor_image = (env('APP_ENV') == 'local') 
                    ? asset('storage/users/' . $session->doctor_image) 
                    : asset('storage/app/public/users/' . $session->doctor_image);
            } else {
                $session->doctor_image = asset('public/assets/admin/img/160x160/img1.jpg');
            }
            return $session;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Active live sessions retrieved successfully',
            'code' => 200,
            'data' => $sessions
        ]);
    }

    // 6. Join/Leave tracking (Optional Basic)
    public function joinLive(Request $request) {
         if($request->has('live_session_id')) {
             DB::table('live_sessions')->where('id', $request->live_session_id)->increment('viewer_count');
         }
         return response()->json([
            'status' => 'success',
            'message' => 'Successfully joined live session',
            'code' => 200,
            'data' => ['action' => 'joined']
        ]);
    }

    public function leaveLive(Request $request) {
         if($request->has('live_session_id')) {
             DB::table('live_sessions')->where('id', $request->live_session_id)->decrement('viewer_count');
         }
         return response()->json([
            'status' => 'success',
            'message' => 'Successfully left live session',
            'code' => 200,
            'data' => ['action' => 'left']
        ]);
    }
}
