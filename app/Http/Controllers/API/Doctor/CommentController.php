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
     * Add a comment.
     * Can be for a specific appointment OR directly on a doctor's profile.
     */
    public function addComment(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            
            // वैलिडेशन: या तो appointment_id हो या doctor_id, दोनों में से एक जरूरी है
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required_without:doctor_id|integer|exists:appointments,id',
                'doctor_id' => 'required_without:appointment_id|integer|exists:users,id,role_id,3',
                'comment' => 'required|string|max:1000',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
            }

            $comment = new Comment();
            $comment->comment = $request->comment;

            // --- केस 1: अपॉइंटमेंट पर कमेंट करना (पुराना तरीका) ---
            if ($request->has('appointment_id')) {
                $appointment = Appointment::find($request->appointment_id);
                
                // सुरक्षा चेक: क्या यूजर इस अपॉइंटमेंट का हिस्सा है?
                if ($user->role_id != 2 && $user->id != $appointment->doctor_id) {
                    return response()->json(['status' => 'error', 'code' => 403, 'message' => 'You are not authorized to comment on this appointment']);
                }
                
                $comment->appointment_id = $request->appointment_id;
                $comment->patient_id = $appointment->user_id;
                $comment->doctor_id = $appointment->doctor_id;
            } 
            // --- केस 2: डॉक्टर की प्रोफाइल पर सीधे कमेंट करना (नया तरीका) ---
            else {
                // केवल पेशेंट ही किसी डॉक्टर की प्रोफाइल पर कमेंट कर सकता है
                if ($user->role_id != 2) {
                    return response()->json(['status' => 'error', 'code' => 403, 'message' => 'Only patients can comment on a doctor\'s profile.']);
                }

                $comment->appointment_id = null; // कोई अपॉइंटमेंट नहीं
                $comment->patient_id = $user->id; // कमेंट करने वाला पेशेंट
                $comment->doctor_id = $request->doctor_id; // जिस डॉक्टर पर कमेंट किया जा रहा है
            }
            
            $comment->save();
            
            // रिलेशनशिप के साथ डेटा भेजें
            $comment->load(['patient:id,name,image', 'doctor:id,name,image']);
            
            return response()->json([
                'status' => 'success', 
                'code' => 200, 
                'message' => 'Comment added successfully.', 
                'data' => $comment
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Get comments for a specific appointment.
     */
    public function getComments(Request $request, $appointment_id)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            
            $appointment = Appointment::find($appointment_id);
            
            if (!$appointment) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Appointment not found']);
            }
            
            if ($user->role_id != 2 && $user->id != $appointment->doctor_id) {
                return response()->json(['status' => 'error', 'code' => 403, 'message' => 'You are not authorized to view comments for this appointment']);
            }
            
            // डॉक्टर का मास्टर स्विच चेक करें (केवल पेशेंट के लिए)
            if ($user->role_id == 2) {
                $doctor = User::find($appointment->doctor_id);
                if ($doctor && !$doctor->show_comments_to_patients) {
                    return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Comments retrieved successfully.', 'data' => []]);
                }
            }
            
            $comments = Comment::where('appointment_id', $appointment_id)
                ->with(['patient:id,name,image', 'doctor:id,name,image'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Comments retrieved successfully', 'data' => $comments]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Get ALL comments for a specific doctor (Instagram-like feed).
     * This includes both appointment-wise and direct profile comments.
     */
    public function getDoctorComments(Request $request, $doctor_id)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);

            if ($user->role_id != 2) {
                return response()->json(['status' => 'error', 'code' => 403, 'message' => 'Only patients can view this feed.']);
            }

            $doctor = User::find($doctor_id);

            if (!$doctor || $doctor->role_id != 3) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Doctor not found.']);
            }

            // डॉक्टर का मास्टर स्विच
            if (!$doctor->show_comments_to_patients) {
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Comments retrieved successfully.', 'data' => []]);
            }

            // डॉक्टर के सभी कमेंट्स लाएं (appointment_id के साथ या बिना)
            $comments = Comment::where('doctor_id', $doctor_id)
                ->with(['patient:id,name,image', 'doctor:id,name,image'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Comments retrieved successfully', 'data' => $comments]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Update a comment (by the author)
     */
    public function updateComment(Request $request, $comment_id)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string|max:1000',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
            }
            
            $comment = Comment::find($comment_id);
            
            if (!$comment) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Comment not found']);
            }
            
            // लेखक ही अपडेट कर सकता है
            if ($user->id != $comment->patient_id && $user->id != $comment->doctor_id) {
                return response()->json(['status' => 'error', 'code' => 403, 'message' => 'You are not authorized to update this comment']);
            }
            
            $comment->comment = $request->comment;
            $comment->save();
            
            $comment->load(['patient:id,name,image', 'doctor:id,name,image']);
            
            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Comment updated successfully', 'data' => $comment]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Delete a comment (by the author)
     */
    public function deleteComment($comment_id)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            
            $comment = Comment::find($comment_id);
            
            if (!$comment) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Comment not found']);
            }
            
            // लेखक ही डिलीट कर सकता है
            if ($user->id != $comment->patient_id && $user->id != $comment->doctor_id) {
                return response()->json(['status' => 'error', 'code' => 403, 'message' => 'You are not authorized to delete this comment']);
            }
            
            $comment->delete();
            
            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Comment deleted successfully', 'data' => null]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
}