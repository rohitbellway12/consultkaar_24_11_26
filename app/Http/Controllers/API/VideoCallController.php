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
use Carbon\Carbon;

class VideoCallController extends Controller
{



    // public function generateToken(Request $request)
// {
//     $token = JWTAuth::getToken();
//     $authUser = JWTAuth::toUser($token);
//     $userId = $authUser ? $authUser->id : null;

    //     if (!$userId) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Unauthorized'
//         ], 401);
//     }

    //     // 🔹 Agora credentials
//     $appID = '05f1690d913e4bddbad1613f9fb71a18';
//     $appCertificate = 'fb5f7d9224ac4b049921cab359ce4bf3';

    //     // 🔹 Inputs
//     $doctorId = $request->doctor_id;
//     $channelName = $request->channel_name ?? ('video_call_' . uniqid());
//     $callType = $request->type ?? 'video';

    //     // 🔹 Doctor fetch
//     $doctor = User::find($doctorId);
//     if (!$doctor) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Doctor not found'
//         ], 404);
//     }

    //     // 🔹 Per minute charge
//     if ($callType === 'audio') {
//         $ratePerMin = $doctor->audio_fee ?? 0;
//     } else {
//         $ratePerMin = $doctor->video_fee ?? 0;
//     }

    //     // 🔹 Agora token
//     $role = RtcTokenBuilder::RoleAttendee;
//     $expireTime = 3600;
//     $currentTimestamp = now()->timestamp;
//     $privilegeExpiredTs = $currentTimestamp + $expireTime;

    //     $agoraToken = RtcTokenBuilder::buildTokenWithUserAccount(
//         $appID,
//         $appCertificate,
//         $channelName,
//         0,
//         $role,
//         $privilegeExpiredTs
//     );

    //     // 🔹 Call request insert
//     $requestId = DB::table('call_requests')->insertGetId([
//         'user_id'         => $userId,
//         'doctor_id'       => $doctorId,
//         'call_status'     => 'pending',
//         'call_rate'       => $ratePerMin,
//         'call_type'       => $callType,
//         'call_start_time' => null,
//         'created_at'      => now(),
//         'updated_at'      => now(),
//     ]);

    //     // 🔹 Push notification data
//     $description = implode('~', [
//         $agoraToken,
//         $doctorId,
//         $channelName,
//         $doctor->name,
//         $ratePerMin,
//         $requestId
//     ]);

    //     $pushData = [
//         'title'       => 'Incoming Call',
//         'description' => $description,
//         'type'        => 'call_request',
//         'request_id'  => $requestId,
//     ];

    //     if (!empty($doctor->fcm_token)) {
//         Helpers::send_push_notif_to_device(
//             $doctor->fcm_token,
//             $pushData,
//             $userId,
//             $doctorId,
//             'Incoming call request'
//         );
//     }

