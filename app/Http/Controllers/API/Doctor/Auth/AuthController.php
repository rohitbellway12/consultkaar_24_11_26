<?php
namespace App\Http\Controllers\API\Doctor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Allergy;
use App\Models\Medication;
use App\Models\Injury;
use App\Models\Surgery;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\SMS_module;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use App\Models\Bank;




class AuthController extends Controller
{



    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'role_id' => 'required',
                'fcm_token' => 'required',
            ], [
                'role_id.required' => 'Please provide role id.',
                'fcm_token.required' => 'Please send fcm token.',
            ]);

            if (!$request->filled('mobile_number') && !$request->filled('email')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please provide either mobile number or email.'
                ]);
            }

            if ($validation->fails()) {
                return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
            }

            $contactField = null;
            $contactValue = null;

            if ($request->filled('email')) {
                $contactField = 'email';
                $contactValue = $request->email;
            } elseif ($request->filled('mobile_number')) {
                $contactField = 'mobile_number';
                $contactValue = $request->mobile_number;
            }

            // ⚠️ Role ID ab dynamic nahi, hardcoded 3 — agar aap chahein toh $request->role_id use karein
            $user = User::where('role_id', 3)->where($contactField, $contactValue)->first();

            $otp = rand(1000, 9999);
            
            $testUsers = ['8899556622', 'bellway43@gmail.com'];
            $otp = in_array($contactValue, $testUsers)
                ? 1234
                : rand(1000, 9999);

            if ($user) {
                $user->fcm_token = $request->fcm_token;
                $user->otp = $otp;
                $user->save();
            } else {
                $user = new User();
                $user->role_id = 3;
                $user->fcm_token = $request->fcm_token;
                $user->otp = $otp;
                $user->is_notification_setting_on = "yes";

                if ($contactField === 'email') {
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

                DB::table('users')->where('id', $user->id)->update(['uid' => 'u' . $user->id]);
            }

            // Image URL
            if (!empty($user->image)) {
                $user->image = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $user->image)
                    : asset('storage/app/public/users/' . $user->image);
            }
            // Note: Agar image null hai, toh frontend handle karega

            // === OTP SEND ===
            $sent = false;
            $message = '';

            // ✅ FIXED: Closure parameter renamed to $mail
            if ($user->email) {
                try {
                    \Mail::raw("Your OTP is: $otp", function ($mail) use ($user) {
                        $mail->to($user->email)->subject('Your OTP Code');
                    });
                    $sent = true;
                    $message = 'OTP sent successfully to your email. Please enter OTP to login.';
                } catch (\Exception $e) {
                    // Optional: Log::error("Email OTP failed: " . $e->getMessage());
                }
            }

            if (!$sent && $user->mobile_number) {
                $config = SMS_module::get_settings('clicksend_sms');
                $templateId = $config['template_id_for_verify_account'] ?? null;
                $smsResponse = SMS_module::send($user->mobile_number, $otp, $templateId);

                if ($smsResponse > 0) {
                    $sent = true;
                    $message = 'OTP sent successfully to your mobile. Please enter OTP to login.';
                }
            }

            if (!$sent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP via email or SMS.'
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $user
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ]);
        }
    }



    public function verifyOTP(Request $request)
    {
        try {
            // Validate: at least one contact + otp
            $validation = Validator::make($request->all(), [
                'otp' => 'required|digits:4',
            ]);

            if (!$request->filled('doctor_id') && !$request->filled('email') && !$request->filled('mobile_number')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please provide doctor_id, email, or mobile_number.'
                ]);
            }

            if ($validation->fails()) {
                return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
            }

            // Build query based on what is provided
            $query = User::where('role_id', 3)->where('otp', $request->otp);

            if ($request->filled('doctor_id')) {
                $query->where('id', $request->input('doctor_id'));
            } elseif ($request->filled('email')) {
                $query->where('email', $request->input('email'));
            } elseif ($request->filled('mobile_number')) {
                $query->where('mobile_number', $request->input('mobile_number'));
            }

            $userData = $query->first();

            if (!$userData) {
                return response()->json(['status' => false, 'message' => 'Invalid OTP or contact details.']);
            }

            // Check OTP expiry (5 minutes)
            $emitted = Carbon::parse($userData->updated_at);
            $now = Carbon::now();
            $diffInSeconds = $now->diffInSeconds($emitted);

            if ($diffInSeconds > 300) {
                // Expire OTP
                $userData->update(['otp' => null]);
                return response()->json(['status' => false, 'message' => 'OTP has expired.']);
            }

            // Rejected user check
            if ($userData->status == 2) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account has been rejected. Reason: ' . ($userData->rejection_reason ?? 'Please contact support.'),
                    'account_status' => 'rejected',
                    'rejection_reason' => $userData->rejection_reason
                ]);
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($userData);
            $userData->update(['auth_token' => $token, 'otp' => null]); // Clear OTP after use

            // Profile completion check
            $isProfileCompleted = !empty($userData->name) &&
                !empty($userData->domain_id) &&
                !empty($userData->speciality_id) &&
                !empty($userData->country_id) &&
                !empty($userData->identity_proofs);

            $canAccessDashboard = ($userData->status == 1 && $userData->is_active == 1);

            // Set status message
            $message = 'OTP verified successfully';
            $accountStatus = 'approved';

            if (!$isProfileCompleted) {
                $message = 'OTP verified. Please complete your profile to proceed.';
                $accountStatus = 'incomplete_profile';
            } elseif ($userData->status == 0) {
                $message = 'OTP verified. Your profile is under review by admin.';
                $accountStatus = 'pending_approval';
            }

            // Format image URL
            if (!empty($userData->image)) {
                $userData->image = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $userData->image)
                    : asset('storage/app/public/users/' . $userData->image);
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $userData,
                'account_status' => $accountStatus,
                'is_profile_completed' => $isProfileCompleted,
                'can_access_dashboard' => $canAccessDashboard,
                'approval_status' => $userData->status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ]);
        }
    }
    public function resendOTP(Request $request)
    {
        try {

            $doctorId = $request->post('doctor_id');

            // Validate the input data
            $validation = Validator::make($request->all(), [
                'doctor_id' => 'required',
            ]);


            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
            }

            // Find the user by doctor_id
            $userData = User::find($doctorId);

            if ($userData) {
                // Update verification code
                $otp = random_int(1000, 9999);
                $userData->otp = $otp;
                $userData->save();

                $sent = false;
                $message = '';

                // ✅ Send OTP via email (same as login)
                if ($userData->email) {
                    try {
                        \Mail::raw("Your OTP is: $otp", function ($mail) use ($userData) {
                            $mail->to($userData->email)->subject('Your OTP Code');
                        });
                        $sent = true;
                        $message = 'OTP sent successfully to your email. Please enter OTP to login.';
                    } catch (\Exception $e) {
                        // fallback to SMS below
                    }
                }

                // Fallback to SMS if email not sent
                if (!$sent && $userData->mobile_number) {
                    $config = SMS_module::get_settings('clicksend_sms');
                    $templateId = $config['template_id_for_verify_account'] ?? null;
                    $smsResponse = SMS_module::send($userData->mobile_number, $otp, $templateId);
                    if ($smsResponse > 0) {
                        $sent = true;
                        $message = 'OTP sent successfully to your mobile. Please enter OTP to login.';
                    }
                }

                if (!$sent) {
                    return response()->json(['status' => false, 'message' => 'Failed to send OTP via email or SMS.', 'data' => null]);
                }

                // ✅ Don't leak OTP in response
                $userData->makeHidden(['otp']);

                return response()->json([
                    'status'  => true,
                    'message' => $message,
                    'data'    => $userData,
                ]);

            } else {
                return response()->json(['status' => false, 'message' => 'User not found', 'data' => null]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getProfileDetails(Request $request)
    {

        // Try to get customer details
        try {

            // Get customer_id from the token
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;


            // Find the user by user_id
            // $userData = User::with(['domain', 'speciality'])->find($userId);
            $userData = User::select('*')->with(['domain', 'speciality'])->find($userId);

            // dd($userData);

            if ($userData) {
                
                
                $bankDetails = Bank::where('doctor_id', $userId)->first();
                $userData->bank_details = $bankDetails;

                $userImage = $userData->image;
                if (!empty($userImage)) {
                    $userData->image = (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);
                }



                // Fetch allergies
                $allergyIds = explode(',', $userData->allergies);
                $allergies = Allergy::whereIn('id', $allergyIds)->get();

                if (count($allergies) > 0) {
                    $userData->allergies = $allergies;
                } else {
                    $userData->allergies = $userData->allergies;
                }

                // Fetch current medications
                $currentMedicationIds = explode(',', $userData->current_medications);
                $currentMedications = Medication::whereIn('id', $currentMedicationIds)->get();

                if (count($currentMedications) > 0) {
                    $userData->current_medications = $currentMedications;
                } else {
                    $userData->current_medications = $userData->current_medications;
                }

                // Fetch past medications
                $pastMedicationIds = explode(',', $userData->past_medications);
                $pastMedications = Medication::whereIn('id', $pastMedicationIds)->get();

                if (count($pastMedications) > 0) {
                    $userData->past_medications = $pastMedications;
                } else {
                    $userData->past_medications = $userData->past_medications;
                }

                // Fetch injuries
                $injuriesIds = explode(',', $userData->injuries);
                $injuries = Injury::whereIn('id', $injuriesIds)->get();
                if (count($injuries) > 0) {
                    $userData->injuries = $injuries;
                } else {
                    $userData->injuries = $userData->injuries;
                }

                // Fetch surgeries
                $surgeriesIds = explode(',', $userData->surgeries);
                $surgeries = Surgery::whereIn('id', $surgeriesIds)->get();

                if (count($surgeries) > 0) {
                    $userData->surgeries = $surgeries;
                } else {
                    $userData->injuries = $userData->surgeries;
                }

                // Add account status information
                $isProfileCompleted = !empty($userData->name) &&
                    !empty($userData->domain_id) &&
                    !empty($userData->speciality_id) &&
                    !empty($userData->country_id) &&
                    !empty($userData->identity_proofs);

                $userData->is_profile_completed = $isProfileCompleted;
                $userData->can_access_dashboard = ($userData->status == 1 && $userData->is_active == 1);
                $userData->approval_status = $userData->status; // 0=pending, 1=approved, 2=rejected
                $userData->approval_status_text = $this->getApprovalStatusText($userData->status);

                // Show rejection reason if rejected
                if ($userData->status == 2) {
                    $userData->rejection_reason_text = $userData->rejection_reason;
                }

                return response()->json(['status' => true, 'message' => 'Data found successfully', 'data' => $userData]);
            } else {
                return response()->json(['status' => false, 'message' => 'User does not exist', 'data' => null]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    // public function completeProfile(Request $request)
    // {
    //     try {
    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = $user->id;

    //         // Base validation rules
    //         $rules = [
    //             'name' => 'required|string|max:100',
    //             'email' => 'required|email|unique:users,email,' . $userId,
    //             'mobile_number' => 'digits_between:10,15|unique:users,mobile_number,' . $userId,
    //             'gender' => 'required|in:Male,Female',
    //             'date_of_birth' => 'date|before:today',
    //             'country' => 'string|max:100',
    //             'state' => 'nullable|string|max:100',
    //             'city' => 'nullable|string|max:100',
    //             'domain_id' => 'required|exists:domains,id',
    //             'qualification' => 'string',
    //             'experience' => 'numeric|min:0',
    //             'description' => 'nullable|string',
    //             'language' => 'nullable',
    //             'address' => 'nullable|string',
    //             'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //             'identity_proofs' => 'array|min:1',
    //             'identity_proofs.*.type' => 'numeric|min:1',
    //             'identity_proofs.*.front' => 'image|mimes:jpeg,png,jpg',
    //             'identity_proofs.*.back' => 'nullable|image|mimes:jpeg,png,jpg',
    //             'relevant_documents' => 'array|min:1',
    //             'relevant_documents.*.type' => 'numeric|min:1',
    //             'relevant_documents.*.file' => 'image|mimes:jpeg,png,jpg',
    //         ];

    //         // Check if selected domain is "Other" by name
    //         $selectedDomain = \App\Models\Domain::find($request->domain_id);
    //         $isOtherDomain = $selectedDomain && strtolower($selectedDomain->name) === 'other';

    //         // Conditional validation based on domain name
    //         if ($isOtherDomain) {
    //             // For "Other" domain, require custom fields
    //             $rules['custom_domain_name'] = 'required|string|max:100';
    //             $rules['custom_specialization'] = 'required|string|max:100';
    //             $rules['speciality_id'] = 'nullable'; // Optional for Other
    //         } else {
    //             // For standard domains, require speciality_id
    //             $rules['speciality_id'] = 'required|exists:specialities,id';
    //         }

    //         $validation = Validator::make($request->all(), $rules);

    //         // Validate speciality-domain relationship (only for non-Other domains)
    //         if ($request->filled('domain_id') && !$isOtherDomain && $request->filled('speciality_id')) {
    //             $valid = \App\Models\Speciality::where('id', $request->speciality_id)
    //                 ->where('domain_id', $request->domain_id)
    //                 ->exists();
    //             if (!$valid) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Selected specialization does not belong to the chosen domain.'
    //                 ]);
    //             }
    //         }

    //         if ($validation->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $validation->errors()->first()
    //             ]);
    //         }

    //         // Uniqueness check
    //         // if (User::where('mobile_number', $request->mobile_number)->where('role_id', 3)->where('id', '!=', $userId)->exists()) {
    //         //     return response()->json(['status' => false, 'message' => 'Mobile number already exists']);
    //         // }
    //         if (User::where('email', $request->email)->where('role_id', 3)->where('id', '!=', $userId)->exists()) {
    //             return response()->json(['status' => false, 'message' => 'Email already exists']);
    //         }

    //         // Update basic profile
    //         $this->updateBasicProfile($user, $request);
    //         // Add these lines in completeProfile()
    //         $user->city = $request->filled('city') ? trim($request->city) : null;
    //         $user->state = $request->filled('state') ? trim($request->state) : null;
    //         $user->country = $request->filled('country') ? trim($request->country) : null;

    //         // === HANDLE IDENTITY PROOFS (ARRAY) ===
    //         $identityProofs = [];
    //         foreach ($request->file('identity_proofs', []) as $index => $files) {
    //             if (isset($files['front'])) {
    //                 $front = Helpers::upload('users/identity_proofs/', 'png', $files['front']);
    //                 $back = null;
    //                 if (isset($files['back'])) {
    //                     $back = Helpers::upload('users/identity_proofs/', 'png', $files['back']);
    //                 }
    //                 $identityProofs[] = [
    //                     'type' => $request->input("identity_proofs.$index.type"),
    //                     'front' => $front,
    //                     'back' => $back
    //                 ];
    //             }
    //         }
    //         $user->identity_proofs = $identityProofs;

    //         // === HANDLE RELEVANT DOCUMENTS (ARRAY) ===
    //         $relevantDocs = [];
    //         foreach ($request->file('relevant_documents', []) as $index => $file) {
    //             if ($file) {
    //                 $image = Helpers::upload('users/relevant_documents/', 'png', $file);
    //                 $relevantDocs[] = [
    //                     'type' => $request->input("relevant_documents.$index.type"),
    //                     'image' => $image
    //                 ];
    //             }
    //         }
    //         $user->relevant_documents = $relevantDocs;

    //         // Profile image
    //         if ($request->hasFile('image')) {
    //             $user->image = Helpers::upload('users/', 'png', $request->file('image'));
    //         }

    //         // *** YE DO NAYI LINES ADD KI GAIN HAIN ***
    //         // Ensure the user has the correct role_id
    //         $user->role_id = 3;

    //         // Set the uid if it's not already set
    //         if (empty($user->uid)) {
    //             $user->uid = 'u' . $userId;
    //         }

    //         $user->isregister = 1;
    //         $user->status = 0;
    //         $user->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Your profile has been submitted for admin approval.',
    //             'data' => $user
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Profile submission failed: ' . $e->getMessage()
    //         ]);
    //     }
    // }
    
   public function completeProfile(Request $request)
{
    try {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user->id;

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $userId,
            'mobile_number' => 'required|digits_between:10,15',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'date|before:today',
            'country' => 'string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'domain_id' => 'required|exists:domains,id',
            'qualification' => 'string',
            'experience' => 'numeric|min:0',
            'description' => 'nullable|string',
            'language' => 'nullable',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'identity_proofs' => 'array|min:1',
            'identity_proofs.*.type' => 'numeric|min:1',
            'identity_proofs.*.front' => 'image|mimes:jpeg,png,jpg',
            'identity_proofs.*.back' => 'nullable|image|mimes:jpeg,png,jpg',
            'relevant_documents' => 'nullable|array',
            'relevant_documents.*.type' => 'nullable|numeric|min:1',
            'relevant_documents.*.file' => 'nullable|image|mimes:jpeg,png,jpg',
        ];

        // Check if selected domain is "Other" by name
        $selectedDomain = \App\Models\Domain::find($request->domain_id);
        $isOtherDomain = $selectedDomain && strtolower($selectedDomain->name) === 'other';

        // Conditional validation based on domain name
        if ($isOtherDomain) {
          
            $rules['custom_domain_name'] = 'required|string|max:100';
            $rules['custom_specialization'] = 'required|string|max:100';
            $rules['speciality_id'] = 'nullable'; // Optional for Other
        } else {
           
            $rules['speciality_id'] = 'required|exists:specialities,id';
        }

        $validation = Validator::make($request->all(), $rules);

        
        if ($request->filled('domain_id') && !$isOtherDomain && $request->filled('speciality_id')) {
            $valid = \App\Models\Speciality::where('id', $request->speciality_id)
                ->where('domain_id', $request->domain_id)
                ->exists();
            if (!$valid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected specialization does not belong to the chosen domain.'
                ]);
            }
        }

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first()
            ]);
        }

        
        if (User::where('email', $request->email)->where('role_id', 3)->where('id', '!=', $userId)->exists()) {
            return response()->json(['status' => false, 'message' => 'Email already exists']);
        }

        
        $this->updateBasicProfile($user, $request);
        
       
        if ($request->filled('mobile_number')) {
            $user->mobile_number = trim($request->mobile_number);
        }
    
        $user->city = $request->filled('city') ? trim($request->city) : null;
        $user->state = $request->filled('state') ? trim($request->state) : null;
        $user->country = $request->filled('country') ? trim($request->country) : null;

       
        $identityProofs = [];
        foreach ($request->file('identity_proofs', []) as $index => $files) {
            if (isset($files['front'])) {
                $front = Helpers::upload('users/identity_proofs/', 'png', $files['front']);
                $back = null;
                if (isset($files['back'])) {
                    $back = Helpers::upload('users/identity_proofs/', 'png', $files['back']);
                }
                $identityProofs[] = [
                    'type' => $request->input("identity_proofs.$index.type"),
                    'front' => $front,
                    'back' => $back
                ];
            }
        }
        $user->identity_proofs = $identityProofs;

        
        $relevantDocs = [];
        if ($request->has('relevant_documents')) {
            foreach ($request->file('relevant_documents', []) as $index => $file) {
                $docFile = is_array($file) && isset($file['file']) ? $file['file'] : $file;
                if ($docFile) {
                    $image = Helpers::upload('users/relevant_documents/', 'png', $docFile);
                    $relevantDocs[] = [
                        'type' => $request->input("relevant_documents.$index.type"),
                        'image' => $image
                    ];
                }
            }
        }
        $user->relevant_documents = !empty($relevantDocs) ? $relevantDocs : null;

        
        if ($request->hasFile('image')) {
            $user->image = Helpers::upload('users/', 'png', $request->file('image'));
        }

       
        $user->role_id = 3;

        if (empty($user->uid)) {
            $user->uid = 'u' . $userId;
        }

        $user->isregister = 1;
        $user->status = 0;
        
        // ✅ availability_status default 'available' — online/offline ka kaam online_status karta hai
        if (empty($user->availability_status)) {
            $user->availability_status = 'available';
        }
        $user->save();
     


        return response()->json([
            'status' => true,
            'message' => 'Your profile has been submitted for admin approval.',
            'data' => $user
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Profile submission failed: ' . $e->getMessage()
        ]);
    }
}

    public function updateProfileDetails(Request $request)
    {
        // Try to update doctor details
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $user = User::find($userId);
            if ($user) {
                // Update all fields for both approved and pending accounts
                $this->updateBasicProfile($user, $request);
                $this->handleDocumentUploads($user, $request);

                // For already approved doctors, allow updating fees
                if ($user->status == 1) {
                    if ($request->has('chat_fee'))
                        $user->chat_fee = $request->chat_fee;
                    if ($request->has('audio_fee'))
                        $user->audio_fee = $request->audio_fee;
                    if ($request->has('video_fee'))
                        $user->video_fee = $request->video_fee;
                }

                $user->save();

                $message = $user->status == 1 ? 'Profile updated successfully' : 'Your profile is under review';
                return response()->json(['status' => true, 'message' => $message, 'data' => $user]);
            } else {
                return response()->json(['status' => false, 'message' => 'User does not exist', 'data' => null]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    private function updateBasicProfile($user, $request)
    {
        $userId = $user->id;
        if ($request->has('mobile_number')) {
            $user->mobile_number = $request->mobile_number;
        }

        if ($request->has('fcm_token'))
            $user->fcm_token = $request->fcm_token;
        if ($request->has('name'))
            $user->name = $request->name;
        if ($request->has('email'))
            $user->email = $request->email;
        if ($request->has('gender'))
            $user->gender = $request->gender;
        if ($request->has('address'))
            $user->address = $request->address;

        if ($request->has('country_id'))
            $user->country_id = $request->country_id;

        // Add these lines alongside other fields
        if ($request->has('state'))
            $user->state = trim($request->state);
        if ($request->has('city'))
            $user->city = trim($request->city);


        // if ($request->has('pincode')) $user->pincode = $request->pincode;

        // if ($request->has('domain_id'))
        //     $user->domain_id = $request->domain_id;

        // // Check if selected domain is "Other" by name
        // $selectedDomain = null;
        // if ($request->has('domain_id')) {
        //     $selectedDomain = \App\Models\Domain::find($request->domain_id);
        // }
        // $isOtherDomain = $selectedDomain && strtolower($selectedDomain->name) === 'other';

        // // Handle custom domain fields for "Other" domain
        // if ($isOtherDomain) {
        //     if ($request->has('custom_domain_name'))
        //         $user->custom_domain_name = trim($request->custom_domain_name);
        //     if ($request->has('custom_specialization'))
        //         $user->custom_specialization = trim($request->custom_specialization);
        //     $user->speciality_id = null; // Clear speciality_id for Other domain
        // } else {
        //     // For standard domains
        //     if ($request->has('speciality_id'))
        //         $user->speciality_id = $request->speciality_id;
        //     $user->custom_domain_name = null; // Clear custom fields
        //     $user->custom_specialization = null;
        // }
        
        
        // only update when provided
         if ($request->filled('domain_id')) {
            $user->domain_id = $request->domain_id;

            $selectedDomain = \App\Models\Domain::find($request->domain_id);
            $isOtherDomain = $selectedDomain && strtolower($selectedDomain->name) === 'other';

            if ($isOtherDomain) {
                if ($request->has('custom_domain_name')) {
                    $user->custom_domain_name = trim($request->custom_domain_name);
                }

                if ($request->has('custom_specialization')) {
                    $user->custom_specialization = trim($request->custom_specialization);
                }

                $user->speciality_id = null;
            } else {
                if ($request->filled('speciality_id')) {
                    $user->speciality_id = $request->speciality_id;
                }

                $user->custom_domain_name = null;
                $user->custom_specialization = null;
            }
        } elseif ($request->filled('speciality_id')) {
            $user->speciality_id = $request->speciality_id;
        }
        
        
        
        
        

        if ($request->has('qualification'))
            $user->qualification = $request->qualification;
        if ($request->has('experience'))
            $user->experience = $request->experience;
        if ($request->has('description'))
            $user->description = $request->description;


        if ($request->has('language')) {
            $language = $request->language;
            if (is_array($language)) {
                $user->language = implode(',', $language);
            } else {
                $user->language = $language;
            }
        }

        if ($request->has('city'))
            $user->city = trim($request->city);
        if ($request->has('state'))
            $user->state = trim($request->state);
        if ($request->has('country'))
            $user->country = trim($request->country);

    }

    private function handleDocumentUploads($user, $request)
    {
        // Handle Identity Proofs Upload (Front & Back)
        if ($request->has('identity_proofs')) {
            $identityProofs = [];
            foreach ($request->identity_proofs as $key => $files) {
                $frontImage = null;
                $backImage = null;

                if (isset($files['front']) && $request->hasFile("identity_proofs.$key.front")) {
                    $frontImage = Helpers::upload('users/identity_proofs/', 'png', $files['front']);
                }

                if (isset($files['back']) && $request->hasFile("identity_proofs.$key.back")) {
                    $backImage = Helpers::upload('users/identity_proofs/', 'png', $files['back']);
                }

                if ($frontImage || $backImage) {
                    $identityProofs[] = [
                        'type' => $key,
                        'front' => $frontImage,
                        'back' => $backImage
                    ];
                }
            }
            if (!empty($identityProofs)) {
                $user->identity_proofs = $identityProofs;
            }
        }

        // Handle Relevant Documents Upload
        if ($request->has('relevant_documents')) {
            $relevantDocs = [];
            foreach ($request->relevant_documents as $key => $file) {
                if ($request->hasFile("relevant_documents.$key")) {
                    $imageName = Helpers::upload('users/relevant_documents/', 'png', $file);
                    $relevantDocs[] = ['type' => $key, 'image' => $imageName];
                }
            }
            if (!empty($relevantDocs)) {
                $user->relevant_documents = $relevantDocs;
            }
        }

        if (!empty($request->file('image'))) {
            $userImage = Helpers::upload('users/', 'png', $request->file('image'));
            $user->image = $userImage;
        }
    }

    public function logout()
    {
        // Try to log out the customer and invalidate the token
        try {
            // Invalidate the user's token
            JWTAuth::invalidate(JWTAuth::getToken());

            // Log the user out and delete the account
            Auth::logout();

            return response()->json([
                'status' => true,
                'message' => 'Logout successfully',
            ]);
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

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



                // delete user's appointment data 

                Appointment::where(['doctor_id' => $customerId])->delete();


                // delete doctor slot

                DB::table('doctor_time_slots')->where('doctor_id', $customerId)->delete();



                // Log the user out
                Auth::logout();



                return response()->json(['status' => true, 'message' => 'Account deleted successfully', 'data' => null]);
            } else {
                return response()->json(['status' => false, 'message' => 'User does not exist', 'data' => null]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }







    /**
     * Doctor updates their comment visibility setting for patients.
     */
    /**
     * Doctor updates their comment visibility setting for patients.
     */
    /**
     * Doctor updates their comment visibility setting for patients.
     */
    public function updateCommentVisibility(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $doctor = JWTAuth::toUser($token);

            if ($doctor->role_id != 3) {
                return response()->json(['status' => 'error', 'code' => 403, 'message' => 'Only doctors can update this setting.']);
            }

            $validator = Validator::make($request->all(), [
                'show_comments_to_patients' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
            }

            $doctor->show_comments_to_patients = $request->show_comments_to_patients;
            $doctor->save();

            $message = ($doctor->show_comments_to_patients)
                ? 'Comments are now enabled for patients. Patients can see your comments.'
                : 'Comments are now disabled for patients. Patients cannot see your comments.';

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $message,
                'data' => ['show_comments_to_patients' => $doctor->show_comments_to_patients]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get human-readable approval status text
     */
    private function getApprovalStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Pending Approval';
            case 1:
                return 'Approved';
            case 2:
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }


    public function OnlineStatus(Request $request)
    {
        try {

            // 1️⃣ Validation
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'online_status' => 'required|in:0,1',
            ], [
                'user_id.required' => 'Please provide user id.',
                'user_id.exists' => 'User not found.',
                'online_status.required' => 'Please send online status.',
                'online_status.in' => 'Online status must be 0 or 1.',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validation->errors()->first()
                ], 422);
            }

            // 2️⃣ User check (role_id = 3)
            $user = User::where('id', $request->user_id)
                ->where('role_id', 3)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found or invalid role'
                ], 404);
            }

            // 3️⃣ Update online status
            $user->online_status = $request->online_status;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Online status updated successfully',
                'online_status' => $user->online_status
            ]);

        } catch (\Exception $e) {


            Log::error('OnlineStatus API Error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

}
