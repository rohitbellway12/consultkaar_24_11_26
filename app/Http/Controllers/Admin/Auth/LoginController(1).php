<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => 'logout']);
    }

    public function login()
    {      
      
       
        $settingData = BusinessSetting::where(['key'=>'logo'])->first();
        $systemLogo = (isset($settingData) && !empty($settingData)) ? $settingData->value : '';
       
        return view('admin-views.auth.login', compact('systemLogo'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
            
        $credentials = $request->only('email', 'password');
        
        
        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            if ($user->role_id == '1') { // Check if user has admin role
                Session::put('doctor_role_id','');
                Session::put('doctor_id', '');
                
                return redirect()->route('admin.dashboard');
            } else {
                Auth::guard('admin')->logout();
                return redirect()->back()->withErrors(['Credentials do not match.']);
            }
        }


        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Credentials does not match.']);
    }

    public function logout(Request $request)
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.auth.login');
    }
}
