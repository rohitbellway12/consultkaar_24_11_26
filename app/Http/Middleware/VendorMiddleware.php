<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        
        if (Auth::guard('vendor')->check()) {
            $vendor = Auth::guard('vendor')->user();
          
            if ($vendor->role_id === "4" ) {
                return $next($request);
            }
            Auth::guard('vendor')->logout();
            return redirect()->route('auth.login')->withErrors(['error' => 'Access forbidden or inactive vendor']);
        }

        if (Auth::guard('vendor_employee')->check()) {
            $employee = Auth::guard('vendor_employee')->user();

            if (!$employee->restaurant->status) {
                Auth::guard('vendor_employee')->logout();
                return redirect()->route('auth.login')->withErrors(['error' => 'Your restaurant is inactive.']);
            }
            return $next($request);
        }
        return redirect()->route('auth.login')->withErrors(['error' => 'Please log in first.']);
    }
}
