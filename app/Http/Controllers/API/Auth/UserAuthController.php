<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Allergy;
use App\Models\Medication;
use App\Models\Injury;
use App\Models\Surgery;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\CentralLogics\Helpers;
use App\CentralLogics\SMS_module;
use App\Models\UserLabTest;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\UsersAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;

class UserAuthController extends Controller
{



public function login(Request $request)
{
    try {

        $validator = Validator::make($request->all(), [
            'role_id'   => 'required',
            'fcm_token' => 'nullable',
        ], [
            'role_id.required'   => 'Please provide role id.',
            'fcm_token.required' => 'Please send FCM token.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if (!$request->filled('mobile_number') && !$request->filled('email')) {
            return response()->json([
                'status'  => false,
                'message' => 'Please provide mobile number or email.'
            ]);
        }

        $role_id   = $request->role_id;
        $fcm_token = $request->fcm_token;

        $contactField = null;
        $contactValue = null;

        if ($request->filled('email')) {
            $contactField = 'email';
            $contactValue = $request->email;
        } else {
            $contactField = 'mobile_number';
            $contactValue = $request->mobile_number;
        }

        $user = User::where($contactField, $contactValue)
            ->where('role_id', $role_id)
            ->first();

        // Set fixed OTP for test email and mobile number
        $otp = ($contactValue == '+917894561235') ? 1111 : 
               (($contactValue == 'testuser@gmail.com') ? 1234 : rand(1000, 9999));

        if ($user) {

            $user->update([
                'fcm_token' => $fcm_token,
                'otp'       => $otp
            ]);

        } else {

            $user = new User();
            $user->role_id = $role_id;
            $user->fcm_token = $fcm_token;
            $user->otp = $otp;
            $user->is_notification_setting_on = 'yes';

            if ($contactField == 'email') {
                $user->email = $contactValue;
                $user->mobile_number = $request->mobile_number ?? null;
            } else {
                $user->mobile_number = $contactValue;
                $user->email = $request->email ?? null;
            }

            $password = Helpers::generatePassword(8);
            $user->password = bcrypt($password);
            $user->decrypted_password = $password;

            $user->save();

            DB::table('users')->where('id', $user->id)->update([
                'uid' => 'u' . $user->id
            ]);
        }

      
        if (!empty($user->image)) {
            $user->image = (env('APP_ENV') == 'local')
                ? asset('storage/users/' . $user->image)
                : asset('storage/app/public/users/' . $user->image);
        } else {
            $user->image = asset('assets/admin/img/160x160/img1.jpg');
        }

   
        $sent = false;
        $message = '';

      
        if ($user->email) {
            try {
                \Mail::raw("Your OTP is: $otp", function ($msg) use ($user) {
                    $msg->to($user->email)
                        ->subject('Your OTP Code');
                });
                $sent = true;
                $message = 'OTP sent to your email. Please verify.';
            } catch (\Exception $e) {
                
            }
        }

        if (!$sent && $user->mobile_number) {
            $config = SMS_module::get_settings('clicksend_sms');
            $templateId = $user->wasRecentlyCreated
                ? ($config['template_id_for_verify_new_account'] ?? null)
                : ($config['template_id_for_verify_account'] ?? null);

            $smsResponse = SMS_module::send($user->mobile_number, $otp, $templateId);

            if ($smsResponse > 0) {
                $sent = true;
                $message = 'OTP sent to your mobile number. Please verify.';
            }
        }

        if (!$sent) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to send OTP.'
            ]);
        }
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $user,
            'isLogin' => $user->isregister ?? 0
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'trace'   => env('APP_DEBUG') ? $e->getTraceAsString() : null
        ]);
    }
}

