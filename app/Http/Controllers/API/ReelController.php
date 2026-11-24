<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\CentralLogics\Helpers;
use App\Models\Reel;
use App\Models\ReelView;
use App\Models\ReelValue;
use App\Models\ReelComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReelController extends Controller
{
    /**
     * Public reel feed + interactions.
     * These endpoints are available to any authenticated user (patient or advisor).
     */

    /**
     * Get the authenticated user, if any (null when not authenticated).
     */
    private function currentUser()
    {
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                return JWTAuth::toUser($token);
            }
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }

    private function advisorImageUrl($image)
    {
        if (!empty($image)) {
            return (env('APP_ENV') == 'local')
                ? asset('storage/users/' . $image)
                : asset('storage/app/public/users/' . $image);
        }
        return asset('public/assets/admin/img/160x160/img1.jpg');
    }

    /**
     * Transform a reel into the public feed payload:
     * includes advisor (uploader) details and the current user's like state.
     */
    private function transformReel($reel, $userId)
    {
        $advisor = $reel->advisor ?? User::find($reel->user_id);

        $liked = false;
        if ($userId) {
            $liked = ReelValue::where('reel_id', $reel->id)
                ->where('user_id', $userId)
                ->exists();
        }

        return [
            'id' => $reel->id,
            'title' => $reel->title,
            'description' => $reel->description,
            'category' => $reel->category,
            'video' => $this->reelVideoUrl($reel->getRawOriginal('video')),
            'views_count' => (int) $reel->views_count,
            'value_count' => (int) $reel->value_count,
            'comments_count' => (int) $reel->comments_count,
            'shares_count' => (int) $reel->shares_count,
            'is_liked' => $liked,
            'created_at' => $reel->created_at,
            'updated_at' => $reel->updated_at,
            'advisor' => $advisor ? [
                'id' => $advisor->id,
                'name' => $advisor->name,
                'image' => $this->advisorImageUrl($advisor->image),
            ] : null,
        ];
    }

    private function reelVideoUrl($video)
    {
        if (!$video) {
            return null;
        }
        return (env('APP_ENV') == 'local')
            ? asset('storage/reels/' . $video)
            : asset('storage/app/public/reels/' . $video);
    }

    /**
     * List all supported categories (public reference).
     */
    public function getCategories()
    {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Categories retrieved successfully',
                'data' => Reel::CATEGORIES,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Public reel feed: all published reels with advisor details.
     * Optional filters: ?category=<category>&user_id=<advisor_id>
     *  - category -> filter reels category-wise
     *  - user_id  -> view all reels uploaded by that advisor
     */
    public function reelList(Request $request)
    {
        try {
            $user = $this->currentUser();
            $userId = $user ? $user->id : null;

            $validator = Validator::make($request->all(), [
                'category' => 'nullable|string|in:' . implode(',', Reel::CATEGORIES),
                'user_id' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $query = Reel::where('status', 1);

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $reels = $query->orderBy('created_at', 'desc')->get();

            $data = $reels->map(function ($reel) use ($userId) {
                return $this->transformReel($reel, $userId);
            })->values();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Reel list retrieved successfully',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Public single reel detail (includes advisor details + like state).
     */
    public function reelDetail(Request $request)
    {
        try {
            $user = $this->currentUser();
            $userId = $user ? $user->id : null;

            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $reel = Reel::where('id', $request->reel_id)->first();

            if (!$reel) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Reel not found', 'data' => null], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Reel detail retrieved successfully',
                'data' => $this->transformReel($reel, $userId),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Track a view. Call once after the viewer has watched for ~5 seconds.
     */
    public function incrementReelView(Request $request)
    {
        try {
            $user = $this->currentUser();
            $userId = $user ? $user->id : null;

            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            ReelView::create([
                'reel_id' => $request->reel_id,
                'user_id' => $userId,
                'ip_address' => $request->ip(),
            ]);

            Reel::where('id', $request->reel_id)->increment('views_count');

            $reel = Reel::find($request->reel_id);
            if ($reel) {
                $reel->checkAndNotifyAdminForEarningCriteria();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'View recorded successfully',
                'data' => null,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Like / unlike a reel (the "Value" / "V" metric). One value per user.
     */
    public function addReelValue(Request $request)
    {
        try {
            $user = $this->currentUser();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
                'action' => 'required|in:add,remove',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $existing = ReelValue::where('reel_id', $request->reel_id)
                ->where('user_id', $user->id)
                ->first();

            if ($request->action === 'add') {
                if ($existing) {
                    return response()->json(['status' => 'error', 'code' => 409, 'message' => 'Value already added'], 409);
                }

                ReelValue::create([
                    'reel_id' => $request->reel_id,
                    'user_id' => $user->id,
                ]);

                Reel::where('id', $request->reel_id)->increment('value_count');

                $reel = Reel::find($request->reel_id);
                if ($reel) {
                    $reel->checkAndNotifyAdminForEarningCriteria();
                }

                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Value added successfully',
                    'data' => ['action' => 'added'],
                ]);
            }

            // action === remove
            if (!$existing) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'No value found to remove'], 404);
            }

            $existing->delete();
            Reel::where('id', $request->reel_id)->decrement('value_count');

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Value removed successfully',
                'data' => ['action' => 'removed'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Track a share.
     */
    // public function addReelShare(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'reel_id' => 'required|exists:reels,id',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
    //         }

    //         Reel::where('id', $request->reel_id)->increment('shares_count');

    //         return response()->json([
    //             'status' => 'success',
    //             'code' => 200,
    //             'message' => 'Share recorded successfully',
    //             'data' => null,
    //         ]);
    //     } catch (\Throwable $e) {
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
    //     }
    // }
    
    
    public function addReelShare(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
            ]);
 
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }
 
            Reel::where('id', $request->reel_id)->increment('shares_count');
 
            $shareUrl = url('/reel/' . $request->reel_id);
 
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Share recorded successfully',
                'data' => [
                    'share_url' => $shareUrl
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Add a comment to a reel.
     */
    public function addComment(Request $request)
    {
        try {
            $user = $this->currentUser();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
                'comment' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $comment = ReelComment::create([
                'reel_id' => $request->reel_id,
                'user_id' => $user->id,
                'comment' => $request->comment,
            ]);

            Reel::where('id', $request->reel_id)->increment('comments_count');

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Comment added successfully',
                'data' => $comment->load('user:id,name,image'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List comments for a reel.
     */
    public function getComments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reel_id' => 'required|exists:reels,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $comments = ReelComment::where('reel_id', $request->reel_id)
                ->whereNull('parent_id')
                ->with(['user:id,name,image', 'replies.user:id,name,image'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Comments retrieved successfully',
                'data' => $comments,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Add a reply to a comment.
     */
    public function addReelReply(Request $request)
    {
        try {
            $user = $this->currentUser();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'comment_id' => 'required|exists:reel_comments,id',
                'reply' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $parentComment = ReelComment::find($request->comment_id);

            $reply = ReelComment::create([
                'reel_id' => $parentComment->reel_id,
                'user_id' => $user->id,
                'comment' => $request->reply,
                'parent_id' => $parentComment->id,
            ]);

            Reel::where('id', $parentComment->reel_id)->increment('comments_count');

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Reply added successfully',
                'data' => $reply->load('user:id,name,image'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List replies for a specific comment.
     */
    public function getReelReplies(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required|exists:reel_comments,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $replies = ReelComment::where('parent_id', $request->comment_id)
                ->with('user:id,name,image')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Replies retrieved successfully',
                'data' => $replies,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a comment (author only).
     */
    public function deleteComment(Request $request)
    {
        try {
            $user = $this->currentUser();

            if (!$user) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'comment_id' => 'required|exists:reel_comments,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()], 422);
            }

            $comment = ReelComment::where('id', $request->comment_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$comment) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Comment not found'], 404);
            }

            $reelId = $comment->reel_id;
            $comment->delete();

            Reel::where('id', $reelId)->decrement('comments_count');
            Reel::where('id', $reelId)->where('comments_count', '<', 0)->update(['comments_count' => 0]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Comment deleted successfully',
                'data' => null,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
