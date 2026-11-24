<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;


// class DoctorMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  \Closure  $next
//      * @return mixed
//      */
//     public function handle(Request $request, Closure $next)
//     {
    
//      if ($request->has('id') && preg_match('/android/i', $request->header('User-Agent'))) {
//             return redirect()->away('https://play.google.com/store/apps/details?id=com.consultkaar.userapp');
//         }

//         if (Auth::check() && Auth::user()->isDoctor()) {            
//             return $next($request);
//         } else {
//             auth()->guard('doctor')->logout();
//             return redirect()->route('doctor.auth.login');
//         }
      
//     }
// }
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DoctorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
    
     // Deep Linking: Only redirect guest (patient/user) Android visitors to Play Store.
        // Skip redirect if the request is coming from admin panel OR if a doctor is already logged in on web.
        $isAdminLoggedIn = Auth::guard('admin')->check();
        $isDoctorLoggedIn = Auth::check() && Auth::user()->isDoctor();
        $isAdminUrl = $request->is('admin/*') || $request->is('admin');

        if (
            $request->has('id') &&
            preg_match('/android/i', $request->header('User-Agent')) &&
            !$isAdminLoggedIn &&
            !$isDoctorLoggedIn &&
            !$isAdminUrl
        ) {
            return redirect()->away('https://play.google.com/store/apps/details?id=com.consultkaar.userapp');
        }

        if (Auth::check() && Auth::user()->isDoctor()) {            
            return $next($request);
        } else {
            auth()->guard('doctor')->logout();
            return redirect()->route('doctor.auth.login');
        }
      
    }
}
