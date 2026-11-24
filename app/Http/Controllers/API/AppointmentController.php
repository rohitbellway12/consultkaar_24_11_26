<?php

// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Cart;
// use Tymon\JWTAuth\Facades\JWTAuth;
// use Illuminate\Support\Facades\Validator;
// use App\Models\Order;
// use App\Models\Coupon;
// use App\Models\UsersAddress;
// use App\Models\BusinessSetting;
// use App\Models\Product;
// use App\Models\Wishlist;
// use App\Models\User;
// use App\Models\Appointment;
// use App\CentralLogics\Helpers;
// use Illuminate\Support\Facades\DB;
// use PDF;
// use Stripe\Charge;
// use Stripe\Stripe;
// use Carbon\Carbon;

// class AppointmentController extends Controller
// {
//     /**
//      * Check doctor availability for new appointment types
//      */
//     public function checkAvailability(Request $request)
//     {
//         try {
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $userId = $user ? $user->id : null;

//             $validator = Validator::make($request->all(), [
//                 'doctor_id' => 'required|exists:users,id',
//                 'appoinmtent_date ' => 'required|date|after_or_equal:today',
//                 'appointment_time' => 'null',
//                 'appointment_type' => 'required|in:chat,audio,video,offline',
//             ]);

//             if ($validator->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
//             }

//             $doctorId = $request->doctor_id;
//             $date = $request->appoinmtent_date ;
//             $time = $request->appointment_time;
//             $type = $request->appointment_type;

//             // Check if doctor is active
//             $doctor = User::where('id', $doctorId)
//                 ->where('role_id', 3)
//                 ->where('is_active', 1)
//                 ->first();

//             if (!$doctor) {
//                 return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Doctor not found or inactive']);
//             }

//             // For chat/audio/video, check if feature is enabled
//             if (in_array($type, ['chat', 'audio', 'video'])) {
//                 $featureField = 'enable_' . $type;
//                 if (!$doctor->$featureField) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => ucfirst($type) . ' consultation is not enabled for this doctor']);
//                 }
//             }

//             // Check if slot is already booked
//             $existingAppointment = Appointment::where('doctor_id', $doctorId)
//                 ->where('appoinmtent_date ', $date)
//                 ->where('appointment_time', $time)
//                 ->whereIn('status', ['pending', 'confirmed'])
//                 ->first();

//             if ($existingAppointment) {
//                 return response()->json(['status' => 'error', 'code' => 400, 'message' => 'This time slot is already booked']);
//             }

//             // Calculate fee based on appointment type
//             $fee = 0;
//             $duration = $request->duration ?? 15;
            
//             if ($type == 'offline') {
//                 $fee = $doctor->consultation_fee ?? 0;
//             } else {
//                 $feeField = $type . '_fee';
//                 $feePerMinute = $doctor->$feeField ?? 0;
//                 $fee = $feePerMinute * $duration;
//             }

//             return response()->json([
//                 'status' => 'success',
//                 'code' => 200,
//                 'message' => 'Slot available',
//                 'data' => [
//                     'doctor_id' => $doctorId,
//                     'appoinmtent_date' => $date,
//                     'appointment_time' => $time,
//                     'appointment_type' => $type,
//                     'duration' => $duration,
//                     'consultation_fee' => $fee,
//                     'doctor_name' => $doctor->name,
//                     'doctor_speciality' => $doctor->speciality->name ?? 'N/A'
//                 ]
//             ]);

//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }

//     /**
//      * Book appointment - Updated for new features
//      */
//     public function book(Request $request)
//     {
//         try {
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $userId = $user ? $user->id : '';

//             // Get requested data      
//             $appointmentType = $request->input('appointment_type'); // Fixed spelling
//             $doctorId = $request->input('doctor_id');
//             $patientName = $request->input('patient_name') ?? $user->name; // Use logged in user's name if not provided
//             $patientMobileNumber = $request->input('patient_mobile_number') ?? $user->mobile_number;
//             $slotId = $request->input('slot_id');
//             $appointmentDate = $request->input('appoinmtent_date') ?? date("Y-m-d");
//             $appointmentTime = $request->input('appointment_time');
//             $paymentBy = $request->input('payment_by');
//             $reason = $request->input('reason');
       
//             $duration = $request->input('duration') ?? 15;

//             // Define validation rules based on appointment type
//             $validationRules = [
//                 'doctor_id' => 'required|exists:users,id',
//                 'appointment_type' => 'required|in:chat,audio,video,offline',
//                 'payment_by' => 'required|in:wallet,online',
//                 'reason' => 'required',
//             ];

//             // Additional validation for offline appointments
//             if ($appointmentType == 'offline') {
//                 $validationRules['slot_id'] = 'required';
//                 $validationRules['appoinmtent_date'] = 'required|date|after_or_equal:today';
//             } else {
//                 // For chat/audio/video appointments
//                 $validationRules['appoinmtent_date'] = 'required|date|after_or_equal:today';
//                 $validationRules['appointment_time'] = 'null';
//                 $validationRules['duration'] = 'required|integer|min:1|max:60';
//             }

//             // Validate the input data
//             $validation = Validator::make($request->all(), $validationRules);

//             if ($validation->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
//             }

//             // Check doctor availability and features
//             $doctor = User::where('id', $doctorId)
//                 ->where('role_id', 3)
//                 ->where('is_active', 1)
//                 ->first();

//             if (!$doctor) {
//                 return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Doctor not found or inactive']);
//             }

//             // For chat/audio/video, check if feature is enabled
//             if (in_array($appointmentType, ['chat', 'audio', 'video'])) {
//                 $featureField = 'enable_' . $appointmentType;
//                 if (!$doctor->$featureField) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => ucfirst($appointmentType) . ' consultation is not available']);
//                 }
//             }

//             // Calculate appointment fee
//             $appointmentFee = 0;
//             if ($appointmentType == 'offline') {
//                 $appointmentFee = $doctor->consultation_fee ?? 0;
                
//                 // Check slot availability for offline appointments
//                 if ($slotId) {
//                     $doctorAppointmentSlotCount = DB::table('appointments')
//                         ->where('doctor_id', $doctorId)
//                         ->where('slot_id', $slotId)
//                         ->where('appoinmtent_date', $appointmentDate)
//                         ->whereIn('status', ['pending', 'confirmed'])
//                         ->count();
                    
//                     $doctorSlotData = DB::table('doctor_time_slots')->where('id', $slotId)->first();
//                     $slotPatientLimit = $doctorSlotData->patient_limit ?? 0;
                    
//                     if ($doctorAppointmentSlotCount >= $slotPatientLimit) {
//                         return response()->json(['status' => 'error', 'code' => 400, 'message' => "This slot is not available"]);
//                     }
                    
//                     // Get time from slot
//                     $slotData = DB::table('doctor_time_slots')->where('id', $slotId)->first();
//                     $appointmentTime = $slotData->start_time ?? $appointmentTime;
//                 }
//             } else {
//                 // Calculate fee for chat/audio/video
//                 $feeField = $appointmentType . '_fee';
//                 $feePerMinute = $doctor->$feeField ?? 0;
//                 $appointmentFee = $feePerMinute * $duration;
                
