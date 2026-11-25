<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AIChatMessage;
use App\Models\AIChatSession;
use App\Models\AITrendingQuestion;
use App\Models\Speciality;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AIChatController extends Controller
{
    /**
     * Get currently authenticated user safely from JWT.
     */
    protected function getAuthUser()
    {
        try {
            if ($token = JWTAuth::getToken()) {
                return JWTAuth::toUser($token);
            }
        } catch (\Exception $e) {
            // Token absent or expired
        }
        return null;
    }

    /**
     * AI Assistant Home Screen Data.
     * GET /api/v1/ai/home
     */
    public function home(Request $request)
    {
        $config = AIService::getConfig();
        $user = $this->getAuthUser();

        $canStartChat = true;
        $cooldownRemainingSeconds = 0;
        $activeSession = null;

        if ($user) {
            // Check if user currently has an active ongoing session
            $ongoing = AIChatSession::where('user_id', $user->id)
                ->where('status', 'active')
                ->latest()
                ->first();

            if ($ongoing) {
                if ($ongoing->isExpired()) {
                    $ongoing->update(['status' => 'expired']);
                } else {
                    $activeSession = [
                        'session_uuid' => $ongoing->session_uuid,
                        'remaining_seconds' => $ongoing->remainingSeconds(),
                        'category_id' => $ongoing->category_id,
                        'started_at' => $ongoing->started_at,
                    ];
                }
            }

            // Check 24-hour restriction / cooldown from last completed or expired session
            $cooldownHours = (int)($config['cooldown_hours'] ?? 24);
            $lastSession = AIChatSession::where('user_id', $user->id)
                ->whereIn('status', ['expired', 'completed'])
                ->latest()
                ->first();

            if ($lastSession && $lastSession->started_at) {
                $cooldownEndsAt = $lastSession->started_at->copy()->addHours($cooldownHours);
                if (now()->lessThan($cooldownEndsAt)) {
                    $canStartChat = false;
                    $cooldownRemainingSeconds = now()->diffInSeconds($cooldownEndsAt, false);
                }
            }
        }

        // Fetch Specialities / Topics
        $specialities = Speciality::where('status', 1)
            ->select('id', 'name', 'image')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($spec) {
                $imagePath = (env('APP_ENV') == 'local') ? asset('storage/speciality/' . $spec->image) : asset('storage/app/public/speciality/' . $spec->image);
                return [
                    'id' => $spec->id,
                    'name' => $spec->name,
                    'image' => $spec->image ? $imagePath : null,
                ];
            });

        // Fetch Trending Questions
        $trendingQuestions = AITrendingQuestion::with('speciality:id,name')
            ->where('status', 1)
            ->orderBy('priority', 'asc')
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'speciality_id' => $q->category_id,
                    'speciality_name' => $q->speciality->name ?? 'General',
                ];
            });

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'ai_service_enabled' => AIService::isEnabled(),
                'model' => $config['model'] ?? 'gpt-6-luna',
                'free_duration_minutes' => (int)($config['duration_minutes'] ?? 5),
                'cooldown_hours' => (int)($config['cooldown_hours'] ?? 24),
                'can_start_chat' => $canStartChat,
                'cooldown_remaining_seconds' => max(0, $cooldownRemainingSeconds),
                'active_session' => $activeSession,
                'categories' => $specialities,
                'specialities' => $specialities,
                'trending_questions' => $trendingQuestions,
            ],
        ]);
    }

    /**
     * Start a 5-minute Free AI Chat Session.
     * POST /api/v1/ai/start-chat
     */
    public function startChat(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Please log in to start your 5-minute free AI consultation.',
            ], 401);
        }

        if (!AIService::isEnabled()) {
            return response()->json([
                'status' => 'error',
                'code' => 503,
                'message' => 'AI Assistant service is currently undergoing maintenance. Please try again shortly.',
            ], 503);
        }

        $config = AIService::getConfig();
        $cooldownHours = (int)($config['cooldown_hours'] ?? 24);

        // Check if user has an existing active unexpired session, reuse it
        $ongoing = AIChatSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($ongoing) {
            if (!$ongoing->isExpired()) {
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Active AI session resumed.',
                    'data' => [
                        'session_uuid' => $ongoing->session_uuid,
                        'category_id' => $ongoing->category_id,
                        'remaining_seconds' => $ongoing->remainingSeconds(),
                        'started_at' => $ongoing->started_at,
                        'expires_at' => $ongoing->expires_at,
                    ],
                ]);
            }
            $ongoing->update(['status' => 'expired']);
        }

        // Enforce 24-Hour Restriction
        $lastSession = AIChatSession::where('user_id', $user->id)
            ->whereIn('status', ['expired', 'completed'])
            ->latest()
            ->first();

        if ($lastSession && $lastSession->started_at) {
            $cooldownEndsAt = $lastSession->started_at->copy()->addHours($cooldownHours);
            if (now()->lessThan($cooldownEndsAt)) {
                $remainingSeconds = now()->diffInSeconds($cooldownEndsAt, false);
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => "You have already used your free 5-minute AI consultation. You can consult again in " . ceil($remainingSeconds / 3600) . " hours.",
                    'data' => [
                        'can_start_chat' => false,
                        'cooldown_remaining_seconds' => max(0, $remainingSeconds),
                    ],
                ], 403);
            }
        }

        $durationMinutes = (int)($config['duration_minutes'] ?? 5);
        $raw = json_decode($request->getContent(), true) ?? [];
        $specId = $request->input('speciality_id') ?? $request->input('category_id') ?? ($raw['speciality_id'] ?? ($raw['category_id'] ?? null));
        $speciality = null;
        if ($specId) {
            $speciality = Speciality::find($specId);
        }

        // Create new session
        $session = AIChatSession::create([
            'session_uuid' => (string)Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $speciality ? $speciality->id : null,
            'started_at' => now(),
            'expires_at' => now()->addMinutes($durationMinutes),
            'status' => 'active',
        ]);

        $specialityName = $speciality ? $speciality->name : 'General Consultation';
        $welcomeMessage = "Hello " . ($user->name ?? 'there') . "! I am your AI Consultation Assistant powered by GPT-6 Luna. You have a 5-minute free consultation regarding {$specialityName}. How can I help you today?";

        // Record initial assistant welcome
        AIChatMessage::create([
            'session_id' => $session->id,
            'user_id' => $user->id,
            'sender' => 'ai',
            'message' => $welcomeMessage,
            'tokens_used' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => '5-minute AI Consultation session started.',
            'data' => [
                'session_uuid' => $session->session_uuid,
                'category_id' => $session->category_id,
                'category_name' => $specialityName,
                'remaining_seconds' => $session->remainingSeconds(),
                'started_at' => $session->started_at,
                'expires_at' => $session->expires_at,
                'welcome_message' => $welcomeMessage,
            ],
        ]);
    }

    /**
     * Send message to AI and receive response.
     * POST /api/v1/ai/send-message
     */
    public function sendMessage(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $raw = json_decode($request->getContent(), true) ?? [];
        $sessionKey = $request->input('session_uuid') ?? $request->input('session_id') ?? ($raw['session_uuid'] ?? ($raw['session_id'] ?? null));
        if (!$sessionKey) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => 'The session_uuid or session_id field is required.',
            ], 422);
        }

        $messageText = trim((string)($request->input('message') ?? ($raw['message'] ?? '')));
        if (empty($messageText)) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => 'The message field is required.'], 422);
        }

        $session = AIChatSession::with('category')
            ->where(function ($q) use ($sessionKey) {
                $q->where('session_uuid', $sessionKey)->orWhere('id', $sessionKey);
            })
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Chat session not found.'], 404);
        }

        // Check 5-Minute Timer Expiration
        if ($session->isExpired()) {
            $endData = $this->completeSessionAndRecommend($session);

            return response()->json([
                'status' => 'expired',
                'code' => 200,
                'is_expired' => true,
                'message' => 'Your 5-minute free consultation has ended. Please connect with our verified human experts for detailed advice.',
                'data' => [
                    'remaining_seconds' => 0,
                    'summary' => $endData['summary'],
                    'recommended_advisors' => $endData['advisors'],
                ],
            ]);
        }

        $config = AIService::getConfig();
        $maxMessages = (int)($config['max_messages'] ?? 30);
        $userMessageCount = AIChatMessage::where('session_id', $session->id)->where('sender', 'user')->count();

        if ($userMessageCount >= $maxMessages) {
            return response()->json([
                'status' => 'error',
                'code' => 429,
                'message' => 'Message limit reached for this 5-minute session.',
            ], 429);
        }

        // Save User Message
        AIChatMessage::create([
            'session_id' => $session->id,
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => $messageText,
        ]);

        // Build recent message history for context (last 8 messages)
        $history = AIChatMessage::where('session_id', $session->id)
            ->orderBy('id', 'desc')
            ->take(8)
            ->get()
            ->reverse();

        $formatted = [];
        foreach ($history as $h) {
            $formatted[] = [
                'role' => $h->sender === 'ai' ? 'assistant' : 'user',
                'content' => $h->message,
            ];
        }

        // Call GPT-6 Luna AI Service
        $categoryName = $session->category->name ?? 'General Consultation';
        $aiResult = AIService::sendChat($formatted, $categoryName);

        if (!$aiResult['success']) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $aiResult['error'] ?? 'AI service temporarily unavailable. Please try again.',
            ], 500);
        }

        $aiReply = $aiResult['reply'];

        // Save AI Reply
        AIChatMessage::create([
            'session_id' => $session->id,
            'user_id' => $user->id,
            'sender' => 'ai',
            'message' => $aiReply,
            'tokens_used' => (int)($aiResult['tokens'] ?? 0),
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'session_uuid' => $session->session_uuid,
                'ai_reply' => $aiReply,
                'remaining_seconds' => $session->remainingSeconds(),
                'is_expired' => false,
            ],
        ]);
    }

    /**
     * Complete Session, Generate Summary & Fetch Recommended Advisors.
     * POST /api/v1/ai/end-chat
     */
    public function endChat(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $sessionKey = $request->input('session_uuid') ?? $request->input('session_id');
        if (!$sessionKey) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => 'The session_uuid or session_id field is required.'], 422);
        }

        $session = AIChatSession::with('category')
            ->where(function ($q) use ($sessionKey) {
                $q->where('session_uuid', $sessionKey)->orWhere('id', $sessionKey);
            })
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Session not found'], 404);
        }

        $session->update(['status' => 'completed']);
        $endData = $this->completeSessionAndRecommend($session);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Session completed.',
            'data' => [
                'session_uuid' => $session->session_uuid,
                'summary' => $endData['summary'],
                'recommended_advisors' => $endData['advisors'],
            ],
        ]);
    }

    /**
     * Helper to generate summary and retrieve recommended doctors.
     */
    protected function completeSessionAndRecommend(AIChatSession $session)
    {
        $session->update(['status' => 'expired']);

        $messages = AIChatMessage::where('session_id', $session->id)
            ->orderBy('id', 'asc')
            ->get(['sender', 'message'])
            ->toArray();

        $categoryName = $session->category->name ?? 'General Consultation';

        // 1. Generate Clinical Summary
        $summaryData = AIService::generateSummary($messages, $categoryName);
        $summary = $summaryData['summary'] ?? "User consulted for {$categoryName}.";

        // 2. Fetch Matching Advisors
        $config = AIService::getConfig();
        $count = (int)($config['recommendation_count'] ?? 3);
        $filter = $config['recommendation_filter'] ?? 'rating';

        $query = User::where('role_id', 3)->where('status', 1);

        if ($session->category_id) {
            $query->where('speciality_id', $session->category_id);
        }

        if ($filter === 'latest') {
            $query->latest();
        } elseif ($filter === 'random') {
            $query->inRandomOrder();
        } else {
            // Default: highest experience / most booked
            $query->orderBy('order_count', 'desc')->latest();
        }

        $advisors = $query->take($count)->get();

        // If not enough doctors found in specific category, fetch top general active doctors
        if ($advisors->count() < $count) {
            $needed = $count - $advisors->count();
            $existingIds = $advisors->pluck('id')->toArray();
            $fallback = User::where('role_id', 3)
                ->where('status', 1)
                ->whereNotIn('id', $existingIds)
                ->orderBy('order_count', 'desc')
                ->latest()
                ->take($needed)
                ->get();
            $advisors = $advisors->merge($fallback);
        }

        // Store recommended advisor IDs in session
        $advisorIds = $advisors->pluck('id')->toArray();
        $session->update([
            'summary' => $summary,
            'recommended_advisor_ids' => $advisorIds,
        ]);

        $formattedAdvisors = $advisors->map(function ($doc) {
            $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $doc->image) : asset('storage/app/public/users/' . $doc->image);
            return [
                'id' => $doc->id,
                'name' => $doc->name ?? 'Advisor',
                'email' => $doc->email,
                'phone' => $doc->mobile_number ?? $doc->phone,
                'image' => $doc->image ? $imagePath : null,
                'rating' => (float)($doc->avg_rating ?? 5.0),
                'experience' => $doc->experience ?? 'Expert',
                'consultation_fee' => (float)($doc->consultation_fee ?? $doc->price ?? 0),
                'speciality_id' => $doc->speciality_id,
            ];
        });

        return [
            'summary' => $summary,
            'advisors' => $formattedAdvisors,
        ];
    }

    /**
     * User's past AI consultations.
     * GET /api/v1/ai/history
     */
    public function history(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $sessions = AIChatSession::with('category:id,name')
            ->where('user_id', $user->id)
            ->withCount('messages')
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $sessions,
        ]);
    }

    /**
     * Get full message transcript for a session.
     * GET /api/v1/ai/session-messages
     */
    public function sessionMessages(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $sessionKey = $request->input('session_uuid') ?? $request->input('session_id');
        if (!$sessionKey) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => 'The session_uuid or session_id is required'], 422);
        }

        $session = AIChatSession::with(['category:id,name', 'messages'])
            ->where(function ($q) use ($sessionKey) {
                $q->where('session_uuid', $sessionKey)->orWhere('id', $sessionKey);
            })
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Session not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'session' => [
                    'session_uuid' => $session->session_uuid,
                    'status' => $session->status,
                    'category' => $session->category->name ?? 'General',
                    'started_at' => $session->started_at,
                    'summary' => $session->summary,
                ],
                'messages' => $session->messages->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'sender' => $m->sender,
                        'message' => $m->message,
                        'created_at' => $m->created_at,
                    ];
                }),
            ],
        ]);
    }
}
