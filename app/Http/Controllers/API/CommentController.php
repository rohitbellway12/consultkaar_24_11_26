<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Comment;
use App\Models\Appointment;
use App\Models\User;

class CommentController extends Controller
{
    /**
     * Add Comment + Rating
     */
    
public function addComment(Request $request)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|integer|exists:users,id,role_id,3',
            'comment'   => 'required|string|max:1000',
            'rating'    => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        // Sirf patients hi comment kar sakte hain
        if ($user->role_id != 2) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Only patients can comment on doctor profile'
            ]);
        }

        $comment = new Comment();
        $comment->patient_id = $user->id;
        $comment->doctor_id  = $request->doctor_id;
        $comment->comment    = $request->comment;
        $comment->rating     = $request->rating;
        $comment->save();

        $comment->load(['patient:id,name,image', 'doctor:id,name,image']);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Comment & rating added successfully',
            'data' => $comment
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'code' => 500,
            'message' => $e->getMessage()
        ]);
    }
}
    /**
     * Get comments for an appointment
     */
    public function getComments(Request $request, $appointment_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $appointment = Appointment::find($appointment_id);
            if (!$appointment) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Appointment not found'
                ]);
            }

            if ($user->id != $appointment->user_id && $user->id != $appointment->doctor_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Unauthorized'
                ]);
            }

            // Patient visibility control
            if ($user->role_id == 2) {
                $doctor = User::find($appointment->doctor_id);
                if ($doctor && !$doctor->show_comments_to_patients) {
                    return response()->json([
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Comments hidden by doctor',
                        'data' => []
                    ]);
                }
            }

            $comments = Comment::where('appointment_id', $appointment_id)
                ->with(['patient:id,name,image', 'doctor:id,name,image'])
                ->orderBy('created_at')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Comments fetched successfully',
                'data' => $comments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get Doctor Feed (Instagram style)
     */
    public function getDoctorComments(Request $request, $doctor_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if ($user->role_id != 2) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Only patients can view this'
                ]);
            }

            $doctor = User::where('id', $doctor_id)->where('role_id', 3)->first();
            if (!$doctor) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Doctor not found'
                ]);
            }

            if (!$doctor->show_comments_to_patients) {
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Comments hidden',
                    'data' => []
                ]);
            }

            $comments = Comment::where('doctor_id', $doctor_id)
                ->with(['patient:id,name,image'])
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Doctor comments fetched',
                'data' => $comments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update Comment + Rating
     */
    public function updateComment(Request $request, $comment_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $validator = Validator::make($request->all(), [
                'comment' => 'required|string|max:1000',
                'rating'  => 'nullable|integer|min:1|max:5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => $validator->errors()->first()
                ]);
            }

            $comment = Comment::find($comment_id);
            if (!$comment) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Comment not found'
                ]);
            }

            if ($user->id != $comment->patient_id && $user->id != $comment->doctor_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Unauthorized'
                ]);
            }

            $comment->comment = $request->comment;
            if ($request->filled('rating')) {
                $comment->rating = $request->rating;
            }

            $comment->save();
            $comment->load(['patient:id,name,image', 'doctor:id,name,image']);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Comment updated successfully',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete Comment
     */
    public function deleteComment($comment_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $comment = Comment::find($comment_id);
            if (!$comment) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Comment not found'
                ]);
            }

            if ($user->id != $comment->patient_id && $user->id != $comment->doctor_id) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Unauthorized'
                ]);
            }

            $comment->delete();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Comment deleted successfully'
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