//                 // Check time slot availability for online appointments
//                 $existingAppointment = Appointment::where('doctor_id', $doctorId)
//                     ->where('appoinmtent_date', $appointmentDate)
//                     ->where('appointment_time', $appointmentTime)
//                     ->whereIn('status', ['pending', 'confirmed'])
//                     ->first();
                    
//                 if ($existingAppointment) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => 'This time slot is already booked']);
//                 }
//             }

//             // Check wallet balance if payment by wallet
//             $userData = User::find($userId);
            
//             if ($paymentBy == 'wallet') {
//                 $walletAmount = $userData->wallet_amount;
//                 if ($walletAmount == 0) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Please recharge your wallet to book appointment']);
//                 }
                
//                 if ($appointmentFee > $walletAmount) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Insufficient balance']);
//                 }
//             }

//             // Prepare appointment data
//             $requestData = [
//                 'user_id' => $userId,
//                 'doctor_id' => $doctorId,
//                 'slot_id' => $slotId,
//                 'appoinmtent_date' =>$appointmentDate,
//                 'appointment_time' =>$appointmentTime,
//                 'appoinmtent_type' =>$appointmentType, // Fixed spelling
//                 'duration' => $duration,
//                 'consultation_fee' => $appointmentFee,
//                 'patient_name' => $patientName,
//                 'patient_mobile_number' => $patientMobileNumber,
//                 'payment_by' => $paymentBy,
//                 'reason' => $reason,
//               'status' => ($appointmentType == 'offline') ? 'pending' : 'confirmed',
//                 'payment_status' => 'pending',
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ];

//             // Insert appointment
//             $appointmentId = DB::table('appointments')->insertGetId($requestData);
//             $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

//             // Process payment
//             if ($paymentBy == 'wallet') {
//                 // Deduct from user's wallet
//                 $newAmount = $userData->wallet_amount - $appointmentFee;
//                 DB::table('users')->where('id', $userId)->update(['wallet_amount' => $newAmount]);
                
//                 // Add wallet transaction
//                 DB::table('wallet_transactions')->insert([
//                     'user_id' => $userId,
//                     'amount' => $appointmentFee,
//                     'order_id' => $appointmentId,
//                     'created_by' => $userId,
//                     'is_credit' => '0',
//                     'transaction_for' => "appointment",
//                     'description' => "Appointment booked with doctor",
//                     'created_at' => now(),
//                 ]);
                
//                 // Update appointment payment status
//                 DB::table('appointments')->where('id', $appointmentId)->update([
//                     'payment_status' => 'paid',
//                     'transaction_id' => 'WALLET_' . time() . '_' . $appointmentId
//                 ]);
//             } else {
//                 // Online payment - just log it
//                 DB::table('payment_logs')->insert([
//                     'user_id' => $userId,
//                     'amount' => $appointmentFee,
//                     'order_id' => $appointmentId,
//                     'created_by' => $userId,
//                     'description' => "Appointment booked with doctor",
//                     'created_at' => now(),
//                 ]);
//             }

//             // Calculate commission and update doctor's wallet
//             $commission = Helpers::get_settings('appointment_commission') ?? 0;
//             $adminShare = ($commission / 100) * $appointmentFee;
//             $doctorShare = $appointmentFee - $adminShare;

//             // Update doctor's wallet
//             DB::table('wallet_transactions')->insert([
//                 'user_id' => $doctorId,
//                 'amount' => $doctorShare,
//                 'order_id' => $appointmentId,
//                 'created_by' => $userId,
//                 'is_credit' => '1',
//                 'transaction_for' => "appointment",
//                 'description' => $patientName . " has booked an appointment with you",
//                 'created_at' => now(),
//             ]);

//             $doctorWalletAmount = $doctor->wallet_amount ?? 0;
//             $newDoctorWalletAmount = $doctorWalletAmount + $doctorShare;
//             DB::table('users')->where('id', $doctorId)->update(['wallet_amount' => $newDoctorWalletAmount]);

//             // Send notifications
//             $description = $patientName . " has booked a " . $appointmentType . " appointment with you";
            
//             if ($appointmentType == 'offline') {
//                 $slotData = DB::table('doctor_time_slots')->where('id', $slotId)->first();
//                 if ($slotData) {
//                     $appointmentDateTime = $appointmentDate . " " . ($slotData->start_time ?? '');
//                     $description .= " on " . $appointmentDateTime;
//                 }
//             } else {
//                 $description .= " on " . $appointmentDate . " at " . $appointmentTime;
//             }

//             // Notification for doctor
//             DB::table('notifications')->insert([
//                 'title' => "New Appointment",
//                 'from_user_id' => $userId,
//                 'to_user_id' => $doctorId,
//                 'description' => $description,
//                 'created_at' => now(),
//                 'updated_at' => now()
//             ]);

//             // Push notification to user
//             $userFcmToken = $userData->fcm_token ?? '';
//             if (!empty($userFcmToken)) {
//                 $data = [
//                     'title' => 'Appointment Booked',
//                     'description' => 'Your appointment has been booked successfully',
//                     'image' => '',
//                     'type' => 'appointment_status',
//                     'appointment_id' => $appointmentId
//                 ];
//                 Helpers::send_push_notif_to_device($userFcmToken, $data);
//             }

//             return response()->json([
//                 'status' => 'success',
//                 'code' => 200,
//                 'message' => 'Appointment booked successfully',
//                 'data' => $appointmentData
//             ]);

//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }

//     /**
//      * Get appointments - Updated with filters
//      */
//     public function getAppointments(Request $request)
//     {
//         try {
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $customerId = $user ? $user->id : '';
    
//             $page = $request->get('page', 1);
//             $status = $request->get('status', 'all'); // all, upcoming, completed, cancelled
//             $appointmentType = $request->get('appointment_type'); // chat, audio, video, offline
//             $perPage = 10;
    
//             // Validate input
//             $validation = Validator::make($request->all(), [
//                 'page' => 'required|integer|min:1',
//             ], [
//                 'page.required' => 'Page is required.',
//             ]);
    
//             if ($validation->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
//             }
    
//             // Build query
//             $query = Appointment::where('user_id', $customerId)
//                 ->with(['doctor' => function($q) {
//                     $q->select('id', 'name', 'speciality_id', 'image', 'mobile_number', 'email')
//                       ->with(['speciality' => function($sq) {
//                           $sq->select('id', 'name');
//                       }]);
//                 }]);
    
//             // Filter by appointment type
//             if ($appointmentType && in_array($appointmentType, ['chat', 'audio', 'video', 'offline'])) {
//                 $query->where('appointment_type', $appointmentType);
//             }
    
//             // Filter by status
//             if ($status != 'all') {
//                 if ($status == 'upcoming') {
//                     $today = Carbon::today()->toDateString();
//                     $query->where('appoinmtent_date', '>=', $today)
//                           ->whereIn('status', ['pending', 'confirmed']);
//                 } else {
//                     $query->where('status', $status);
//                 }
//             }
    
