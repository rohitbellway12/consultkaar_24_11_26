<?php

namespace App\Http\Controllers\Doctor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\User;
use App\CentralLogics\SMS_module;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
         $this->middleware('guest:doctor', ['except' => 'logout']);
    }

    public function login()
    {      
        $settingData = BusinessSetting::where('key', 'logo')->first();
        $systemLogo = $settingData ? $settingData->value : '';
       
        return view('doctor-views.auth.login', compact('systemLogo'));
    }

    public function submit(Request $request)
{
    $request->validate([
        'mobile_number' => 'required|numeric',
    ]);

    // Check if the mobile number exists in the users table
    $user = User::where('mobile_number', $request->mobile_number)->where('role_id', 3)->first();    
    if (!$user) {
        return redirect()->back()->withErrors(['Invalid mobile number']);
    }

    // Generate OTP
    $otp = rand(1000, 9999); // Generate random 4-digit OTP
    $user->otp = $otp;
    $user->save();
    $mobile_number = $request->mobile_number;
    $config = SMS_module::get_settings('clicksend_sms');

    $templateId =  $config['template_id_for_verify_account'];
    $smsResponse =  SMS_module::send($mobile_number, $otp, $templateId);

    // Store OTP and mobile number in session
    Session::put('otp', $otp);
    Session::put('mobile_number', $request->mobile_number);

    // Redirect to the OTP verification page with OTP data
    return redirect()->route('doctor.auth.verify-otp')->with('otp', $otp);
}


  public function verifyotp()
{      
    $settingData = BusinessSetting::where('key', 'logo')->first();
    $systemLogo = $settingData ? $settingData->value : '';

    // **Ensure OTP is always retrieved from session**
    $otp = Session::get('otp');

    return view('doctor-views.auth.verify-otp', compact('systemLogo', 'otp'));
}



    
    public function submitotp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4',
        ]);

        $otp = Session::get('otp');
        $mobileNumber = Session::get('mobile_number');
        $user = User::where('mobile_number', $mobileNumber)->where('role_id', 3)->first();    
        

            $password =  $user->decrypted_password;
          //  dd($password);




        if ($user && Hash::check($password, $user->password)) {

             if ($user->status == 0) {
                 return redirect()->back()->withErrors(['Your account is pending admin approval.']);
             }

             if ($user->status == 2) {
                 return redirect()->back()->withErrors(['Your account has been rejected. Please contact support.']);
             }

             Auth::login($user);
             Session::put('doctor_role_id', $user->role_id);
             Session::put('doctor_id', $user->id);
    

                return redirect()->route('doctor.dashboard');
            } else {
                return redirect()->back()->withErrors(['Invalid OTP']);
            }
    
    }

    public function resendOtp(Request $request)
{
    $mobileNumber = Session::get('mobile_number');

    // Check if the user exists
    $user = User::where('mobile_number', $mobileNumber)->where('role_id', 3)->first();
    if (!$user) {
        return redirect()->route('doctor.auth.signin')->withErrors(['Invalid mobile number, please sign in again.']);
    }

    // Generate a new OTP
    $newOtp = rand(1000, 9999);

    // Save new OTP in the database
    $user->otp = $newOtp;
    $user->save();

    // **Clear old OTP from session and set the new one**
    Session::forget('otp'); 
    Session::put('otp', $newOtp);
    // dd(Session::get('otp'));

    // Here you can integrate your SMS API to send the OTP to the user

    Toastr::success('A new OTP has been sent to your mobile number.', 'Success');

    // Redirect back to OTP verification page
    return redirect()->route('doctor.auth.verify-otp');
}



    public function logout(Request $request)
    {
        auth()->logout();
        return redirect()->route('doctor.auth.login');
    }
    
  public function onlineStatus(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found'
        ], 404);
    }

    $user->online_status = $user->online_status == 1 ? 0 : 1;
    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'Online status toggled',
        'online_status' => $user->online_status
    ]);
}

    
    
}