// verifyOTP function remains the same
public function verifyOTP(Request $request)
{
    try {
        $userId = (int) $request->user_id;
        $otp = (int) $request->otp;

        // Validate input
        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
        ], [
            'user_id.required' => 'Please provide a user id.',
            'otp.required' => 'Please enter an OTP.',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => $validation->errors()->first()
            ]);
        }

        // Fetch user
        $userData = User::where(['otp' => $otp, 'id' => $userId])->first();

        if ($userData) {
            // Check if OTP is expired
            $now = Carbon::now();
            $otpIssuedAt = Carbon::parse($userData->updated_at);
            $secondsElapsed = $now->diffInSeconds($otpIssuedAt);

            if ($secondsElapsed > 300) {
                $userData->update(['otp' => 0]); // Reset OTP
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'OTP has expired',
                    'isLogin' => false
                ]);
            }

            // OTP is valid — generate JWT token
            $token = JWTAuth::fromUser($userData);
            $userData->update(['auth_token' => $token]);

            // Set image path
            if (!empty($userData->image)) {
                $imagePath = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $userData->image)
                    : asset('storage/app/public/users/' . $userData->image);
            } else {
                $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                $imagePath = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
            }

            $userData->image = $imagePath;

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'OTP verified successfully',
                'data' => $userData,
                'isLogin' => $userData->isregister,
            ]);
        } else {
            // Invalid OTP
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Invalid OTP',
                'isLogin' => false
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'code' => 500,
            'message' => $e->getMessage()
        ]);
    }
}
  
 
public function resendOTP(Request $request)
{
    try {
        $userId = (int) $request->user_id;

        $validation = Validator::make($request->all(), [
            'user_id' => 'required',
        ], [
            'user_id.required' => 'Please enter a user id.',
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
        }

        $userData = User::find($userId);

        if ($userData) {
            $otp = random_int(1000, 9999);
            $userData->otp = $otp;
            $userData->save();

            $sent = false;
            $message = '';

            // ✅ Send OTP via email (same as login)
            if ($userData->email) {
                try {
                    \Mail::raw("Your OTP is: $otp", function ($msg) use ($userData) {
                        $msg->to($userData->email)->subject('Your OTP Code');
                    });
                    $sent = true;
                    $message = 'OTP sent successfully to your email. Please enter OTP to verify.';
                } catch (\Exception $e) {
                    // fallback to WhatsApp below
                }
            }

            // Fallback to WhatsApp if email not sent
            if (!$sent) {
                $whatsappResponse = \App\Helpers\MSG91Helper::sendWhatsAppOTP($userData->mobile_number, $otp);
                if ($whatsappResponse) {
                    $sent = true;
                    $message = 'OTP sent successfully on WhatsApp';
                }
            }

            if (!$sent) {
                return response()->json([
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Failed to send OTP.'
                ]);
            }

            // ✅ Don't leak OTP in response
            $userData->makeHidden(['otp']);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $message,
                'data' => $userData
            ]);

        } else {
            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'User not found']);
        }
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    }
}

    
    public function getUserDetails(Request $request)
    {
      
        // Try to get customer details
        try {

            // Get customer_id from the token
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;         
            

            // Find the user by user_id
            $userData = User::find($userId);



            if ($userData) {

             $userImage = $userData->image; 
             
                 if(!empty($userData->image)){
                    $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $userData->image) : asset('storage/app/public/users/' . $userData->image);
    
                } else {
                      $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                      $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");
                }
                $userData->image = $imagePath;
  

                

              // Fetch allergies
              $allergyIds = explode(',', $userData->allergies);
              $allergies = Allergy::whereIn('id', $allergyIds)->get();  

                if(count($allergies) > 0){
                    $userData->allergies =  $allergies;
                } else {
                    $userData->allergies = $userData->allergies;
                }

               // Fetch current medications
               $currentMedicationIds = explode(',', $userData->current_medications);
               $currentMedications = Medication::whereIn('id', $currentMedicationIds)->get();

                if(count($currentMedications) > 0){
                   $userData->current_medications =  $currentMedications;
                } else {
                    $userData->current_medications = $userData->current_medications;
                }

                // Fetch past medications
                $pastMedicationIds = explode(',', $userData->past_medications);
                $pastMedications = Medication::whereIn('id', $pastMedicationIds)->get();

                if(count($pastMedications) > 0){
                    $userData->past_medications =  $pastMedications;
                } else {
                     $userData->past_medications = $userData->past_medications;
                }

                // Fetch injuries
                $injuriesIds = explode(',', $userData->injuries);
                $injuries = Injury::whereIn('id', $injuriesIds)->get();
                if(count($injuries) > 0){
                    $userData->injuries =  $injuries;
                } else {
                    $userData->injuries = $userData->injuries;
                }

                // Fetch surgeries
                $surgeriesIds = explode(',', $userData->surgeries);
                $surgeries = Surgery::whereIn('id', $surgeriesIds)->get();

                if(count($surgeries) > 0){
                    $userData->surgeries =  $surgeries;
                } else {
                    $userData->injuries = $userData->surgeries;
                }
                return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Data found successfully' , 'data' => $userData]);
            } else {
                return response()->json(['code' => 404, 'status' => 'error', 'message' => 'User does not exist']);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }

   public function updateUserDetails(Request $request)
{
    try {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user->id;

        // Validate the input data
        $validation = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'email' => 'required|regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,4}$/',
            // *** CHANGE 1: Regex mein \s add kiya gaya hai spaces ke liye ***
            'name' => 'sometimes|nullable|string|min:3|regex:/^[a-zA-Z\s]+$/',
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
        }

        $userData = User::find($userId);

        if ($userData) {
            $mobileNumberExist = User::where('mobile_number', $request->mobile_number)
                ->where('role_id', '2')
                ->where('id', '!=', $userId)
                ->first();
            if($mobileNumberExist){
                $userImage =  $user->image;
                $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);
    
                $responseData = $user->toArray();
                $responseData['image'] = $imagePath;           
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Mobile number is already exist','data' => $responseData]);
            }

            if(!empty($request->file('image'))){
                $userData->image = Helpers::upload('users/', 'png', $request->file('image')); 
            }

            // *** CHANGE 2: Name sirf tab update hoga jab field mein value ho ***
            if ($request->filled('name')) {
                $userData->name = trim($request->post('name'));
            }
            
            $userData->email = $request->post('email');
            $userData->gender = $request->post('gender');
            $userData->mobile_number = $request->post('mobile_number');
            $userData->date_of_birth = $request->post('date_of_birth');
            $userData->blood_group = $request->post('blood_group');
            $userData->martial_status = $request->post('martial_status');
            $userData->height = $request->post('height');
            $userData->emergency_contact_number = $request->post('emergency_contact_number');
            $userData->location = $request->post('location');
            $userData->allergies = $request->post('allergies');
            $userData->current_medications = $request->post('current_medications');
            $userData->past_medications = $request->post('past_medications');
            $userData->injuries = $request->post('injuries');
            $userData->surgeries = $request->post('surgeries');
            $userData->smoking_habits = $request->post('smoking_habits');
            $userData->alchol_consumption = $request->post('alchol_consumption');
            $userData->activity_level = $request->post('activity_level');
            $userData->food_preference = $request->post('food_preference');
            $userData->occupation = $request->post('occupation');
            $userData->city = $request->post('city');
            $userData->pincode = $request->post('pincode');
            $userData->state = $request->post('state');
            $userData->address = $request->post('address');
            $userData->isregister = true;
            
            $userData->save();
            
            $userImage = $userData->image; 
                 
            if(!empty($userData->image)){
                $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $userData->image) : asset('storage/app/public/users/' . $userData->image);

            } else {
                  $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                  $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");
            }
            $userData->image = $imagePath;

            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Profile updated successfully','data' => $userData ]);
        } else {
            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'User does not exist']);
        }
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    }
}

  
    public function logout()
    {
        // Try to log out the customer and invalidate the token
        try {
            // Invalidate the user's token
            JWTAuth::invalidate(JWTAuth::getToken());

            // Log the user out
            Auth::logout();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Logout successfully',
            ]);
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }



    /**
     * Delete a patient account by customer_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount(Request $request)
    {
        // Try to delete a customer account
        try {




            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $customerId = $user->id;

            // Find the user by customer_id
            $user = User::find($customerId);

            if ($user) {
                $user->fcm_token = NULL;
                $user->save();
                  // Delete the user 
                $user->delete();
                
                // Invalidate the user's token
                JWTAuth::invalidate(JWTAuth::getToken());
                // delete user's booking data
            
                Order::where(['user_id'=>$customerId])->delete();
            
                 // delete user's address data 
          
                  UsersAddress::where(['user_id'=>$customerId])->delete();
         
                 // delete user's appointment data 
            
                Appointment::where(['user_id'=>$customerId])->delete();


                // delete user's 

                
                UserLabTest::where(['user_id'=>$customerId])->delete();
                 // Log the user out
                Auth::logout();
                
                

                return response()->json(['status' => 'success','code' => 200,  'message' => 'Account deleted successfully','data' => null]);
            } else {
                return response()->json(['status' => 'error', 'code' => 404,  'message' => 'User does not exist','data' => null]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    
    
    
    
    
    
    
    
    
    //------------------------User delete api with 200 response always-------------------------//
    public function deleteUser($id)
    {
        // Try to find the user
        $user = User::find($id);

        // If user exists, delete it
        if ($user) {
            $user->delete();
        }

        // Always return 200 OK response
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
    
    
   public function delete(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Item deleted (simulated).',
        ]);
    }
    
    
}