//             // Order and paginate
//             $appointments = $query->orderBy('appoinmtent_date', 'desc')
//                 ->orderBy('appointment_time', 'desc')
//                 ->paginate($perPage, ['*'], 'page', $page);
    
//             // Transform data
//             $appointments->getCollection()->transform(function ($appointment) use ($customerId) {
//                 // Format doctor image
//                 if ($appointment->doctor && $appointment->doctor->image) {
//                     $appointment->doctor->image = (env('APP_ENV') == 'local')
//                         ? asset('storage/users/' . $appointment->doctor->image)
//                         : asset('storage/app/public/users/' . $appointment->doctor->image);
//                 }
                
//                 // Add speciality name
//                 $appointment->doctor_name = $appointment->doctor->name ?? 'N/A';
//                 $appointment->speciality_name = $appointment->doctor->speciality->name ?? 'N/A';
                
//                 // Determine if appointment is upcoming
//                 $today = Carbon::today()->toDateString();
//                 $now = Carbon::now();
//                 $appointmentDateTime = Carbon::parse($appointment->appoinmtent_date  . ' ' . $appointment->appointment_time);
                
//                 $appointment->is_upcoming = $appointmentDateTime->gt($now) && 
//                                           in_array($appointment->status, ['pending', 'confirmed']);
                
//                 // Add patient details (from appointment record)
//                 $appointment->patient_name = $appointment->patient_name;
//                 $appointment->patient_mobile = $appointment->patient_mobile_number;
                
//                 return $appointment;
//             });
    
//             $responseData = [
//                 'status' => 'success',
//                 'message' => 'Appointments retrieved successfully',
//                 'code' => 200,
//                 'data' => [
//                     'appointments' => $appointments->items(),
//                     'paginator' => [
//                         'totalDocs' => $appointments->total(),
//                         'limit' => $appointments->perPage(),
//                         'page' => $appointments->currentPage(),
//                         'totalPages' => $appointments->lastPage(),
//                         'hasPrevPage' => $appointments->previousPageUrl() ? true : false,
//                         'hasNextPage' => $appointments->nextPageUrl() ? true : false,
//                     ],
//                 ]
//             ];
    
//             return response()->json($responseData);
    
//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }

//     /**
//      * Get appointment details
//      */
//     public function getAppointmentDetail(Request $request)
//     {
//         try {
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $customerId = $user ? $user->id : '';

//             $appointmentId = $request->get('appointment_id');

//             $validator = Validator::make($request->all(), [
//                 'appointment_id' => 'required|exists:appointments,id'
//             ]);

//             if ($validator->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
//             }

//             $appointment = Appointment::where('id', $appointmentId)
//                 ->where('user_id', $customerId)
//                 ->with(['doctor' => function($q) {
//                     $q->select('id', 'name', 'speciality_id', 'image', 'mobile_number', 'email', 
//                               'experience', 'qualification', 'description',
//                               'enable_chat', 'enable_audio', 'enable_video',
//                               'chat_fee', 'audio_fee', 'video_fee', 'consultation_fee')
//                       ->with(['speciality' => function($sq) {
//                           $sq->select('id', 'name');
//                       }]);
//                 }])
//                 ->first();

//             if (!$appointment) {
//                 return response()->json(['status' => 'error', 'message' => 'Appointment not found', 'code' => 404, 'data' => null]);
//             }

//             // Format doctor image
//             if ($appointment->doctor && $appointment->doctor->image) {
//                 $appointment->doctor->image = (env('APP_ENV') == 'local')
//                     ? asset('storage/users/' . $appointment->doctor->image)
//                     : asset('storage/app/public/users/' . $appointment->doctor->image);
//             }

//             // Add appointment type details
//             if (in_array($appointment->appointment_type, ['chat', 'audio', 'video'])) {
//                 $feeField = $appointment->appointment_type . '_fee';
//                 $appointment->fee_per_minute = $appointment->doctor->$feeField ?? 0;
//             } else {
//                 $appointment->fee_per_minute = $appointment->doctor->consultation_fee ?? 0;
//             }

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Appointment details retrieved successfully',
//                 'code' => 200,
//                 'data' => $appointment
//             ]);

//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }

//     /**
//      * Cancel appointment
//      */
//     public function cancelAppointment(Request $request, $id)
//     {
//         try {
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $patientId = $user ? $user->id : null;

//             $appointment = Appointment::where('id', $id)
//                 ->where('user_id', $patientId)
//                 ->first();

//             if (!$appointment) {
//                 return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Appointment not found']);
//             }

//             // Check if appointment can be cancelled
//             if (!in_array($appointment->status, ['pending', 'confirmed'])) {
//                 return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Appointment cannot be cancelled']);
//             }

//             // Check cancellation window (2 hours before appointment)
//             $appointmentDateTime = Carbon::parse($appointment->appoinmtent_date  . ' ' . $appointment->appointment_time);
//             $now = Carbon::now();
            
//             if ($now->diffInHours($appointmentDateTime, false) < 2) {
//                 return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Appointment can only be cancelled 2 hours before the scheduled time']);
//             }

//             // Update status
//             $appointment->update(['status' => 'cancelled']);

//             // Refund to wallet if paid
//             if ($appointment->payment_status == 'paid' && $appointment->payment_by == 'wallet') {
//                 $user = User::find($patientId);
//                 $newAmount = $user->wallet_amount + $appointment->consultation_fee;
//                 $user->update(['wallet_amount' => $newAmount]);
                
//                 // Log refund transaction
//                 DB::table('wallet_transactions')->insert([
//                     'user_id' => $patientId,
//                     'amount' => $appointment->consultation_fee,
//                     'order_id' => $appointment->id,
//                     'created_by' => $patientId,
//                     'is_credit' => '1',
//                     'transaction_for' => "appointment_refund",
//                     'description' => "Refund for cancelled appointment",
//                     'created_at' => now(),
//                 ]);
//             }

//             // Notify doctor
//             DB::table('notifications')->insert([
//                 'title' => "Appointment Cancelled",
//                 'from_user_id' => $patientId,
//                 'to_user_id' => $appointment->doctor_id,
//                 'description' => $appointment->patient_name . " has cancelled the appointment",
//                 'created_at' => now(),
//                 'updated_at' => now()
//             ]);

//             return response()->json([
//                 'status' => 'success',
//                 'code' => 200,
//                 'message' => 'Appointment cancelled successfully',
//                 'data' => $appointment
//             ]);

//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }

//     /**
//      * Get available time slots for doctor
//      */
//     public function getSlots(Request $request)
//     {
//         try {
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $doctorId = $request->doctor_id;
//             $date = $request->date ?? date("Y-m-d");
//             $appointmentType = $request->appointment_type ?? 'offline';
//             $dayName = date("l", strtotime($date));
    
//             // Validate input
//             $validator = Validator::make($request->all(), [
//                 'doctor_id' => 'required|exists:users,id',
//                 'date' => 'required|date',
//             ]);
    
//             if ($validator->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
//             }
    
