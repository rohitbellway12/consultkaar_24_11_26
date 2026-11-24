<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\CentralLogics\Helpers;

class ChatController extends Controller
{
    /**
     * Doctor ko aayi chat requests list karna
     */
    public function incomingRequests(Request $request)
    {
        $doctor = JWTAuth::toUser(JWTAuth::getToken());
        $doctorId = $doctor->id;

        $chats = DB::table('call_requests')
            ->where('doctor_id', $doctorId)
            ->where('call_type', 'chat')
            ->whereIn('call_status', ['pending', 'progress'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $chats
        ]);
    }

    /**
     * Doctor chat accept/reject karna
     */
    public function manageChatRequest(Request $request)
    {
        $doctor = JWTAuth::toUser(JWTAuth::getToken());

        $requestId = $request->request_id;
        $status = $request->status; // accept / reject

        $chatRequest = DB::table('call_requests')->where('id', $requestId)->where('call_type', 'chat')->first();

        if (!$chatRequest) {
            return response()->json(['status' => 'error', 'message' => 'Chat request not found'], 404);
        }

        if ($status == 'accept') {
            // Doctor busy
            $doctor->availability_status = 'Busy';
            $doctor->save();

            DB::table('call_requests')->where('id', $requestId)->update([
                'call_status' => 'progress',
                'call_start_time' => now()
            ]);

            $patient = User::find($chatRequest->user_id);
            if ($patient && !empty($patient->fcm_token)) {
                $data = [
                    'title' => 'Chat Request Accepted',
                    'description' => 'Advisor has accepted your chat request.',
                    'type' => 'chat_accepted',
                    'request_id' => $requestId,
                ];
                Helpers::send_push_notif_to_device($patient->fcm_token, $data, $doctor->id, $patient->id, 'Chat request accepted');
            }

            return response()->json(['status' => 'success', 'message' => 'Chat request accepted']);
        } else {
            // reject
            DB::table('call_requests')->where('id', $requestId)->update([
                'call_status' => 'cancel'
            ]);

            $patient = User::find($chatRequest->user_id);
            if ($patient && !empty($patient->fcm_token)) {
                $data = [
                    'title' => 'Chat Request Rejected',
                    'description' => 'Advisor has rejected your call request.',
                    'type' => 'chat_rejected',
                    'request_id' => $requestId,
                ];
                Helpers::send_push_notif_to_device($patient->fcm_token, $data, $doctor->id, $patient->id, 'Chat request rejected');
            }

            return response()->json(['status' => 'success', 'message' => 'Chat request rejected']);
        }
    }

    /**
     * Chat end karna
     */
    // public function endChat(Request $request)
    // {
    //     $requestId = $request->request_id;

    //     DB::beginTransaction();
    //     try {
    //         $chat = DB::table('call_requests')->where('id', $requestId)->where('call_type', 'chat')->lockForUpdate()->first();

    //         if (!$chat) {
    //             DB::rollBack();
    //             return response()->json(['status' => 'error', 'message' => 'Chat not found'], 404);
    //         }

    //         if ($chat->call_status === 'complete') {
    //             DB::rollBack();
    //             return response()->json(['status' => 'error', 'message' => 'Chat already ended'], 409);
    //         }

    //         if (!$chat->call_start_time) {
    //             DB::rollBack();
    //             return response()->json(['status' => 'error', 'message' => 'Chat start time missing'], 400);
    //         }

    //         // ⏱ Dynamic Calculation
    //         $startTime = \Carbon\Carbon::parse($chat->call_start_time);
    //         $endTime = now();
    //         $totalSeconds = $startTime->diffInSeconds($endTime);
    //         $actualMinutes = max(1, ceil($totalSeconds / 60));
    //         $totalMin = max($actualMinutes, (int) ($chat->slot_time ?? 0));

    //         $amount = (float) $totalMin * (float) ($chat->call_rate ?? 0);

    //         // User & Doctor models with lock
    //         $user = User::lockForUpdate()->find($chat->user_id);
    //         $doctor = User::lockForUpdate()->find($chat->doctor_id);

