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
use App\CentralLogics\Helpers;


class VideoCallController extends Controller
{
    public function manageCallRequest(Request $request)
    {

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

            if ($status == 'accept') {
                $doctorId = $callRequest->doctor_id;
                $doctorData = User::find($doctorId);
                $doctorData->availability_status = 'Busy';
                $doctorData->save();


                DB::table('call_requests')->where('id', $request->request_id)->update([
                    'call_status' => 'progress',
                    'call_start_time' => now()
                ]);

                $patient = User::find($callRequest->user_id);
                if ($patient && !empty($patient->fcm_token)) {
                    $data = [
                        'title' => 'Call Request Accepted',
                        'description' => 'Advisor has accepted your call request.',
                        'type' => 'call_accepted',
                        'request_id' => $request->request_id,
                    ];
                    Helpers::send_push_notif_to_device($patient->fcm_token, $data, $doctorId, $callRequest->user_id);
                }


                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Call request accepted successfully.'
                ], 200);
            } else {

                DB::table('call_requests')->where('id', $request->request_id)->update([
                    'call_status' => 'cancel',
                ]);

                $patient = User::find($callRequest->user_id);
                if ($patient && !empty($patient->fcm_token)) {
                    $data = [
                        'title' => 'Call Request Rejected',
                        'description' => 'Advisor has rejected your call request.',
                        'type' => 'call_rejected',
                        'request_id' => $request->request_id,
                    ];
                    Helpers::send_push_notif_to_device($patient->fcm_token, $data, $callRequest->doctor_id, $callRequest->user_id);
                }

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


    // public function endCall(Request $request)
    // {
    //     if (!$request->request_id) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'request_id is required'
    //         ], 422);
    //     }

    //     $requestId = $request->request_id;

    //     DB::beginTransaction();
    //     try {
    //         $call = DB::table('call_requests')->where('id', $requestId)->lockForUpdate()->first();

    //         if (!$call) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Call request not found'
    //             ], 404);
    //         }

    //         if ($call->call_status === 'complete') {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Call already completed'
    //             ], 400);
    //         }

    //         if (!$call->call_start_time) {
    //             DB::rollBack();
    //             return response()->json(['status' => 'error', 'message' => 'Call start time missing'], 400);
    //         }

    //         // ⏱ Dynamic Calculation
    //         $startTime = \Carbon\Carbon::parse($call->call_start_time);
    //         $endTime = now();
    //         $totalSeconds = $startTime->diffInSeconds($endTime);
    //         $actualMinutes = max(1, ceil($totalSeconds / 60));
    //         $totalMin = max($actualMinutes, (int) ($call->slot_time ?? 0));

    //         $amount = (float) $totalMin * (float) ($call->call_rate ?? 0);

    //         // User & Doctor models with lock
    //         $user = User::lockForUpdate()->find($call->user_id);
    //         $doctor = User::lockForUpdate()->find($call->doctor_id);

    //         if (!$user || !$doctor) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'User or Advisor not found'
    //             ], 404);
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

    //         DB::table('call_requests')->where('id', $call->id)->update([
    //             'call_status' => 'complete',
    //             'deduction' => $amount,
    //             'total_min' => $totalMin,
    //             'ended_at' => $endTime,
    //             'updated_at' => now()
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'status' => 'success',
    //             'code' => 200,
    //             'message' => 'Call ended successfully',
    //             'data' => [
    //                 'amount' => $amount,
    //                 'minutes' => $totalMin
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
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

    $requestId = $request->request_id;

    DB::beginTransaction();

    try {

        // Get call with lock
        $call = DB::table('call_requests')
            ->where('id', $requestId)
            ->lockForUpdate()
            ->first();

        // Call not found
        if (!$call) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Call request not found'
            ], 404);
        }

        // Already completed
        if ($call->call_status === 'complete') {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Call already completed'
            ], 400);
        }

        // =====================================
        // FIXED SLOT BASED BILLING
        // =====================================

        $endTime = now();

        // Always use booked slot time only
        $totalMin = (int) ($call->slot_time ?? 0);

        // Per minute rate
        $callRate = (float) ($call->call_rate ?? 0);

        // Final amount
        $amount = $totalMin * $callRate;

        // User & Doctor with lock
        $user = User::lockForUpdate()->find($call->user_id);

        $doctor = User::lockForUpdate()->find($call->doctor_id);

        if (!$user || !$doctor) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'User or Advisor not found'
            ], 404);
        }

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

        // Doctor available again
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
                'total_min' => $totalMin,

                'deduction' => $amount,

                'ended_at' => $endTime,

                'updated_at' => now()

            ]);

        DB::commit();

        return response()->json([

            'status' => 'success',

            'code' => 200,

            'message' => 'Call ended successfully',

            'data' => [

                'amount' => $amount,

                'minutes' => $totalMin,

                'rate_per_min' => $callRate

            ]

        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([

            'status' => 'error',

            'code' => 500,

            'message' => $e->getMessage()

        ], 500);
    }
}
    public function getCallHistory(Request $request)
    {
        try {
            // Get requested data
            $page = $request->get('page');
            $orderBy = 'desc';
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

            $appointments = DB::table('call_requests as call')
                ->join('users as astr', 'astr.id', '=', 'call.doctor_id')
                ->join('users as u', 'u.id', '=', 'call.user_id')
                ->select('u.name as userName', 'u.email as user_email', 'astr.name as doctor_name', 'astr.email as doctor_email', 'call.*', 'u.gender')
                ->orderBy('call.id', 'DESC')
                ->where(array('call.doctor_id' => $userId))
                ->paginate($perPage, ['*'], 'page', $page);




            if ($appointments->total() > 0) {
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


 public function getInstantCallCount(Request $request)
{
    try {

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $doctorId = $user ? $user->id : null;

        if (!$doctorId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $filter = $request->type; // today, yesterday, this_week, this_month, custom
        $fromDate = null;
        $toDate = null;

       
        switch ($filter) {
            case 'today':
                $fromDate = now()->startOfDay();
                $toDate = now()->endOfDay();
                break;

            case 'yesterday':
                $fromDate = now()->subDay()->startOfDay();
                $toDate = now()->subDay()->endOfDay();
                break;

            case 'this_week':
                $fromDate = now()->startOfWeek();
                $toDate = now()->endOfWeek();
                break;

            case 'this_month':
                $fromDate = now()->startOfMonth();
                $toDate = now()->endOfMonth();
                break;

            case 'custom':
                $fromDate = $request->from_date
                    ? \Carbon\Carbon::parse($request->from_date)->startOfDay()
                    : null;

                $toDate = $request->to_date
                    ? \Carbon\Carbon::parse($request->to_date)->endOfDay()
                    : null;
                break;

            default:
                // No filter → All time
                break;
        }

        // 🔥 Base Query
        $query = DB::table('call_requests')
            ->where('doctor_id', $doctorId);

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        // 🔥 Call Type Count
        $typeCounts = (clone $query)
            ->select('call_type', DB::raw('COUNT(*) as total'))
            ->whereIn('call_type', ['chat', 'audio', 'video'])
            ->groupBy('call_type')
            ->pluck('total', 'call_type');

        // 🔥 Status Count
        $statusCounts = (clone $query)
            ->select('call_status', DB::raw('COUNT(*) as total'))
            ->groupBy('call_status')
            ->pluck('total', 'call_status');

        $totalChat = $typeCounts['chat'] ?? 0;
        $totalAudio = $typeCounts['audio'] ?? 0;
        $totalVideo = $typeCounts['video'] ?? 0;

        $totalCall = $totalChat + $totalAudio + $totalVideo;

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'filter_applied' => $filter ?? 'all_time',
            'data' => [
                'total_chat' => $totalChat,
                'total_audio' => $totalAudio,
                'total_video' => $totalVideo,
                'total_call' => $totalCall,

                'progress_call' => $statusCounts['progress'] ?? 0,
                'complete_call' => $statusCounts['complete'] ?? 0,
                'cancel_call' => $statusCounts['cancel'] ?? 0,
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