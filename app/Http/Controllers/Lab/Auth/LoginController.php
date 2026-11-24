<?php

namespace App\Http\Controllers\Lab\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\Lab;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:lab', ['except' => 'logout']);
    }

    public function login()
    {      
      
       
        $settingData = BusinessSetting::where(['key'=>'logo'])->first();
        $systemLogo = (isset($settingData) && !empty($settingData)) ? $settingData->value : '';
       
        return view('lab-views.auth.login', compact('systemLogo'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $labData = Lab::where('email', $request->email)->first();    
        

 

      $password = $request->password;
    
    

    if ($labData && Hash::check($password, $labData->password)) {
       // dd($password);
        Session::put('lab_data', $labData);
        Session::put('lab_password', $request->password);
         //Auth::login($user);
         Session::put('lab_id', $labData->id);

         return redirect()->route('lab.dashboard');
  
    
} else {
    return redirect()->back()->withInput($request->only('email', 'remember'))
    ->withErrors(['Credentials does not match.']);
}
    }

    public function logout(Request $request)
    {
        auth()->guard('lab')->logout();
        return redirect()->route('lab.auth.login');
    }
}
