<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDoctorApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->role_id == '3') {
            if ($user->status == 0) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Your account is pending approval. Please wait for admin to review your profile.',
                    'account_status' => 'pending_approval'
                ], 403);
            }

            if ($user->status == 2) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Your account has been rejected. Reason: ' . ($user->rejection_reason ?? 'Please contact support.'),
                    'account_status' => 'rejected'
                ], 403);
            }

            if ($user->status != 1 || $user->is_active != 1) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Your account is not active. Please contact support.',
                    'account_status' => 'inactive'
                ], 403);
            }
        }

        return $next($request);
    }
}