//             // For offline appointments, get time slots from doctor_time_slots table
//             if ($appointmentType == 'offline') {
//                 $slots = DB::table('doctor_time_slots')
//                     ->where('doctor_id', $doctorId)
//                     ->where('day_name', $dayName)
//                     ->where('is_available', 1)
//                     ->orderBy('start_time', 'asc')
//                     ->get();
    
//                 // Check availability for each slot
//                 $availableSlots = [];
//                 foreach ($slots as $slot) {
//                     $bookedCount = Appointment::where('doctor_id', $doctorId)
//                         ->where('slot_id', $slot->id)
//                         ->where('appoinmtent_date', $date)
//                         ->whereIn('status', ['pending', 'confirmed'])
//                         ->count();
    
//                     $available = $bookedCount < $slot->patient_limit;
                    
//                     $availableSlots[] = [
//                         'id' => $slot->id,
//                         'doctor_id' => $slot->doctor_id,
//                         'day_name' => $slot->day_name,
//                         'start_time' => $slot->start_time,
//                         'end_time' => $slot->end_time,
//                         'patient_limit' => $slot->patient_limit,
//                         'is_available' => $slot->is_available,
//                         'booked_count' => $bookedCount,
//                         'available' => $available,
//                         'available_slots' => $slot->patient_limit - $bookedCount
//                     ];
//                 }
    
//                 return response()->json([
//                     'status' => 'success',
//                     'message' => 'Slots retrieved successfully',
//                     'code' => 200,
//                     'data' => $availableSlots
//                 ]);
//             } else {
//                 // For online appointments (chat/audio/video), generate time slots
//                 // You can implement your own logic for online appointment slots
//                 return response()->json([
//                     'status' => 'success',
//                     'message' => 'Online appointments can be booked at any available time',
//                     'code' => 200,
//                     'data' => []
//                 ]);
//             }
    
//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }



    
//     public function getPaymentDetail(Request $request){
            
//         $pay_for = $request->payment_for;
//         $order_id = $request->order_id;
        
//         if($pay_for == 'medicine'){

//              $order = DB::table('orders')->where('id', $order_id)->first();
//         } 
        
        
        
//         if($pay_for == 'lab_test'){
//             $order =    DB::table('users_lab_tests')->where('id',$order_id)->first();

//         }

//         if($pay_for == 'appointment')
//         {
//           $order = DB::table('appointments')->where('id', $order_id)->first();


//         }
        
//         if($pay_for == 'wallet')
//         {
//             $order = DB::table('payment_logs')->where('id',$order_id)->first();
//             $order->payment_status = $order->status;

//         }
        
//              return response()->json(['status' => 'success', 'code' => 200,'message' => 'Data found','data' => $order]);

        
//     }
    
//         public function downloadMedicalPrescription(Request $request){
        
//             $appointmentId = $request->appointment_id;
//             $patientId = $request->patient_id;
  
         
//             $medicineData = DB::table('users_medicine')->where(array('appointment_id' => $appointmentId,'patient_id' => $patientId))->first();
        
            
//             if (isset($medicineData)) { 
//               $businessSettingData = BusinessSetting::where('key','logo')->first(); 
//               $businessSettingLogoPath = (isset($businessSettingData) && !empty($businessSettingData)) ? "/storage/app/public/business/".$businessSettingData->value : '';
               
              
//               $path = base_path($businessSettingLogoPath);;
//               $type = pathinfo($path,PATHINFO_EXTENSION);
              
//               $data = file_get_contents($path);
//               $logoPath = 'data:image/'. $type .';base64,'. base64_encode($data);
//               $appointmentData = Appointment::find($appointmentId);
               
//               $pdf = PDF::setOptions(['debugKeepTemp' => true,'defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true, 'isPhpEnabled' => true])->loadView('prescription',compact('medicineData','appointmentData','logoPath'));
//               return $pdf->download('Prescription'.'# '.$medicineData->id.'.pdf');
//             } else {
//             return response()->json(['status' => 'error', 'message' => 'No Data found', 'code' => 200, 'data' => null]);
//             }

        
//     }
    
//       public function getPaymentLogs(Request $request){
//         try {
//             // Get requested data
//             $page = $request->get('page');
//             $orderBy =  'desc';
//             $orderColumn = 'created_at';
//             $perPage = 10; 
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $userId = (isset($user) && !empty($user)) ? $user->id : '';

    
//             // Define the validation rules
//             $validationRules = [
//                 'page' => 'required',
//             ];
    
//             // Validate the input data
//             $validation = Validator::make($request->all(), $validationRules, [
//                 'page.required' => 'Page is required.',
//             ]);
    
//             // Check for validation errors and return error response if any
//             if ($validation->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
//             }
    
//             // Query to retrieve items
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $userId = $user ? $user->id : null;
//             $transactions = DB::table('payment_logs')->select('*')->where(array('user_id'=>$userId ))->orderBy('id','desc')->paginate($perPage, ['*'], 'page', $page);     
//             $transactions->getCollection()->transform(function ($transaction)  {
                
//                 $transaction->amount = (string) $transaction->amount;

//                 return $transaction;
               
//             });
            
            
//             if($transactions->total() > 0){
//                 $status = "success";
//                 $message = "Data found";
//             } else {
//                 $status = "error";
//                 $message = "No data found";
//             }
    
//             $responseData = [
//                 'status' => $status,
//                 'message' => $message,
//                 'code' => 200,
//                 'data' => [
//                     'data' => $transactions->items(),
//                     'paginator' => [
//                         'totalDocs' => $transactions->total(),
//                         'limit' => $transactions->perPage(),
//                         'page' => $transactions->currentPage(),
//                         'totalPages' => $transactions->lastPage(),
//                         'hasPrevPage' => $transactions->previousPageUrl() ? true : false,
//                         'hasNextPage' => $transactions->nextPageUrl() ? true : false,
//                     ],
//                 ]
//             ];

//             return response()->json($responseData);
    