    //         if (!$user || !$doctor) {
    //             DB::rollBack();
    //             return response()->json(['status' => 'error', 'message' => 'User or Doctor not found'], 404);
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
    //                 'description' => 'Chat charge',
    //                 'transaction_for' => 'chat',
    //                 'order_id' => $chat->id,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ],
    //             [
    //                 'user_id' => $doctor->id,
    //                 'amount' => $amount,
    //                 'created_by' => $user->id,
    //                 'is_credit' => '1',
    //                 'description' => 'Chat earning',
    //                 'transaction_for' => 'chat',
    //                 'order_id' => $chat->id,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]
    //         ]);

    //         // Update DB
    //         DB::table('call_requests')->where('id', $chat->id)->update([
    //             'call_status' => 'complete',
    //             'total_min' => $totalMin,
    //             'deduction' => $amount,
    //             'ended_at' => $endTime,
    //             'updated_at' => now()
    //         ]);

    //         // Notify Patient that Chat is ended
    //         if ($user && !empty($user->fcm_token)) {
    //             $data = [
    //                 'title' => 'Chat Ended',
    //                 'description' => 'Advisor has ended the chat.',
    //                 'type' => 'chat_ended',
    //                 'request_id' => $requestId,
    //             ];
    //             Helpers::send_push_notif_to_device($user->fcm_token, $data, $doctor->id, $user->id, 'Chat ended by Advisor');
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Chat ended successfully',
    //             'data' => [
    //                 'amount' => $amount,
    //                 'minutes' => $totalMin
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    //     }
    // }


public function endChat(Request $request)
{
    $requestId = $request->request_id;

    DB::beginTransaction();

    try {

        $chat = DB::table('call_requests')
            ->where('id', $requestId)
            ->where('call_type', 'chat')
            ->lockForUpdate()
            ->first();

        if (!$chat) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Chat not found'
            ], 404);
        }

        if ($chat->call_status === 'complete') {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Chat already ended'
            ], 409);
        }

        // =========================
        // FIXED SLOT BILLING
        // =========================

        $endTime = now();

        // Always use booked slot time
        $totalMin = (int) ($chat->slot_time ?? 0);

        // Calculate amount
        $amount = (float) $totalMin * (float) ($chat->call_rate ?? 0);

        // User & Doctor models with lock
        $user = User::lockForUpdate()->find($chat->user_id);

        $doctor = User::lockForUpdate()->find($chat->doctor_id);

        if (!$user || !$doctor) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'User or Doctor not found'
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
                'description' => 'Chat charge',
                'transaction_for' => 'chat',
                'order_id' => $chat->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => $doctor->id,
                'amount' => $amount,
                'created_by' => $user->id,
                'is_credit' => '1',
                'description' => 'Chat earning',
                'transaction_for' => 'chat',
                'order_id' => $chat->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);

        // Update DB
        DB::table('call_requests')
            ->where('id', $chat->id)
            ->update([

                'call_status' => 'complete',

                // Fixed slot time save
                'total_min' => $totalMin,

                'deduction' => $amount,

                'ended_at' => $endTime,

                'updated_at' => now()

            ]);

        // Notify Patient
        if ($user && !empty($user->fcm_token)) {

            $data = [
                'title' => 'Chat Ended',
                'description' => 'Advisor has ended the chat.',
                'type' => 'chat_ended',
                'request_id' => $requestId,
            ];

            Helpers::send_push_notif_to_device(
                $user->fcm_token,
                $data,
                $doctor->id,
                $user->id,
                'Chat ended by Advisor'
            );
        }

        DB::commit();

        return response()->json([

            'status' => 'success',
            'message' => 'Chat ended successfully',

            'data' => [

                'amount' => $amount,
                'minutes' => $totalMin

            ]

        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Doctor chat history
     */
    public function chatHistory(Request $request)
    {
        $doctor = JWTAuth::toUser(JWTAuth::getToken());
        $perPage = 10;
        $page = $request->get('page', 1);

        $chats = DB::table('call_requests as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->select('c.*', 'u.name as user_name', 'u.email as user_email')
            ->where('c.doctor_id', $doctor->id)
            ->where('c.call_type', 'chat')
            ->orderBy('c.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $chats
        ]);
    }
}