    //     return response()->json([
//         'status'  => 'success',
//         'code'    => 200,
//         'message' => 'Token generated successfully',
//         'data'    => [
//             'token'        => $agoraToken,
//             'channel_name' => $channelName,
//             'request_id'   => $requestId,
//             'rate_per_min' => $ratePerMin,
//             'call_type'    => $callType
//         ]
//     ]);
// }
    public function generateToken(Request $request)
    {
        $token = JWTAuth::getToken();
        $authUser = JWTAuth::toUser($token);
        $userId = $authUser ? $authUser->id : null;

        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::find($userId);

        // 🔹 Agora credentials
        $appID = '05f1690d913e4bddbad1613f9fb71a18';
        $appCertificate = 'fb5f7d9224ac4b049921cab359ce4bf3';

        // 🔹 Inputs
        $doctorId = $request->doctor_id;
        $channelName = $request->channel_name ?? ('video_call_' . uniqid());
        $callType = $request->type ?? 'video';

        // 🔹 Doctor fetch
        $doctor = User::find($doctorId);
        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor not found'
            ], 404);
        }

        // 🔹 Rate decide
        if ($callType === 'audio') {
            $ratePerMin = $doctor->audio_fee ?? 0;
        } else {
            $ratePerMin = $doctor->video_fee ?? 0;
        }

        // ✅ WALLET CHECK (Minimum 1 minute)
        if ($user->wallet_amount < $ratePerMin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance for call'
            ], 402);
        }

        // 🔹 Agora token
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTime = 3600;
        $currentTimestamp = now()->timestamp;
        $privilegeExpiredTs = $currentTimestamp + $expireTime;

        $agoraToken = RtcTokenBuilder::buildTokenWithUserAccount(
            $appID,
            $appCertificate,
            $channelName,
            0,
            $role,
            $privilegeExpiredTs
        );

        // 🔹 Call request insert
        $requestId = DB::table('call_requests')->insertGetId([
            'user_id' => $userId,
            'doctor_id' => $doctorId,
            'call_status' => 'pending',
            'call_rate' => $ratePerMin,
            'slot_time' => $request->slot_time ?? 0,
            'call_type' => $callType,
            'call_start_time' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🔹 Push notification
        $description = implode('~', [
            $agoraToken,
            $doctorId,
            $channelName,
            $doctor->name,
            $ratePerMin,
            $requestId
        ]);

        $pushData = [
            'title' => 'Incoming Call',
            'description' => $description,
            'type' => 'call_request',
            'request_id' => $requestId,
        ];

        if (!empty($doctor->fcm_token)) {
            Helpers::send_push_notif_to_device(
                $doctor->fcm_token,
                $pushData,
                $userId,
                $doctorId,
                'Incoming call request'
            );
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Token generated successfully',
            'data' => [
                'token' => $agoraToken,
                'channel_name' => $channelName,
                'request_id' => $requestId,
                'rate_per_min' => $ratePerMin,
                'call_type' => $callType
            ]
        ]);
    }

    public function getRequest(Request $request)
    {

        $requestId = $request->request_id;

        $requestData = DB::table('call_requests')->where('id', $requestId)->first();




        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data Found',
            'data' => $requestData
        ]);


    }



    // public function endCall(Request $request)
    // {
    //     if (!$request->request_id) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'request_id is required'
    //         ], 422);
    //     }

    //     DB::beginTransaction();

    //     try {

    //         $call = DB::table('call_requests')
    //             ->where('id', $request->request_id)
    //             ->lockForUpdate()
    //             ->first();

    //         if (!$call || $call->call_status === 'complete') {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Invalid or already completed call'
    //             ], 400);
    //         }

    //         if (!$call->call_start_time) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Call start time missing'
    //             ], 400);
    //         }

    //         // ⏱ Time calculation
    //         $startTime = Carbon::parse($call->call_start_time);
    //         $endTime = now();

    //         $totalSeconds = $startTime->diffInSeconds($endTime);
    //         $actualMinutes = max(1, ceil($totalSeconds / 60));
    //         $totalMinutes = max($actualMinutes, (int) ($call->slot_time ?? 0));

    //         // 💰 Rate
    //         $ratePerMin = (float) $call->call_rate;
    //         $amount = $totalMinutes * $ratePerMin;

    //         // 👤 User & Doctor
    //         $user = \App\Models\User::lockForUpdate()->find($call->user_id);
    //         $doctor = \App\Models\User::lockForUpdate()->find($call->doctor_id);

    //         if (!$user || !$doctor) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'User or Advisor not found'
    //             ], 404);
    //         }

    //         // 🛑 Wallet safety
    //         if ($user->wallet_amount < $amount) {
    //             $amount = $user->wallet_amount;
    //             $totalMinutes = $ratePerMin > 0 ? floor($amount / $ratePerMin) : 0;
    //         }

    //         // 💳 Wallet update
    //         $user->wallet_amount -= $amount;
    //         $doctor->wallet_amount += $amount;
    //         $doctor->availability_status = 'Available';

    //         $user->save();
    //         $doctor->save();

    //         // 📒 Wallet transactions
    //         DB::table('wallet_transactions')->insert([
    //             [
    //                 'user_id' => $user->id,
    //                 'amount' => $amount,
    //                 'created_by' => $user->id,
    //                 'is_credit' => '0',
    //                 'description' => 'Call charge',
    //                 'transaction_for' => 'call',
    //                 'order_id' => $call->id,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ],
    //             [
    //                 'user_id' => $doctor->id,
    //                 'amount' => $amount,
    //                 'created_by' => $user->id,
    //                 'is_credit' => '1',
    //                 'description' => 'Call earning',
    //                 'transaction_for' => 'call',
    //                 'order_id' => $call->id,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]
    //         ]);

    //         // 📞 Call update (IMPORTANT)
    //         DB::table('call_requests')->where('id', $call->id)->update([
    //             'call_status' => 'complete',
    //             'deduction' => $amount,
    //             'total_min' => $totalMinutes,
    //             'ended_at' => $endTime,
    //             'updated_at' => now(),
    //         ]);

    //         DB::commit(); // ✅ MOST IMPORTANT

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Call ended successfully',
    //             'data' => [
    //                 'call_id' => $call->id,
    //                 'seconds' => $totalSeconds,
    //                 'minutes' => $totalMinutes,
    //                 'rate_per_min' => $ratePerMin,
    //                 'amount_charged' => $amount,
    //             ]
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function endCall(Request $request)
{
    if (!$request->request_id) {

        return response()->json([
            'status' => 'error',
            'message' => 'request_id is required'
        ], 422);
    }

    DB::beginTransaction();

    try {

        // Find call with lock
        $call = DB::table('call_requests')
            ->where('id', $request->request_id)
            ->lockForUpdate()
            ->first();

        // Check call
        if (!$call || $call->call_status === 'complete') {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or already completed call'
            ], 400);
        }

        // User & Doctor
        $user = \App\Models\User::lockForUpdate()->find($call->user_id);

        $doctor = \App\Models\User::lockForUpdate()->find($call->doctor_id);

        if (!$user || !$doctor) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'User or Advisor not found'
            ], 404);
        }

        // =====================================
        // FIXED SLOT TIME BILLING
        // =====================================

        $endTime = now();

        // Always use booked slot time only
        $totalMinutes = (int) ($call->slot_time ?? 0);

        // Rate per minute
        $ratePerMin = (float) ($call->call_rate ?? 0);

        // Final amount
        $amount = $totalMinutes * $ratePerMin;

        // Wallet balance check
        if ($user->wallet_amount < $amount) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        // Wallet update
        $user->wallet_amount -= $amount;

        $doctor->wallet_amount += $amount;

        // Doctor available
        $doctor->availability_status = 'Available';

        $user->save();

        $doctor->save();

        // Wallet transactions
        DB::table('wallet_transactions')->insert([

            [
                'user_id' => $user->id,
                'amount' => $amount,
                'created_by' => $user->id,
                'is_credit' => '0',
                'description' => 'Call charge',
                'transaction_for' => 'call',
                'order_id' => $call->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => $doctor->id,
                'amount' => $amount,
                'created_by' => $user->id,
                'is_credit' => '1',
                'description' => 'Call earning',
                'transaction_for' => 'call',
                'order_id' => $call->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);

        // Update call request
        DB::table('call_requests')
            ->where('id', $call->id)
            ->update([

                'call_status' => 'complete',

                // Fixed slot time
                'total_min' => $totalMinutes,

                'deduction' => $amount,

                'ended_at' => $endTime,

                'updated_at' => now(),

            ]);

        DB::commit();

        return response()->json([

            'status' => 'success',

            'message' => 'Call ended successfully',

            'data' => [

                'call_id' => $call->id,

                'minutes' => $totalMinutes,

                'rate_per_min' => $ratePerMin,

                'amount_charged' => $amount,

            ]

        ], 200);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([

            'status' => 'error',

            'message' => $e->getMessage()

        ], 500);
    }
}

    public function getInstantCallCount(Request $request)
    {
        try {
            // 🔹 JWT authentication
            $token = JWTAuth::getToken();
            $authUser = JWTAuth::toUser($token);

            if (!$authUser) {
                return response()->json([
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $userId = $authUser->id;
            // $role   = $authUser->role ?? 'user'; // Agar role field available hai

            // 🔹 Count logic

            // User ke liye
            $totalCalls = DB::table('call_requests')->where('user_id', $userId)->count();
            $pendingCalls = DB::table('call_requests')->where('user_id', $userId)->where('call_status', 'pending')->count();
            $completedCalls = DB::table('call_requests')->where('user_id', $userId)->where('call_status', 'complete')->count();


            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Call counts fetched successfully',
                'data' => [
                    'total_calls' => $totalCalls,
                    'pending_calls' => $pendingCalls,
                    'completed_calls' => $completedCalls,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getUserCallHistory(Request $request)
    {
        try {
            // 🔹 Authenticated user
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user->id;

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // 🔹 Pagination & search
            $page = $request->get('page', 1);
            $perPage = 10;
            $searchKey = $request->get('search_key', null);

            // 🔹 Base query
            $query = DB::table('call_requests as call')
                ->leftJoin('users as doctor', 'doctor.id', '=', 'call.doctor_id')
                ->select(
                    'call.*',
                    'doctor.name as doctor_name',
                    'doctor.email as doctor_email',
                    'doctor.image as doctor_image'
                )
                ->where('call.user_id', $userId)
                ->orderBy('call.id', 'DESC');

            // 🔹 Optional search by doctor name or email
            if ($searchKey) {
                $query->where(function ($q) use ($searchKey) {
                    $q->where('doctor.name', 'like', "%{$searchKey}%")
                        ->orWhere('doctor.email', 'like', "%{$searchKey}%");
                });
            }

            // 🔹 Pagination
            $calls = $query->paginate($perPage, ['*'], 'page', $page);

            // 🔹 Transform doctor image
            $calls->getCollection()->transform(function ($call) {
                $call->doctor_image = $call->doctor_image
                    ? asset('storage/users/' . $call->doctor_image)
                    : asset('assets/admin/img/160x160/img1.jpg');
                return $call;
            });

            // 🔹 Response
            $status = $calls->total() > 0 ? 'success' : 'error';
            $message = $calls->total() > 0 ? 'Data found' : 'No data found';

            return response()->json([
                'status' => $status,
                'code' => 200,
                'message' => $message,
                'data' => [
                    'data' => $calls->items(),
                    'paginator' => [
                        'totalDocs' => $calls->total(),
                        'limit' => $calls->perPage(),
                        'page' => $calls->currentPage(),
                        'totalPages' => $calls->lastPage(),
                        'hasPrevPage' => $calls->previousPageUrl() ? true : false,
                        'hasNextPage' => $calls->nextPageUrl() ? true : false,
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>