<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIChatMessage;
use App\Models\AIChatSession;
use App\Models\AITrendingQuestion;
use App\Models\Speciality;
use App\Services\AIService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class AIChatAdminController extends Controller
{
    /**
     * Display AI Configuration Settings Page.
     */
    public function settings()
    {
        $config = AIService::getConfig();
        $totalSessions = AIChatSession::count();
        $activeSessions = AIChatSession::where('status', 'active')->count();
        $totalQuestions = AITrendingQuestion::count();

        return view('admin-views.ai.settings', compact('config', 'totalSessions', 'activeSessions', 'totalQuestions'));
    }

    /**
     * Update AI Configuration.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'api_endpoint' => 'nullable|string',
            'model' => 'required|string',
            'duration_minutes' => 'required|integer|min:1|max:60',
            'cooldown_hours' => 'required|integer|min:1|max:168',
            'max_messages' => 'required|integer|min:5|max:100',
            'recommendation_count' => 'required|integer|min:1|max:10',
            'recommendation_filter' => 'required|in:rating,latest,random',
            'system_prompt' => 'required|string',
        ]);

        $data = [
            'status' => $request->has('status') ? (int)$request->status : 0,
            'api_key' => trim($request->api_key ?? ''),
            'api_endpoint' => trim($request->api_endpoint ?? 'https://api.openai.com/v1'),
            'model' => trim($request->model),
            'duration_minutes' => (int)$request->duration_minutes,
            'cooldown_hours' => (int)$request->cooldown_hours,
            'max_messages' => (int)$request->max_messages,
            'recommendation_count' => (int)$request->recommendation_count,
            'recommendation_filter' => $request->recommendation_filter,
            'system_prompt' => trim($request->system_prompt),
        ];

        AIService::saveConfig($data);

        Toastr::success('AI Assistant settings updated successfully!');
        return back();
    }

    /**
     * Display Trending / Frequently Asked Questions.
     */
    public function questions(Request $request)
    {
        $query = AITrendingQuestion::with('speciality');

        if ($request->has('search') && !empty($request->search)) {
            $query->where('question', 'like', "%{$request->search}%");
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        $questions = $query->orderBy('priority', 'asc')->latest()->paginate(15);
        $specialities = Speciality::where('status', 1)->orderBy('name', 'asc')->get();

        return view('admin-views.ai.questions', compact('questions', 'specialities'));
    }

    /**
     * Store a new Trending Question.
     */
    public function storeQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'category_id' => 'nullable|integer',
            'priority' => 'nullable|integer',
        ]);

        AITrendingQuestion::create([
            'question' => trim($request->question),
            'category_id' => $request->category_id ? (int)$request->category_id : null,
            'priority' => (int)($request->priority ?? 0),
            'status' => 1,
        ]);

        Toastr::success('Trending Question added successfully!');
        return back();
    }

    /**
     * Update an existing Trending Question.
     */
    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'category_id' => 'nullable|integer',
            'priority' => 'nullable|integer',
        ]);

        $item = AITrendingQuestion::findOrFail($id);
        $item->update([
            'question' => trim($request->question),
            'category_id' => $request->category_id ? (int)$request->category_id : null,
            'priority' => (int)($request->priority ?? 0),
        ]);

        Toastr::success('Trending Question updated successfully!');
        return back();
    }

    /**
     * Toggle status of a Trending Question.
     */
    public function statusQuestion($id, $status)
    {
        $item = AITrendingQuestion::findOrFail($id);
        $item->status = (int)$status;
        $item->save();

        Toastr::success('Question status updated successfully!');
        return back();
    }

    /**
     * Delete a Trending Question.
     */
    public function deleteQuestion($id)
    {
        $item = AITrendingQuestion::findOrFail($id);
        $item->delete();

        Toastr::success('Trending Question deleted successfully!');
        return back();
    }

    /**
     * List user AI Chat Sessions & Logs.
     */
    public function sessions(Request $request)
    {
        $query = AIChatSession::with(['user', 'speciality']);

        if ($request->has('status') && in_array($request->status, ['active', 'expired', 'completed'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                  ->orWhere('l_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $sessions = $query->latest()->paginate(20);

        return view('admin-views.ai.sessions', compact('sessions'));
    }

    /**
     * View detailed transcript of an AI Chat Session.
     */
    public function sessionDetails($id)
    {
        $session = AIChatSession::with(['user', 'speciality', 'messages'])->findOrFail($id);
        return view('admin-views.ai.session-details', compact('session'));
    }
}
