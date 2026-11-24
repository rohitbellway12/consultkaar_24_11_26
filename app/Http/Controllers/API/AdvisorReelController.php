<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\CentralLogics\Helpers;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdvisorReelController extends Controller
{
    /**
     * Advisor-only reel management:
     * upload, list own reels, delete own reel, view own reel performance.
     */

    private function advisor()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return null;
        }
        try {
            return JWTAuth::toUser($token);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Upload a reel (video + title/description + category).
     */
    public function addReel(Request $request)
    {
        try {
            $user = $this->advisor();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'video' => 'required|file|mimes:mp4,mov,avi,mkv,webm|max:102400',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string|in:' . implode(',', Reel::CATEGORIES),
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $format = $request->file('video')->getClientOriginalExtension();
            $videoName = Helpers::upload('reels/', $format, $request->file('video'));

            $reel = Reel::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'video' => $videoName,
                'status' => 1,
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Reel uploaded successfully',
                'data' => $reel,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List the advisor's own reels.
     */
    public function getReels(Request $request)
    {
        try {
            $user = $this->advisor();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $reels = Reel::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Reels retrieved successfully',
                'data' => $reels,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a reel (owner only).
     */
    public function deleteReel(Request $request)
    {
        try {
            $user = $this->advisor();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $reel = Reel::where('id', $request->reel_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$reel) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Reel not found'], 404);
            }

            if ($reel->video && Storage::disk('public')->exists('reels/' . $reel->video)) {
                Storage::disk('public')->delete('reels/' . $reel->video);
            }

            $reel->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Reel deleted successfully',
                'data' => null,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Advisor's performance metrics for their own reel.
     */
    public function getReelPerformance(Request $request)
    {
        try {
            $user = $this->advisor();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $reel = Reel::where('id', $request->reel_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$reel) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Reel not found'], 404);
            }

            $data = [
                'reel_id' => $reel->id,
                'title' => $reel->title,
                'total_views' => (int) $reel->views_count,
                'total_value' => (int) $reel->value_count,
                'total_comments' => (int) $reel->comments_count,
                'total_shares' => (int) $reel->shares_count,
            ];

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Reel performance retrieved successfully',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
