<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\CentralLogics\Helpers;

class ChatController extends Controller
{

    public function startChat(Request $request)
    {
        $user = JWTAuth::toUser(JWTAuth::getToken());

        $doctor = User::find($request->doctor_id);
        // dd($doctor);
        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor not found'
            ], 404);
        }

        $requestId = DB::table('call_requests')->insertGetId([
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'call_type' => 'chat',
            'call_status' => 'pending',
            'call_rate' => $doctor->chat_fee,
            'slot_time' => $request->slot_time ?? 0,
            'call_start_time' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🔹 Push notification to doctor
        if ($doctor && !empty($doctor->fcm_token)) {
            $data = [
                'title' => 'Incoming Chat',
                'description' => $user->name . ' is requesting a chat.',
                'type' => 'chat_request',
                'request_id' => $requestId,
            ];
            Helpers::send_push_notif_to_device($doctor->fcm_token, $data, $user->id, $doctor->id, 'Incoming chat request');
        }

        return response()->json([
            'status' => 'success',
            'request_id' => $requestId
        ]);
    }

    // public function endChat(Request $request)
    // {
    //     if (!$request->request_id) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'request_id is required'
    //         ], 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $chat = DB::table('call_requests')->where('id', $request->request_id)->lockForUpdate()->first();

    //         if (!$chat || $chat->call_type !== 'chat') {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Chat not found'
    //             ], 404);
    //         }

    //         if ($chat->call_status === 'complete') {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Chat already ended'
    //             ], 409);
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

    //         $user = User::lockForUpdate()->find($chat->user_id);
    //         $doctor = User::lockForUpdate()->find($chat->doctor_id);

    //         if (!$user || !$doctor) {
    //             DB::rollBack();
    //             return response()->json(['status' => 'error', 'message' => 'User or Doctor not found'], 404);
    //         }

    //         $user->wallet_amount -= $amount;
    //         $doctor->wallet_amount += $amount;
    //         $doctor->availability_status = 'Available';

    //         $user->save();
    //         $doctor->save();

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

    //         DB::table('call_requests')->where('id', $chat->id)->update([
    //             'call_status' => 'complete',
    //             'total_min' => $totalMin,
    //             'deduction' => $amount,
    //             'ended_at' => $endTime,
    //             'updated_at' => now()
    //         ]);

    //         // Notify Doctor that Chat is ended
    //         if ($doctor && !empty($doctor->fcm_token)) {
    //             $data = [
    //                 'title' => 'Chat Ended',
    //                 'description' => 'User has ended the chat.',
    //                 'type' => 'chat_ended',
    //                 'request_id' => $request->request_id,
    //             ];
    //             Helpers::send_push_notif_to_device($doctor->fcm_token, $data, $user->id, $doctor->id, 'Chat ended by user');
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
    if (!$request->request_id) {

        return response()->json([
            'status' => 'error',
            'message' => 'request_id is required'
        ], 422);
    }

    DB::beginTransaction();

    try {

        // Lock chat row
        $chat = DB::table('call_requests')
            ->where('id', $request->request_id)
            ->lockForUpdate()
            ->first();

        // Chat check
        if (!$chat || $chat->call_type !== 'chat') {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Chat not found'
            ], 404);
        }

        // Already completed
        if ($chat->call_status === 'complete') {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Chat already ended'
            ], 409);
        }

        // User and doctor lock
        $user = User::lockForUpdate()->find($chat->user_id);

        $doctor = User::lockForUpdate()->find($chat->doctor_id);

        if (!$user || !$doctor) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'User or Doctor not found'
            ], 404);
        }

        // ==================================
        // FIXED SLOT TIME BILLING
        // ==================================

        $endTime = now();

        // Only slot time deduction
        $totalMin = (int) ($chat->slot_time ?? 0);

        $callRate = (float) ($chat->call_rate ?? 0);

        $amount = $totalMin * $callRate;

        // Wallet balance check
        if ($user->wallet_amount < $amount) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        // Deduct from user
        $user->wallet_amount -= $amount;

        // Add to doctor
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

        // Update call request
        DB::table('call_requests')
            ->where('id', $chat->id)
            ->update([

                'call_status' => 'complete',

                // Fixed slot time
                'total_min' => $totalMin,

                'deduction' => $amount,

                'ended_at' => $endTime,

                'updated_at' => now()

            ]);

        // Push notification to doctor
        if (!empty($doctor->fcm_token)) {

            $data = [
                'title' => 'Chat Ended',
                'description' => 'User has ended the chat.',
                'type' => 'chat_ended',
                'request_id' => $request->request_id,
            ];

            Helpers::send_push_notif_to_device(
                $doctor->fcm_token,
                $data,
                $user->id,
                $doctor->id,
                'Chat ended by user'
            );
        }

        DB::commit();

        return response()->json([

            'status' => 'success',
            'message' => 'Chat ended successfully',

            'data' => [

                'charged_minutes' => $totalMin,
                'call_rate' => $callRate,
                'deducted_amount' => $amount

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
}
