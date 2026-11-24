<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

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
            'user_id'     => $user->id,
            'doctor_id'   => $doctor->id,
            'call_type'   => 'chat',
            'call_status' => 'progress',
            'call_rate'   => $doctor->chat_fee,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'request_id' => $requestId
        ]);
    }

    public function endChat(Request $request)
    {
        if (!$request->request_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'request_id is required'
            ], 422);
        }

        $chat = DB::table('call_requests')->where('id', $request->request_id)->first();

        if (!$chat || $chat->call_type !== 'chat') {
            return response()->json([
                'status' => 'error',
                'message' => 'Chat not found'
            ], 404);
        }

        if ($chat->call_status === 'complete') {
            return response()->json([
                'status' => 'error',
                'message' => 'Chat already ended'
            ], 409);
        }

        DB::table('call_requests')->where('id', $chat->id)->update([
            'call_status' => 'complete',
            'total_min'   => $request->total_min ?? 0,
            'deduction'   => $request->amount_used ?? 0,
            'updated_at'  => now()
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Chat ended successfully'
        ]);
    }
}
