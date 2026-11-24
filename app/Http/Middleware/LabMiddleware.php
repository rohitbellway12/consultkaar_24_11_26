<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Lab; 
use Illuminate\Support\Facades\Session;

class LabMiddleware
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

        $labData = Session::get('lab_data');
      
     
        $labEmail =  !empty($labData) ? $labData->email : '';
        $labPassword =   Session::get('lab_password');
        // Retrieve user by email (assuming email is unique)
        $user = Lab::where('email', $labEmail)->first();
   
        // Check if user exists and password matches
        if ($user && Hash::check($labPassword, $user->password)) {
            // If user is valid, you might want to perform additional checks here
           
            // For example, you can check if the user has a specific role
            
            // Then you can proceed with the request
            return $next($request);
        } else {
          
            // If user is not valid, redirect to login page
            return redirect()->route('lab.auth.login')->with('error', 'Invalid credentials');
        }
    }
}