//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }
// }


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\UsersAddress;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Appointment;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use PDF;
use Stripe\Charge;
use Stripe\Stripe;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Check doctor availability for new appointment types
     */
    public function checkAvailability(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user ? $user->id : null;

            $validator = Validator::make($request->all(), [
                'doctor_id' => 'required|exists:users,id',
                'appoinmtent_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required',
                'appointment_type' => 'required|in:chat,audio,video,offline',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
            }

            $doctorId = $request->doctor_id;
            $date = $request->appoinmtent_date;
            $time = $request->appointment_time;
            $type = $request->appointment_type;

            // Check if doctor is active
            $doctor = User::where('id', $doctorId)
                ->where('role_id', 3)
                ->where('is_active', 1)
                ->first();

            if (!$doctor) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Doctor not found or inactive']);
            }

            // For chat/audio/video, check if feature is enabled
            if (in_array($type, ['chat', 'audio', 'video'])) {
                $featureField = 'enable_' . $type;
                if (!$doctor->$featureField) {
                    return response()->json(['status' => 'error', 'code' => 400, 'message' => ucfirst($type) . ' consultation is not enabled for this doctor']);
                }
            }

            // Check if slot is already booked
            $existingAppointment = Appointment::where('doctor_id', $doctorId)
                ->where('appoinmtent_date', $date)
                ->where('appointment_time', $time)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if ($existingAppointment) {
                return response()->json(['status' => 'error', 'code' => 400, 'message' => 'This time slot is already booked']);
            }

            // Calculate fee based on appointment type
            $fee = 0;
            $feePerMinute = 0;
            $duration = $request->duration ?? 15;
            
            if ($type == 'offline') {
                $fee = $doctor->consultation_fee ?? 0;
                $feePerMinute = $doctor->consultation_fee ?? 0;
            } else {
                $feeField = $type . '_fee';
                $feePerMinute = $doctor->$feeField ?? 0;
                $fee = $feePerMinute * $duration;
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Slot available',
                'data' => [
                    'doctor_id' => $doctorId,
                    'appoinmtent_date' => $date,
                    'appointment_time' => $time,
                    'appointment_type' => $type,
                    'duration' => $duration,
                    'fee_per_minute' => $feePerMinute,
                    'total_fee' => $fee,
                    'doctor_name' => $doctor->name,
                    'doctor_speciality' => $doctor->speciality->name ?? 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Book appointment - Updated for new features
     */
//   public function book(Request $request)
//     {
//         try {
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $userId = $user ? $user->id : '';

//             // Get requested data      
//             $appointmentType = $request->input('appointment_type');
//             $doctorId = $request->input('doctor_id');
//             $patientName = $request->input('patient_name') ?? $user->name;
//             $patientMobileNumber = $request->input('patient_mobile_number') ?? $user->mobile_number;
//             $slotId = $request->input('slot_id');
//             $appointmentDate = $request->input('appoinmtent_date') ?? date("Y-m-d");
//             $appointmentTime = $request->input('appointment_time');
//             $paymentBy = $request->input('payment_by');
//             $reason = $request->input('reason');
//             $symptoms = $request->input('symptoms');
//             $medicalHistory = $request->input('medical_history');
//             $duration = $request->input('duration') ?? 15;

//             // Define validation rules based on appointment type
//             $validationRules = [
//                 'doctor_id' => 'required|exists:users,id',
//                 'appointment_type' => 'required|in:chat,audio,video,offline',
//                 'payment_by' => 'required|in:wallet,online',
//                 'reason' => 'required',
//                 'appoinmtent_date' => 'required|date|after_or_equal:today',
//                 'appointment_time' => 'required',
//                 'duration' => 'required|integer|min:1|max:60',
//             ];

//             // Validate the input data
//             $validation = Validator::make($request->all(), $validationRules);

//             if ($validation->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
//             }

//             // Check doctor availability and features
//             $doctor = User::where('id', $doctorId)
//                 ->where('role_id', 3)
//                 ->where('is_active', 1)
//                 ->first();

//             if (!$doctor) {
//                 return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Doctor not found or inactive']);
//             }

//             // For chat/audio/video, check if feature is enabled
//             if (in_array($appointmentType, ['chat', 'audio', 'video'])) {
//                 $featureField = 'enable_' . $appointmentType;
//                 if (!$doctor->$featureField) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => ucfirst($appointmentType) . ' consultation is not available']);
//                 }
//             }

//             // Calculate appointment fee based on type
//             $appointmentFee = 0;
//             $feePerMinute = 0;
            
//             if ($appointmentType == 'offline') {
//                 $appointmentFee = $doctor->consultation_fee ?? 0;
//                 $feePerMinute = $doctor->consultation_fee ?? 0;
//             } else {
//                 // Calculate fee for chat/audio/video based on doctor's fees
//                 if ($appointmentType == 'chat') {
//                     $feePerMinute = $doctor->chat_fee ?? 0;
//                 } elseif ($appointmentType == 'audio') {
//                     $feePerMinute = $doctor->audio_fee ?? 0;
//                 } elseif ($appointmentType == 'video') {
//                     $feePerMinute = $doctor->video_fee ?? 0;
//                 }
                
//                 $appointmentFee = $feePerMinute * $duration;
                
//                 // Check if fee is set for this consultation type
//                 if ($feePerMinute <= 0) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => ucfirst($appointmentType) . ' consultation fee is not set for this doctor']);
//                 }
//             }

//             // Check time slot availability for all appointments
//             $existingAppointment = Appointment::where('doctor_id', $doctorId)
//                 ->where('appoinmtent_date', $appointmentDate)
//                 ->where('appointment_time', $appointmentTime)
//                 ->whereIn('status', ['pending', 'confirmed'])
//                 ->first();
                
//             if ($existingAppointment) {
//                 return response()->json(['status' => 'error', 'code' => 400, 'message' => 'This time is already booked']);
//             }

//             // Check wallet balance if payment by wallet
//             $userData = User::find($userId);
            
//             if ($paymentBy == 'wallet') {
//                 $walletAmount = $userData->wallet_amount ?? 0;
//                 if ($walletAmount == 0) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Please recharge your wallet to book appointment']);
//                 }
                
//                 if ($appointmentFee > $walletAmount) {
//                     return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Insufficient balance. Required: ' . $appointmentFee . ', Available: ' . $walletAmount]);
//                 }
//             }

//             // Prepare appointment data
//             $requestData = [
//                 'user_id' => $userId,
//                 'doctor_id' => $doctorId,
//                 'appoinmtent_date' => $appointmentDate,
//                 'appointment_time' => $appointmentTime,
//                 'appoinmtent_type' => $appointmentType,
//                 'duration' => $duration,
//                 'consultation_fee' => $appointmentFee,
//                 'patient_name' => $patientName,
//                 'patient_mobile_number' => $patientMobileNumber,
//                 'payment_by' => $paymentBy,
//                 'reason' => $reason,
//                 'status' => ($appointmentType == 'offline') ? 'pending' : 'confirmed',
//                 'payment_status' => 'pending',
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ];

//             // Add optional fields if they exist in database
//             $columns = DB::select('SHOW COLUMNS FROM appointments');
//             $columnNames = array_column($columns, 'Field');
            
//             if (in_array('symptoms', $columnNames)) {
//                 $requestData['symptoms'] = $symptoms;
//             }
            
//             if (in_array('medical_history', $columnNames)) {
//                 $requestData['medical_history'] = $medicalHistory;
//             }
            
//             if (in_array('patient_name', $columnNames)) {
//                 $requestData['patient_name'] = $patientName;
//             }

//             // Insert appointment
//             $appointmentId = DB::table('appointments')->insertGetId($requestData);
//             $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

//             // Process payment
//             if ($paymentBy == 'wallet') {
//                 // Deduct from user's wallet
//                 $newAmount = $userData->wallet_amount - $appointmentFee;
//                 DB::table('users')->where('id', $userId)->update(['wallet_amount' => $newAmount]);
                
//                 // Add wallet transaction
//                 DB::table('wallet_transactions')->insert([
//                     'user_id' => $userId,
//                     'amount' => $appointmentFee,
//                     'order_id' => $appointmentId,
//                     'created_by' => $userId,
//                     'is_credit' => '0',
//                     'transaction_for' => "appointment",
//                     'description' => "Appointment booked with " . $doctor->name,
//                     'created_at' => now(),
//                 ]);
                
//                 // Update appointment payment status
//                 DB::table('appointments')->where('id', $appointmentId)->update([
//                     'payment_status' => 'paid',
//                     'transaction_id' => 'WALLET_' . time() . '_' . $appointmentId
//                 ]);
//             } else {
//                 // Online payment - just log it
//                 DB::table('payment_logs')->insert([
//                     'user_id' => $userId,
//                     'amount' => $appointmentFee,
//                     'order_id' => $appointmentId,
//                     'created_by' => $userId,
//                     'description' => "Appointment booked with " . $doctor->name,
//                     'created_at' => now(),
//                 ]);
//             }

//             // Calculate commission and update doctor's wallet
//             // First check if doctor has a specific commission percentage set
//             $commission = 0;
//             if (isset($doctor->commission_percentage) && $doctor->commission_percentage > 0) {
//                 $commission = $doctor->commission_percentage;
//             } else {
//                 // Fallback to global setting
//                 $commission = Helpers::get_settings('appointment_commission') ?? 0;
//             }
            
//             $adminShare = ($commission / 100) * $appointmentFee;
//             $doctorShare = $appointmentFee - $adminShare;

//             // Update doctor's wallet
//             DB::table('wallet_transactions')->insert([
//                 'user_id' => $doctorId,
//                 'amount' => $doctorShare,
//                 'order_id' => $appointmentId,
//                 'created_by' => $userId,
//                 'is_credit' => '1',
//                 'transaction_for' => "appointment",
//                 'description' => $patientName . " has booked a " . $appointmentType . " appointment with you",
//                 'created_at' => now(),
//             ]);

//             $doctorWalletAmount = $doctor->wallet_amount ?? 0;
//             $newDoctorWalletAmount = $doctorWalletAmount + $doctorShare;
//             DB::table('users')->where('id', $doctorId)->update(['wallet_amount' => $newDoctorWalletAmount]);

//             // Send notifications using centralized helper
//             // Helpers::sendAppointmentBookedNotification(
//             //     $userId,
//             //     $doctorId,
//             //     $appointmentId,
//             //     $appointmentType,
//             //     $appointmentDate,
//             //     $appointmentTime,
//             //     $patientName,
//             //     $appointmentFee,
//             //     $duration
//             // );

//             // Add fee details to response
//             $appointmentData->fee_details = [
//                 'appointment_type' => $appointmentType,
//                 'fee_per_minute' => $feePerMinute,
//                 'duration_minutes' => $duration,
//                 'total_fee' => $appointmentFee,
//                 'doctor_name' => $doctor->name,
//             ];

//             return response()->json([
//                 'status' => 'success',
//                 'code' => 200,
//                 'message' => ucfirst($appointmentType) . ' appointment booked successfully',
//                 'data' => $appointmentData
//             ]);

//         } catch (\Exception $e) {
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }





public function book(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user ? $user->id : '';

            // Get requested data      
            $appointmentType = $request->input('appointment_type');
            $doctorId = $request->input('doctor_id');
            $patientName = $request->input('patient_name') ?? $user->name;
            $patientMobileNumber = $request->input('patient_mobile_number') ?? $user->mobile_number;
            $slotId = $request->input('slot_id');
            $appointmentDate = $request->input('appoinmtent_date') ?? date("Y-m-d");
            $appointmentTime = $request->input('appointment_time');
            $paymentBy = $request->input('payment_by');
            $reason = $request->input('reason');
            $symptoms = $request->input('symptoms');
            $medicalHistory = $request->input('medical_history');
            $duration = $request->input('duration') ?? 15;

            // Define validation rules based on appointment type
            $validationRules = [
                'doctor_id' => 'required|exists:users,id',
                'appointment_type' => 'required|in:chat,audio,video,offline',
                'payment_by' => 'required|in:wallet,online',
                'reason' => 'required',
                'appoinmtent_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required',
                'duration' => 'required|integer|min:1|max:60',
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules);

            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            // Check doctor availability and features
            $doctor = User::where('id', $doctorId)
                ->where('role_id', 3)
                ->where('is_active', 1)
                ->first();

            if (!$doctor) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Doctor not found or inactive']);
            }

            // For chat/audio/video, check if feature is enabled
            if (in_array($appointmentType, ['chat', 'audio', 'video'])) {
                $featureField = 'enable_' . $appointmentType;
                if (!$doctor->$featureField) {
                    return response()->json(['status' => 'error', 'code' => 400, 'message' => ucfirst($appointmentType) . ' consultation is not available']);
                }
            }

            // Calculate appointment fee based on type
            $appointmentFee = 0;
            $feePerMinute = 0;
            
            if ($appointmentType == 'offline') {
                $appointmentFee = $doctor->consultation_fee ?? 0;
                $feePerMinute = $doctor->consultation_fee ?? 0;
            } else {
                // Calculate fee for chat/audio/video based on doctor's fees
                if ($appointmentType == 'chat') {
                    $feePerMinute = $doctor->chat_fee ?? 0;
                } elseif ($appointmentType == 'audio') {
                    $feePerMinute = $doctor->audio_fee ?? 0;
                } elseif ($appointmentType == 'video') {
                    $feePerMinute = $doctor->video_fee ?? 0;
                }
                
                $appointmentFee = $feePerMinute * $duration;
                
                // Check if fee is set for this consultation type
                if ($feePerMinute <= 0) {
                    return response()->json(['status' => 'error', 'code' => 400, 'message' => ucfirst($appointmentType) . ' consultation fee is not set for this doctor']);
                }
            }

            // Check time slot availability for all appointments
            $existingAppointment = Appointment::where('doctor_id', $doctorId)
                ->where('appoinmtent_date', $appointmentDate)
                ->where('appointment_time', $appointmentTime)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();
                
            if ($existingAppointment) {
                return response()->json(['status' => 'error', 'code' => 400, 'message' => 'This time is already booked']);
            }

            // Check wallet balance if payment by wallet
            $userData = User::find($userId);
            
            if ($paymentBy == 'wallet') {
                $walletAmount = $userData->wallet_amount ?? 0;
                if ($walletAmount == 0) {
                    return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Please recharge your wallet to book appointment']);
                }
                
                if ($appointmentFee > $walletAmount) {
                    return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Insufficient balance. Required: ' . $appointmentFee . ', Available: ' . $walletAmount]);
                }
            }

            // Prepare appointment data
            $requestData = [
                'user_id' => $userId,
                'doctor_id' => $doctorId,
                'appoinmtent_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'appoinmtent_type' => $appointmentType,
                'duration' => $duration,
                'consultation_fee' => $appointmentFee,
                'patient_name' => $patientName,
                'patient_mobile_number' => $patientMobileNumber,
                'payment_by' => $paymentBy,
                'reason' => $reason,
                'status' => ($appointmentType == 'offline') ? 'pending' : 'confirmed',
                'payment_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Add optional fields if they exist in database
            $columns = DB::select('SHOW COLUMNS FROM appointments');
            $columnNames = array_column($columns, 'Field');
            
            if (in_array('symptoms', $columnNames)) {
                $requestData['symptoms'] = $symptoms;
            }
            
            if (in_array('medical_history', $columnNames)) {
                $requestData['medical_history'] = $medicalHistory;
            }
            
            if (in_array('patient_name', $columnNames)) {
                $requestData['patient_name'] = $patientName;
            }

            // Insert appointment
            $appointmentId = DB::table('appointments')->insertGetId($requestData);
            $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

            // Process payment
            if ($paymentBy == 'wallet') {
                // Deduct from user's wallet
                // Deduct from user's wallet
                DB::table('users')->where('id', $userId)->decrement('wallet_amount', $appointmentFee);
                
                // Add wallet transaction
                DB::table('wallet_transactions')->insert([
                    'user_id' => $userId,
                    'amount' => $appointmentFee,
                    'order_id' => $appointmentId,
                    'created_by' => $userId,
                    'is_credit' => '0',
                    'transaction_for' => "appointment",
                    'description' => "Appointment booked with " . $doctor->name,
                    'created_at' => now(),
                ]);
                
                // Update appointment payment status
                DB::table('appointments')->where('id', $appointmentId)->update([
                    'payment_status' => 'paid',
                    'transaction_id' => 'WALLET_' . time() . '_' . $appointmentId
                ]);
            } else {
                // Online payment - just log it
                DB::table('payment_logs')->insert([
                    'user_id' => $userId,
                    'amount' => $appointmentFee,
                    'order_id' => $appointmentId,
                    'created_by' => $userId,
                    'description' => "Appointment booked with " . $doctor->name,
                    'created_at' => now(),
                ]);
            }

            // Calculate commission and update doctor's wallet
            // First check if doctor has a specific commission percentage set
            $commission = 0;
            if (isset($doctor->commission_percentage) && $doctor->commission_percentage > 0) {
                $commission = $doctor->commission_percentage;
            } else {
                // Fallback to global setting
                $commission = Helpers::get_settings('appointment_commission') ?? 0;
            }
            
            $adminShare = ($commission / 100) * $appointmentFee;
            $doctorShare = $appointmentFee - $adminShare;

            // Update doctor's wallet
            DB::table('wallet_transactions')->insert([
                'user_id' => $doctorId,
                'amount' => $doctorShare,
                'order_id' => $appointmentId,
                'created_by' => $userId,
                'is_credit' => '1',
                'transaction_for' => "appointment",
                'description' => $patientName . " has booked a " . $appointmentType . " appointment with you",
                'created_at' => now(),
            ]);

            // Update doctor's wallet
            if ($doctorShare > 0) {
                DB::table('users')->where('id', $doctorId)->increment('wallet_amount', $doctorShare);
            }

            // Send notifications using centralized helper
            // NotificationHelper::sendAppointmentBookedNotification(
            //     $userId,
            //     $doctorId,
            //     $appointmentId,
            //     $appointmentType,
            //     $appointmentDate,
            //     $appointmentTime,
            //     $patientName,
            //     $appointmentFee,
            //     $duration
            // );

            // Add fee details to response
            $appointmentData->fee_details = [
                'appointment_type' => $appointmentType,
                'fee_per_minute' => $feePerMinute,
                'duration_minutes' => $duration,
                'total_fee' => $appointmentFee,
                'doctor_name' => $doctor->name,
            ];

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => ucfirst($appointmentType) . ' appointment booked successfully',
                'data' => $appointmentData
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    /**
     * Get appointments - Updated with filters
     */
    public function getAppointments(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $customerId = $user ? $user->id : '';
    
            $page = $request->get('page', 1);
            $status = $request->get('status', 'all');
            $appointmentType = $request->get('appointment_type');
            $perPage = 10;
    
            // Validate input
            $validation = Validator::make($request->all(), [
                'page' => 'required|integer|min:1',
            ], [
                'page.required' => 'Page is required.',
            ]);
    
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }
    
            // Build query
            $query = Appointment::where('user_id', $customerId)
                ->with(['doctor' => function($q) {
                    $q->select('id', 'name', 'speciality_id', 'image', 'mobile_number', 'email',
                              'chat_fee', 'audio_fee', 'video_fee', 'consultation_fee',
                              'enable_chat', 'enable_audio', 'enable_video')
                      ->with(['speciality' => function($sq) {
                          $sq->select('id', 'name');
                      }]);
                }]);
    
            // Filter by appointment type
            if ($appointmentType && in_array($appointmentType, ['chat', 'audio', 'video', 'offline'])) {
                $query->where('appoinmtent_type', $appointmentType);
            }
    
            // Filter by status
            if ($status != 'all') {
                if ($status == 'upcoming') {
                    $today = Carbon::today()->toDateString();
                    $query->where('appoinmtent_date', '>=', $today)
                          ->whereIn('status', ['pending', 'confirmed']);
                } else {
                    $query->where('status', $status);
                }
            }
    
            // Order and paginate
            $appointments = $query->orderBy('appoinmtent_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
    
            // Transform data
            $appointments->getCollection()->transform(function ($appointment) {
                // Format doctor image
                if ($appointment->doctor && $appointment->doctor->image) {
                    $appointment->doctor->image = (env('APP_ENV') == 'local')
                        ? asset('storage/users/' . $appointment->doctor->image)
                        : asset('storage/app/public/users/' . $appointment->doctor->image);
                }
                
                // Add speciality name
                $appointment->doctor_name = $appointment->doctor->name ?? 'N/A';
                $appointment->speciality_name = $appointment->doctor->speciality->name ?? 'N/A';
                
                // Calculate fee per minute based on appointment type
                if (in_array($appointment->appoinmtent_type, ['chat', 'audio', 'video'])) {
                    $feeField = $appointment->appoinmtent_type . '_fee';
                    $appointment->fee_per_minute = $appointment->doctor->$feeField ?? 0;
                } else {
                    $appointment->fee_per_minute = $appointment->doctor->consultation_fee ?? 0;
                }
                
                // Determine if appointment is upcoming
                $today = Carbon::today()->toDateString();
                $now = Carbon::now();
                $appointmentDateTime = Carbon::parse($appointment->appoinmtent_date . ' ' . $appointment->appointment_time);
                
                $appointment->is_upcoming = $appointmentDateTime->gt($now) && 
                                           in_array($appointment->status, ['pending', 'confirmed']);
                
                return $appointment;
            });
    
            $responseData = [
                'status' => 'success',
                'message' => 'Appointments retrieved successfully',
                'code' => 200,
                'data' => [
                    'appointments' => $appointments->items(),
                    'paginator' => [
                        'totalDocs' => $appointments->total(),
                        'limit' => $appointments->perPage(),
                        'page' => $appointments->currentPage(),
                        'totalPages' => $appointments->lastPage(),
                        'hasPrevPage' => $appointments->previousPageUrl() ? true : false,
                        'hasNextPage' => $appointments->nextPageUrl() ? true : false,
                    ],
                ]
            ];
    
            return response()->json($responseData);
    
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get appointment details
     */
    public function getAppointmentDetail(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $customerId = $user ? $user->id : '';

            $appointmentId = $request->get('appointment_id');

            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
            }

            $appointment = Appointment::where('id', $appointmentId)
                ->where('user_id', $customerId)
                ->with(['doctor' => function($q) {
                    $q->select('id', 'name', 'speciality_id', 'image', 'mobile_number', 'email', 
                               'experience', 'qualification', 'description',
                               'enable_chat', 'enable_audio', 'enable_video',
                               'chat_fee', 'audio_fee', 'video_fee', 'consultation_fee')
                      ->with(['speciality' => function($sq) {
                          $sq->select('id', 'name');
                      }]);
                }])
                ->first();

            if (!$appointment) {
                return response()->json(['status' => 'error', 'message' => 'Appointment not found', 'code' => 404, 'data' => null]);
            }

            // Format doctor image
            if ($appointment->doctor && $appointment->doctor->image) {
                $appointment->doctor->image = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $appointment->doctor->image)
                    : asset('storage/app/public/users/' . $appointment->doctor->image);
            }

            // Add appointment type details
            if (in_array($appointment->appoinmtent_type, ['chat', 'audio', 'video'])) {
                $feeField = $appointment->appoinmtent_type . '_fee';
                $appointment->fee_per_minute = $appointment->doctor->$feeField ?? 0;
            } else {
                $appointment->fee_per_minute = $appointment->doctor->consultation_fee ?? 0;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment details retrieved successfully',
                'code' => 200,
                'data' => $appointment
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Cancel appointment
     */
    public function cancelAppointment(Request $request, $id)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $patientId = $user ? $user->id : null;

            $appointment = Appointment::where('id', $id)
                ->where('user_id', $patientId)
                ->first();

            if (!$appointment) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Appointment not found']);
            }

            // Check if appointment can be cancelled
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Appointment cannot be cancelled']);
            }

            // Check cancellation window (2 hours before appointment)
            $appointmentDateTime = Carbon::parse($appointment->appoinmtent_date . ' ' . $appointment->appointment_time);
            $now = Carbon::now();
            
            if ($now->diffInHours($appointmentDateTime, false) < 2) {
                return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Appointment can only be cancelled 2 hours before the scheduled time']);
            }

            // Update status
            $appointment->update(['status' => 'cancelled']);

            // Refund to wallet if paid
            if ($appointment->payment_status == 'paid' && $appointment->payment_by == 'wallet') {
                $user = User::find($patientId);
                $newAmount = $user->wallet_amount + $appointment->consultation_fee;
                $user->update(['wallet_amount' => $newAmount]);
                
                // Log refund transaction
                DB::table('wallet_transactions')->insert([
                    'user_id' => $patientId,
                    'amount' => $appointment->consultation_fee,
                    'order_id' => $appointment->id,
                    'created_by' => $patientId,
                    'is_credit' => '1',
                    'transaction_for' => "appointment_refund",
                    'description' => "Refund for cancelled appointment",
                    'created_at' => now(),
                ]);
            }

            // Notify doctor
            DB::table('notifications')->insert([
                'title' => "Appointment Cancelled",
                'from_user_id' => $patientId,
                'to_user_id' => $appointment->doctor_id,
                'description' => $appointment->patient_name . " has cancelled the appointment",
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Appointment cancelled successfully',
                'data' => $appointment
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get available time slots for doctor
     */
    public function getSlots(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Slot feature has been removed. Please book using direct time.',
            'code' => 404
        ]);
    }

    public function getPaymentDetail(Request $request)
    {
        $pay_for = $request->payment_for;
        $order_id = $request->order_id;
        
        if($pay_for == 'medicine'){
            $order = DB::table('orders')->where('id', $order_id)->first();
        } 
        elseif($pay_for == 'lab_test'){
            $order = DB::table('users_lab_tests')->where('id',$order_id)->first();
        }
        elseif($pay_for == 'appointment') {
            $order = DB::table('appointments')->where('id', $order_id)->first();
        }
        elseif($pay_for == 'wallet') {
            $order = DB::table('payment_logs')->where('id',$order_id)->first();
            $order->payment_status = $order->status;
        }
        
        return response()->json(['status' => 'success', 'code' => 200,'message' => 'Data found','data' => $order]);
    }
    
    public function downloadMedicalPrescription(Request $request)
    {
        $appointmentId = $request->appointment_id;
        $patientId = $request->patient_id;
  
        $medicineData = DB::table('users_medicine')->where(array('appointment_id' => $appointmentId,'patient_id' => $patientId))->first();
        
        if (isset($medicineData)) { 
            $businessSettingData = BusinessSetting::where('key','logo')->first(); 
            $businessSettingLogoPath = (isset($businessSettingData) && !empty($businessSettingData)) ? "/storage/app/public/business/".$businessSettingData->value : '';
            
            $path = base_path($businessSettingLogoPath);;
            $type = pathinfo($path,PATHINFO_EXTENSION);
            
            $data = file_get_contents($path);
            $logoPath = 'data:image/'. $type .';base64,'. base64_encode($data);
            $appointmentData = Appointment::find($appointmentId);
            
            $pdf = PDF::setOptions(['debugKeepTemp' => true,'defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true, 'isPhpEnabled' => true])->loadView('prescription',compact('medicineData','appointmentData','logoPath'));
            return $pdf->download('Prescription'.'# '.$medicineData->id.'.pdf');
        } else {
            return response()->json(['status' => 'error', 'message' => 'No Data found', 'code' => 200, 'data' => null]);
        }
    }
    
    public function getPaymentLogs(Request $request)
    {
        try {
            $page = $request->get('page');
            $orderBy = 'desc';
            $orderColumn = 'created_at';
            $perPage = 10; 
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user ? $user->id : '';

            $validation = Validator::make($request->all(), [
                'page' => 'required',
            ], [
                'page.required' => 'Page is required.',
            ]);

            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            $transactions = DB::table('payment_logs')
                ->select('*')
                ->where('user_id', $userId)
                ->orderBy('id','desc')
                ->paginate($perPage, ['*'], 'page', $page);     
            
            $transactions->getCollection()->transform(function ($transaction) {
                $transaction->amount = (string) $transaction->amount;
                return $transaction;
            });
            
            if($transactions->total() > 0){
                $status = "success";
                $message = "Data found";
            } else {
                $status = "error";
                $message = "No data found";
            }
    
            $responseData = [
                'status' => $status,
                'message' => $message,
                'code' => 200,
                'data' => [
                    'data' => $transactions->items(),
                    'paginator' => [
                        'totalDocs' => $transactions->total(),
                        'limit' => $transactions->perPage(),
                        'page' => $transactions->currentPage(),
                        'totalPages' => $transactions->lastPage(),
                        'hasPrevPage' => $transactions->previousPageUrl() ? true : false,
                        'hasNextPage' => $transactions->nextPageUrl() ? true : false,
                    ],
                ]
            ];

            return response()->json($responseData);
    
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
}



