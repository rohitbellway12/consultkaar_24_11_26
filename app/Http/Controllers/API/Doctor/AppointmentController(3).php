<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use DB;
use PDF;
use App\Models\Appointment;
use App\Models\Payment;
use Stripe\Charge;
use Stripe\Stripe;
use PHPUnit\Exception;
use Illuminate\Support\Facades\Redirect;
use App\CentralLogics\Helpers;
use Stripe\Account;
use Stripe\AccountToken;
use Stripe\Token;
use Stripe\Transfer;
use Carbon\Carbon;


class AppointmentController extends Controller
{



    // public function getAppointments(Request $request){
    //     $token = JWTAuth::getToken();
    //     $user = JWTAuth::toUser($token);
    //     //   dd ($request->all());
    //     $customerId = (isset($user) && !empty($user)) ? $user->id : '';

    //     $page = $request->get('page');
    //     $status = $request->get('status');
    //     $appoinmentType = $request->get('appoinmtent_type');
    //     $date = $request->get('date');
    //     $orderBy = 'desc';
    //     $orderColumn = 'created_at';
    //     $perPage = 10;

    //     // Define the validation rules
    //     $validationRules = [
    //         'page' => 'required',
    //     ];

    //     // Validate the input data
    //     $validation = Validator::make($request->all(), $validationRules, [
    //         'page.required' => 'page is required.',
    //     ]);

    //     // Check for validation errors and return error response if any
    //     if ($validation->fails()) {
    //         return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
    //     }       
    //     //  dd($status, $appoinmentType);
    //         if($status == 'confirmed' && $appoinmentType == 'offline'){
    //             $bookingData = DB::table('appointments')->select('*')
    //             ->where(array('doctor_id' => $customerId,'status' => $status,'appoinmtent_type' => 'offline'))->whereDate("appoinmtent_date",$date)
    //             ->orderBy($orderColumn, $orderBy)
    //             ->paginate($perPage, ['*'], 'page', $page);
    //         } else {
    //             // dd($customerId);
    //             $bookingData = DB::table('appointments')->select('*')
    //             ->where(array('doctor_id' => $customerId))
    //             ->where('status', $status)
    //         //   ->where('appoinmtent_type', $appoinmentType)
    //         //   ->orwhereDate("appoinmtent_date",$date)
    //             ->orderBy($orderColumn, $orderBy)
    //             ->paginate($perPage, ['*'], 'page', $page);
    //         }


    //         $bookingData->getCollection()->transform(function ($item)  {

    //             $doctorId = $item->doctor_id;
    //             $doctorData = User::find($doctorId);
    //             $item->doctor_data = $doctorData;
    //             $userId = $item->user_id;
    //             $userData = User::find($userId);
    //             $item->user_data = $userData;
    //             $item->user_data->name = $item->patent_name;
    //             $item->user_data->mobile_number = $item->patient_mobile_number;
    //             if($item->transaction_id != ''){
    //                 $item->payment_status = 'paid';   
    //             } else {
    //                 $item->payment_status = 'unpaid';   
    //             }

    //              $item->user_data = User::find($item->user_id);
    //             $userImage = $item->user_data->image; 
    //             if(!empty($userImage)){
    //                 $item->user_data->image =  (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);  
    //             } else {
    //                   $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
    //                   $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");

    //                   $item->user_data->image = $imagePath;
    //             } 


    //             return $item; // Return the modified item
    //         });


    //     if($bookingData->total() > 0){
    //         $status = "success";
    //         $message = "Data found";
    //     } else {
    //         $status = "error";
    //         $message = "No data found";
    //     }


    // //   dd($bookingData);


    //     $responseData = [
    //         'status' => $status,
    //         'message' => $message,
    //         'code' => 200,
    //         'data' => [
    //             'data' => $bookingData->items(),
    //             'paginator' => [
    //                 'totalDocs' => $bookingData->total(),
    //                 'limit' => $bookingData->perPage(),
    //                 'page' => $bookingData->currentPage(),
    //                 'totalPages' => $bookingData->lastPage(),
    //                 'hasPrevPage' => $bookingData->previousPageUrl() ? true : false,
    //                 'hasNextPage' => $bookingData->nextPageUrl() ? true : false,
    //             ],
    //         ]
    //     ];

    //     return response()->json($responseData);
    // }



    public function getAppointments(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $doctorId = $user->id;

        $page = $request->get('page', 1);
        $status = $request->get('status');
        $appointmentType = $request->get('appoinmtent_type');
        $date = $request->get('date');
        $perPage = 10;

        $validation = Validator::make($request->all(), [
            'page' => 'required|integer|min:1',
        ], [
            'page.required' => 'Page is required.'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => $validation->errors()->first()
            ]);
        }


        $query = DB::table('appointments')
            ->leftJoin('call_requests', 'appointments.call_request_id', '=', 'call_requests.id')
            ->select(
                'appointments.*',
                'call_requests.call_status as call_status'
            )
            ->where('appointments.doctor_id', $doctorId);

        if ($status) {
            $query->where('appointments.status', $status);
        }
        if ($appointmentType) {
            $query->where('appointments.appoinmtent_type', $appointmentType);
        }
        if ($date) {
            $query->whereDate('appointments.appoinmtent_date', $date);
        }

        $bookingData = $query->orderBy('appointments.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);


        $bookingData->getCollection()->transform(function ($item) {
            $patient = User::find($item->user_id);

            $item->user_data = $patient ?? new \stdClass();
            $item->user_data->name = $item->patient_name ?? ($patient ? $patient->name : 'N/A');
            $item->user_data->mobile_number = $item->patient_mobile_number ?? ($patient ? $patient->mobile_number : 'N/A');

            // Image
            if ($patient && !empty($patient->image)) {
                $item->user_data->image = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $patient->image)
                    : asset('storage/app/public/users/' . $patient->image);
            } else {
                $item->user_data->image = asset('public/assets/admin/img/160x160/img1.jpg');
            }

            $item->payment_status_display = $item->payment_status ?? 'unpaid';


            return $item;
        });

        // Get separate call requests data
        $callRequestsQuery = DB::table('call_requests')
            ->where('doctor_id', $doctorId);

        // Apply same filters to call requests (map appointment status to call status)
        if ($status) {
            if ($status === 'confirmed') {
                $callRequestsQuery->where('call_status', 'complete');
            } elseif ($status === 'pending') {
                $callRequestsQuery->where('call_status', 'progress');
            } elseif ($status === 'cancelled') {
                $callRequestsQuery->where('call_status', 'cancel');
            } else {
                $callRequestsQuery->where('call_status', $status);
            }
        }
        if ($date) {
            $callRequestsQuery->whereDate('created_at', $date);
        }

        $callRequests = $callRequestsQuery->orderBy('created_at', 'desc')->get();

        // Transform call requests data
        $callRequests->transform(function ($call) {
            $patient = User::find($call->user_id);
            
            $call->user_data = $patient ?? new \stdClass();
            $call->user_data->name = $patient ? $patient->name : 'N/A';
            $call->user_data->mobile_number = $patient ? $patient->mobile_number : 'N/A';
            
            // Image
            if ($patient && !empty($patient->image)) {
                $call->user_data->image = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $patient->image)
                    : asset('storage/app/public/users/' . $patient->image);
            } else {
                $call->user_data->image = asset('public/assets/admin/img/160x160/img1.jpg');
            }
            
            return $call;
        });

        $hasData = $bookingData->total() > 0 || $callRequests->count() > 0;
        return response()->json([
            'status' => $hasData ? 'success' : 'error',
            'message' => $hasData ? 'Data found' : 'No data found',
            'code' => 200,
            'data' => [
                'appointments' => $callRequests,
                // 'call_requests' => $callRequests,
                // 'appointments' => $bookingData->items(),
                // 'call_requests' => $callRequests,
                'paginator' => [
                    'totalDocs' => $bookingData->total(),
                    'limit' => $bookingData->perPage(),
                    'page' => $bookingData->currentPage(),
                    'totalPages' => $bookingData->lastPage(),
                    'hasPrevPage' => $bookingData->previousPageUrl() !== null,
                    'hasNextPage' => $bookingData->nextPageUrl() !== null,
                ],
            ]
        ]);
    }




    public function getAppointmentDetail(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $doctorId = $user ? $user->id : null;

            $validator = Validator::make($request->all(), [
                'appoinment_id' => 'required|exists:appointments,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                    'code' => 422
                ]);
            }

            $appointmentId = $request->get('appoinment_id');

            $appointment = DB::table('appointments')
                ->leftJoin('call_requests', 'appointments.call_request_id', '=', 'call_requests.id')
                ->select(
                    'appointments.*',
                    'appointments.call_request_id as request_id',
                    'call_requests.call_status as call_status'
                )
                ->where('appointments.id', $appointmentId)
                ->where('appointments.doctor_id', $doctorId)
                ->first();

            if (!$appointment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Appointment not found or access denied',
                    'code' => 404,
                    'data' => null
                ]);
            }

            $patient = User::find($appointment->user_id);

            $appointment->user_data = $patient ?? new \stdClass();
            $appointment->user_data->name = $appointment->patient_name ?? ($patient ? $patient->name : 'N/A');
            $appointment->user_data->mobile_number = $appointment->patient_mobile_number ?? ($patient ? $patient->mobile_number : 'N/A');

            if ($patient && !empty($patient->image)) {
                $appointment->user_data->image = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $patient->image)
                    : asset('storage/app/public/users/' . $patient->image);
            } else {
                $appointment->user_data->image = asset('public/assets/admin/img/160x160/img1.jpg');
            }

            $appointment->payment_status_display = $appointment->payment_status ?? 'unpaid';

            return response()->json([
                'status' => 'success',
                'message' => 'Data found successfully',
                'code' => 200,
                'data' => $appointment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error: ' . $e->getMessage(),
                'code' => 500
            ]);
        }
    }



    // public function getAppointmentDetail(Request $request){
    //     $token = JWTAuth::getToken();
    //     $user = JWTAuth::toUser($token);
    //     $customerId = (isset($user) && !empty($user)) ? $user->id : '';


    //     $appoinmentId = $request->get('appoinment_id');

    //     $bookingData = DB::table('appointments')->select('*')->where(array('user_id' => $customerId , 'id' => $appoinmentId))->get();

    //     // Check if the order exists
    //     if (count($bookingData) == 0) {
    //         return response()->json(['status' => 'error', 'message' => 'Appointment not found', 'code' => 404,'data' => null]);
    //     }







    //     $bookingData->transform(function ($item) {

    //         $doctorId = $item->doctor_id;
    //         $doctorData = User::find($doctorId);
    //         $item->doctor_data = $doctorData;
    //         $userId = $item->user_id;
    //         $userData = User::find($userId);
    //         $item->user_data = $userData;
    //         $item->user_data->name = $item->patent_name;
    //         $item->user_data->mobile_number = $item->patient_mobile_number;
    //         if($item->transaction_id != ''){
    //             $item->payment_status = 'paid';   
    //         } else {
    //             $item->payment_status = 'unpaid';   
    //         }




    //         return $item; // Return the modified item
    //     });

    //     if (count($bookingData) == 0) {
    //         return response()->json(['status' => 'error', 'message' => 'No data found', 'code' => 404,'data' => null]);
    //     }


    //     return response()->json(['status' => 'success', 'message' => 'Data found successfully', 'code' => 200, 'data' => $bookingData[0]]);
    // }





    public function getSlots(Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'Slot feature has been removed.', 'code' => 404]);
    }

    public function deleteSlot(Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'Slot feature has been removed.', 'code' => 404]);
    }



    public function manageAppointmentStatus(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $appointmentId = $request->appointment_id;
        $status = $request->status;

        $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

        if (isset($appointmentData) && !empty($appointmentData)) {
            DB::table('appointments')->where('id', $appointmentId)->update(['status' => $status]);
            if ($status == "confirmed") {
                $message = "Appointment is already confirmed";

            } else {
                $message = "Appointment is already rejected";
            }

            if ($appointmentData->status == $status) {

                return response()->json(['status' => 'error', 'code' => 200, 'message' => $message, 'data' => $appointmentData]);
            }

            if ($status == "confirmed") {
                $message = "Appointment confirmed successfully";

            } else {
                $message = "Appointment rejected successfully";
            }
            // send notification to patient
            $patientData = User::find($appointmentData->user_id);
            if ($patientData && !empty($patientData->fcm_token)) {
                $data = [
                    'title' => 'Appointment Update',
                    'description' => $message,
                    'image' => '',
                    'type' => 'appointment',
                    'appointment_id' => $appointmentId
                ];
                try {
                    Helpers::send_push_notif_to_device($patientData->fcm_token, $data, $user->id, $appointmentData->user_id);
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                }
            }

            $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

            return response()->json(['status' => 'success', 'code' => 200, 'message' => $message, 'data' => $appointmentData]);
        } else {
            return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Invalid appointment id', 'data' => null]);
        }
    }


    // public function getTotalEarnings(Request $request){
    //     $token = JWTAuth::getToken();
    //     $user = JWTAuth::toUser($token);
    //     $doctorId = (isset($user) && !empty($user)) ? $user->id : '';   

    //     $appointmentData = DB::table('appointments')->select('*')->where(array('doctor_id' => $doctorId))->get();


    //     $totalEarnings = 0;

    //     if(isset($appointmentData) && !empty($appointmentData)){            
    //         foreach($appointmentData as $key => $value){
    //             $totalEarnings = $totalEarnings +$value->consultation_fee;
    //         }
    //     }

    //     $data = array('total_earnings' => $totalEarnings);

    //     $responseData = ['status' => 'success','message' => "Data found",'code' => 200,'data' => $data];

    //     return response()->json($responseData);
    // }
    // public function getTotalEarnings(Request $request)
    // {
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate();

    //         if ($user->role_id != 3) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'code' => 403,
    //                 'message' => 'Only doctors can view earnings'
    //             ]);
    //         }

    //         $query = DB::table('appointments')
    //             ->where('doctor_id', $user->id);

    //         // ✅ APPLY FILTERS (Mutual Exclusive with Custom Range Priority)
    //         if ($request->filled('from_date') && $request->filled('to_date')) {
    //             $query->whereBetween('appoinmtent_date', [$request->from_date, $request->to_date]);
    //         } elseif ($request->type === 'today') {
    //             $query->where('appoinmtent_date', Carbon::today()->toDateString());
    //         } elseif ($request->type === 'yesterday') {
    //             $query->where('appoinmtent_date', Carbon::yesterday()->toDateString());
    //         } elseif ($request->type === 'last_7_days') {
    //             $query->whereBetween('appoinmtent_date', [
    //                 Carbon::now()->subDays(6)->toDateString(),
    //                 Carbon::today()->toDateString()
    //             ]);
    //         } elseif ($request->type === 'last_30_days') {
    //             $query->whereBetween('appoinmtent_date', [
    //                 Carbon::now()->subDays(29)->toDateString(),
    //                 Carbon::today()->toDateString()
    //             ]);
    //         }

    //         // ✅ TOTAL EARNINGS
    //         $totalEarnings = $query->sum('consultation_fee');

    //         return response()->json([
    //             'status' => 'success',
    //             'code' => 200,
    //             'message' => 'Earnings fetched successfully',
    //             'data' => [
    //                 'filter_type' => $request->type,
    //                 'total_earnings' => $totalEarnings
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'code' => 500,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
public function getTotalEarnings(Request $request)
{
    try {
        // 🔹 Authenticated doctor
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role_id != 3) { // 3 = Doctor
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Only doctors can view earnings'
            ]);
        }

        // 🔹 Base query (call_requests)
        $query = DB::table('call_requests')
            ->where('doctor_id', $user->id)
            ->where('call_status', 'complete'); // only completed calls count

        // 🔹 Apply date filters (mutually exclusive)
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('ended_at', [$request->from_date, $request->to_date]);
        } elseif ($request->type === 'today') {
            $query->whereDate('ended_at', Carbon::today()->toDateString());
        } elseif ($request->type === 'yesterday') {
            $query->whereDate('ended_at', Carbon::yesterday()->toDateString());
        } elseif ($request->type === 'last_7_days') {
            $query->whereBetween('ended_at', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::today()->toDateString()
            ]);
        } elseif ($request->type === 'last_30_days') {
            $query->whereBetween('ended_at', [
                Carbon::now()->subDays(29)->toDateString(),
                Carbon::today()->toDateString()
            ]);
        }

        // 🔹 Total earnings
        $totalEarnings = $query->sum('deduction');

        // 🔹 Get appointments count with same filters
        $appointmentsQuery = DB::table('appointments')
            ->where('doctor_id', $user->id);

        // Apply same date filters to appointments
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $appointmentsQuery->whereBetween('appoinmtent_date', [$request->from_date, $request->to_date]);
        } elseif ($request->type === 'today') {
            $appointmentsQuery->whereDate('appoinmtent_date', Carbon::today()->toDateString());
        } elseif ($request->type === 'yesterday') {
            $appointmentsQuery->whereDate('appoinmtent_date', Carbon::yesterday()->toDateString());
        } elseif ($request->type === 'last_7_days') {
            $appointmentsQuery->whereBetween('appoinmtent_date', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::today()->toDateString()
            ]);
        } elseif ($request->type === 'last_30_days') {
            $appointmentsQuery->whereBetween('appoinmtent_date', [
                Carbon::now()->subDays(29)->toDateString(),
                Carbon::today()->toDateString()
            ]);
        }

        $totalAppointments = $appointmentsQuery->count();

        // 🔹 Get completed call requests count with same filters
        $callRequestsQuery = DB::table('call_requests')
            ->where('doctor_id', $user->id)
            ->where('call_status', 'complete');

        // Apply same date filters to call requests
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $callRequestsQuery->whereBetween('ended_at', [$request->from_date, $request->to_date]);
        } elseif ($request->type === 'today') {
            $callRequestsQuery->whereDate('ended_at', Carbon::today()->toDateString());
        } elseif ($request->type === 'yesterday') {
            $callRequestsQuery->whereDate('ended_at', Carbon::yesterday()->toDateString());
        } elseif ($request->type === 'last_7_days') {
            $callRequestsQuery->whereBetween('ended_at', [
                Carbon::now()->subDays(6)->toDateString(),
                Carbon::today()->toDateString()
            ]);
        } elseif ($request->type === 'last_30_days') {
            $callRequestsQuery->whereBetween('ended_at', [
                Carbon::now()->subDays(29)->toDateString(),
                Carbon::today()->toDateString()
            ]);
        }

        $totalCompletedCalls = $callRequestsQuery->count();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Earnings, appointments and calls fetched successfully',
            'data' => [
                'filter_type' => $request->type ?? 'custom',
                'total_earnings' => (float)$totalEarnings,
                'total_appointments' => $totalAppointments,
                'total_completed_calls' => $totalCompletedCalls
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'code' => 500,
            'message' => $e->getMessage()
        ]);
    }
}

    // public function getTotalAppointments(Request $request){
    //     $token = JWTAuth::getToken();
    //     $user = JWTAuth::toUser($token);
    //     $doctorId = (isset($user) && !empty($user)) ? $user->id : '';   

    //     $appointmentData = DB::table('appointments')->select('*')->where(array('doctor_id' => $doctorId))->get();


    //     $data = array('total_appointments' => count($appointmentData));




    //     $responseData = ['status' => 'success','message' => "Data found",'code' => 200,'data' => $data];

    //     return response()->json($responseData);
    // }

    public function getTotalAppointments(Request $request)
    {
        try {
            // ✅ Logged-in user
            $user = JWTAuth::parseToken()->authenticate();

            // ✅ Only doctor allowed
            if ($user->role_id != 3) {
                return response()->json([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Only doctors can view appointments'
                ]);
            }

            /*
            Request params (optional):
            type = today | last_7_days | last_30_days
            from_date = YYYY-MM-DD
            to_date   = YYYY-MM-DD
            */

            $query = DB::table('appointments')
                ->where('doctor_id', $user->id);

            // ✅ Quick filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereBetween('appoinmtent_date', [$request->from_date, $request->to_date]);
            } elseif ($request->type === 'today') {
                $query->where('appoinmtent_date', Carbon::today()->toDateString());
            } elseif ($request->type === 'yesterday') {
                $query->where('appoinmtent_date', Carbon::yesterday()->toDateString());
            } elseif ($request->type === 'last_7_days') {
                $query->whereBetween('appoinmtent_date', [
                    Carbon::now()->subDays(6)->toDateString(),
                    Carbon::today()->toDateString()
                ]);
            } elseif ($request->type === 'last_30_days') {
                $query->whereBetween('appoinmtent_date', [
                    Carbon::now()->subDays(29)->toDateString(),
                    Carbon::today()->toDateString()
                ]);
            }

            // Count appointments (optimized)
            $totalAppointments = $query->count();

            // Get completed call requests count with same filters
            $callRequestsQuery = DB::table('call_requests')
                ->where('doctor_id', $user->id)
                ->where('call_status', 'complete');

            // Apply same date filters to call requests
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $callRequestsQuery->whereBetween('ended_at', [$request->from_date, $request->to_date]);
            } elseif ($request->type === 'today') {
                $callRequestsQuery->whereDate('ended_at', Carbon::today()->toDateString());
            } elseif ($request->type === 'yesterday') {
                $callRequestsQuery->whereDate('ended_at', Carbon::yesterday()->toDateString());
            } elseif ($request->type === 'last_7_days') {
                $callRequestsQuery->whereBetween('ended_at', [
                    Carbon::now()->subDays(6)->toDateString(),
                    Carbon::today()->toDateString()
                ]);
            } elseif ($request->type === 'last_30_days') {
                $callRequestsQuery->whereBetween('ended_at', [
                    Carbon::now()->subDays(29)->toDateString(),
                    Carbon::today()->toDateString()
                ]);
            }

            $totalCompletedCalls = $callRequestsQuery->count();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Appointments and calls fetched successfully',
                'data' => [
                    'filter_type' => $request->type,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'total_appointments' => $totalCompletedCalls,
                    
                    // 'total_appointments' => $totalAppointments,
                    // 'total_completed_calls' => $totalCompletedCalls
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }


    // public function getTotalOrdersAndEarnings(Request $request){
    //     $token = JWTAuth::getToken();
    //     $user = JWTAuth::toUser($token);
    //     $user = JWTAuth::toUser($token);
    //     $doctorId = (isset($user) && !empty($user)) ? $user->id : '';   

    //     $appointmentData = DB::table('appointments')->select('*')->where(array('doctor_id' => $doctorId))->get();
    //     $earningData    = DB::table('wallet_transactions')->select('*')->where(array('user_id' => $doctorId,'is_credit' => '1'))->get();


    //     $totalEarnings = 0;

    //     if(isset($earningData) && !empty($earningData)){            
    //         foreach($earningData as $key => $value){
    //             $totalEarnings = $totalEarnings +$value->amount;
    //         }
    //     }



    //     $data = array('total_earnings' => $totalEarnings,'total_orders' => count($appointmentData));




    //     $responseData = ['status' => 'success','message' => "Data found",'code' => 200,'data' => $data];

    //     return response()->json($responseData);
    // }



    public function getTotalOrdersAndEarnings(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $doctorId = $user?->id;

        if (!$doctorId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'code' => 401
            ]);
        }

        // Use IST timezone (adjust if your DB uses UTC)
        $now = \Carbon\Carbon::now('Asia/Kolkata');
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        // === EARNINGS (from wallet_transactions) ===
        $totalEarnings = DB::table('wallet_transactions')
            ->where('user_id', $doctorId)
            ->where('is_credit', 1)
            ->sum('amount');

        $todayEarnings = DB::table('wallet_transactions')
            ->where('user_id', $doctorId)
            ->where('is_credit', 1)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('amount');

        $monthEarnings = DB::table('wallet_transactions')
            ->where('user_id', $doctorId)
            ->where('is_credit', 1)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');

        // === APPOINTMENTS (from appointments table) ===
        $totalAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->count();

        $todayAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        $monthAppointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        // === RESPONSE DATA ===
        $data = [
            'total_earnings' => (float) $totalEarnings,
            'today_earnings' => (float) $todayEarnings,
            'month_earnings' => (float) $monthEarnings,

            'total_appointments' => (int) $totalAppointments,
            'today_appointments' => (int) $todayAppointments,
            'month_appointments' => (int) $monthAppointments,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard data retrieved successfully',
            'code' => 200,
            'data' => $data
        ]);
    }



    public function getEarningReportGraph(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $doctorId = (isset($user) && !empty($user)) ? $user->id : '';

        $year = date('Y');
        $month = date('m');

        // Use strtotime to find the last day of the current month
        $lastDayTimestamp = strtotime("last day of $year-$month");

        // Format the timestamp to get the last day of the current month
        $lastDay = date('d', $lastDayTimestamp);




        $earningData = [];
        $totalEarningsCurrentMonth = 0;

        for ($i = 1; $i <= $lastDay; $i++) {
            $dayName = ($i < 10) ? "0" . $i : $i;
            $date = date("Y-m-$dayName");
            $earningSum = DB::table('wallet_transactions')
                ->where([
                    ['user_id', '=', $doctorId],
                    ['is_credit', '=', '1']
                ])
                ->whereDate('created_at', $date)
                ->sum('amount');

            $totalEarningsCurrentMonth += $earningSum;

            array_push($earningData, array("date" => $date, 'earning' => (string) $earningSum));

        }



        if (count($earningData) > 0) {
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
            'total_earnings' => (string) $totalEarningsCurrentMonth, // Include total earnings in the response
            'data' => $earningData
        ];

        return response()->json($responseData);
    }


    // public function getEarningReport(Request $request){
    //     try {
    //         // Get requested data
    //         $page = $request->get('page');
    //         $orderBy =  'desc';
    //         $orderColumn = 'created_at';
    //         $perPage = 10; 
    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = (isset($user) && !empty($user)) ? $user->id : '';
    //         $transactionMode = $request->transaction_mode;


    //         // Define the validation rules
    //         $validationRules = [
    //             'page' => 'required',
    //         ];

    //         // Validate the input data
    //         $validation = Validator::make($request->all(), $validationRules, [
    //             'page.required' => 'Page is required.',
    //         ]);

    //         // Check for validation errors and return error response if any
    //         if ($validation->fails()) {
    //             return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
    //         }

    //         // Query to retrieve items
    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = $user ? $user->id : null;


    //          $transactions = DB::table('wallet_transactions')->select('*')->where(array('user_id'=>$userId ))->orderBy('id','desc')->paginate($perPage, ['*'], 'page', $page);     



    //         $transactions->getCollection()->transform(function ($transaction)  {

    //             $transaction->amount = (string) $transaction->amount;
    //                 $appointmentId = $transaction->order_id;
    //                 $appoimtmentData = Appointment::find($appointmentId);
    //                 $transaction->patient_data = User::find($appoimtmentData->user_id);
    //                 $userImage = $transaction->patient_data->image; 
    //                 $transaction->patient_data->name = $appoimtmentData->patent_name;
    //                 $transaction->patient_data->mobile_number = $appoimtmentData->patient_mobile_number;
    //                 if(!empty($userImage)){
    //                     $transaction->patient_data->image =  (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);  
    //                 } else {
    //                       $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
    //                       $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");

    //                       $transaction->patient_data->image = $imagePath;
    //                 } 
    //             return $transaction;

    //         });


    //         if($transactions->total() > 0){
    //             $status = "success";
    //             $message = "Data found";
    //         } else {
    //             $status = "error";
    //             $message = "No data found";
    //         }

    //         $responseData = [
    //             'status' => $status,
    //             'message' => $message,
    //             'code' => 200,
    //             'data' => [
    //                 'data' => $transactions->items(),
    //                 'paginator' => [
    //                     'totalDocs' => $transactions->total(),
    //                     'limit' => $transactions->perPage(),
    //                     'page' => $transactions->currentPage(),
    //                     'totalPages' => $transactions->lastPage(),
    //                     'hasPrevPage' => $transactions->previousPageUrl() ? true : false,
    //                     'hasNextPage' => $transactions->nextPageUrl() ? true : false,
    //                 ],
    //             ]
    //         ];

    //         return response()->json($responseData);

    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    //     }
    // }

    public function getEarningReport(Request $request)
    {
        try {
            // Get requested data
            $page = $request->get('page');
            $orderBy = 'desc';
            $orderColumn = 'created_at';
            $perPage = 10;
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';
            $transactionMode = $request->transaction_mode;


            // Define the validation rules
            $validationRules = [
                'page' => 'required',
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'page.required' => 'Page is required.',
            ]);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            // Query to retrieve items
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user ? $user->id : null;


            $query = DB::table('wallet_transactions')->select('*')->where('user_id', $userId);

            if ($transactionMode == 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($transactionMode == 'yesterday') {
                $query->whereDate('created_at', Carbon::yesterday());
            } elseif ($transactionMode == 'this_week') {
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($transactionMode == 'this_month') {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
            } elseif ($transactionMode == 'this_year') {
                $query->whereYear('created_at', Carbon::now()->year);
            }

            $transactions = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);



            $transactions->getCollection()->transform(function ($transaction) {

                $transaction->amount = (string) $transaction->amount;
                $appointmentId = $transaction->order_id;
                $appoimtmentData = Appointment::find($appointmentId);
                $transaction->patient_data = User::find($appoimtmentData->user_id);
                $userImage = $transaction->patient_data->image;
                $transaction->patient_data->name = $appoimtmentData->patent_name;
                $transaction->patient_data->mobile_number = $appoimtmentData->patient_mobile_number;
                if (!empty($userImage)) {
                    $transaction->patient_data->image = (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);
                } else {
                    $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                    $imagePath = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");

                    $transaction->patient_data->image = $imagePath;
                }
                return $transaction;

            });


            if ($transactions->total() > 0) {
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


    /**
     * add meidicine
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function addMedicines(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';

            // Get requested data      

            $medicines = $request->input('medicines');
            $appointmentId = $request->input('appointment_id');
            $note = $request->input('note');
            $patientId = $request->input('patient_id');





            // Define the validation rules
            $validationRules = [
                'appointment_id' => 'required',
                'medicines' => 'required',
                'patient_id' => 'required',
                'note' => 'required',
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }


            $requestData = [

                'appointment_id' => $appointmentId,
                'medicines' => json_encode($medicines, true),
                'patient_id' => $patientId,
                'note' => $note,

            ];

            DB::table('users_medicine')->insertgetId($requestData);







            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Meidicine added successfully', 'data' => $request->all()]);
        } catch (\Exception $e) {
            // Handle exceptions, if any

            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }



    // public function getWalletTransactions(Request $request){
    //     try {
    //         // Get requested data
    //         $page = $request->get('page');
    //         $orderBy =  'desc';
    //         $orderColumn = 'created_at';
    //         $perPage = 10; 
    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = (isset($user) && !empty($user)) ? $user->id : '';


    //         // Define the validation rules
    //         $validationRules = [
    //             'page' => 'required',
    //         ];

    //         // Validate the input data
    //         $validation = Validator::make($request->all(), $validationRules, [
    //             'page.required' => 'Page is required.',
    //         ]);

    //         // Check for validation errors and return error response if any
    //         if ($validation->fails()) {
    //             return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
    //         }

    //         // Query to retrieve items
    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = $user ? $user->id : null;


    //         $transactions = DB::table('wallet_transactions')->select('*')->where('user_id', $userId)->orderBy('id','desc')->paginate($perPage, ['*'], 'page', $page);    


    //         $transactions->getCollection()->transform(function ($transaction)  {
    //             $transaction->amount = (string) $transaction->amount;
    //             $appointmentId = $transaction->order_id;
    //             $appoimtmentData = Appointment::find($appointmentId);

    //             if($appoimtmentData) {
    //                 $transaction->total_amount = (string) $appoimtmentData->consultation_fee; 
    //                 // Admin commission is Total - Doctor's Share (transaction amount)
    //                 $admin_commission = $appoimtmentData->consultation_fee - $transaction->amount;
    //                 $transaction->admin_commission = (string) $admin_commission;

    //                 // Add commission percentage
    //                 if ($appoimtmentData->consultation_fee > 0) {
    //                     $transaction->commission_percentage = (string) round(($admin_commission / $appoimtmentData->consultation_fee) * 100, 2);
    //                 } else {
    //                     $transaction->commission_percentage = "0";
    //                 }

    //                 $transaction->patient_data = User::find($appoimtmentData->user_id);
    //             } else {
    //                 $transaction->total_amount = "0";
    //                 $transaction->admin_commission = "0";
    //                 $transaction->commission_percentage = "0";
    //                 $transaction->patient_data = new \stdClass(); // Empty object to avoid null errors
    //             }

    //             $userImage = isset($transaction->patient_data->image) ? $transaction->patient_data->image : null;

    //             if(!empty($userImage)){
    //                 $transaction->patient_data->image =  (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);  
    //             } else {
    //                   $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
    //                   $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");

    //                   if(isset($transaction->patient_data)) {
    //                       $transaction->patient_data->image = $imagePath;
    //                   }
    //             } 
    //             return $transaction;

    //         });



    //         if($transactions->total() > 0){
    //             $status = "success";
    //             $message = "Data found";
    //         } else {
    //             $status = "error";
    //             $message = "No data found";
    //         }


    //         $doctorData = User::find($userId);
    //         $walletBalance = $doctorData->wallet_amount ?? "0";

    //         // Calculate total business stats for the doctor
    //         $stats = DB::table('wallet_transactions')
    //             ->join('appointments', 'wallet_transactions.order_id', '=', 'appointments.id')
    //             ->where('wallet_transactions.user_id', $userId)
    //             ->select(
    //                 DB::raw('SUM(appointments.consultation_fee) as total_business'),
    //                 DB::raw('SUM(wallet_transactions.amount) as doctor_earnings')
    //             )
    //             ->first();

    //         $totalAmountPaid = $stats->total_business ?? 0;
    //         $doctorEarnings = $stats->doctor_earnings ?? 0;
    //         $totalAdminCommission = $totalAmountPaid - $doctorEarnings;

    //         $responseData = [
    //             'status' => $status,
    //             'message' => $message,
    //             'code' => 200,
    //             'data' => [
    //                 'wallet_balance' => $walletBalance,
    //                 'total_amount_paid' => (string)$totalAmountPaid,
    //                 'total_admin_commission' => (string)$totalAdminCommission,
    //                 'data' => $transactions->items(),
    //                 'paginator' => [
    //                     'totalDocs' => $transactions->total(),
    //                     'limit' => $transactions->perPage(),
    //                     'page' => $transactions->currentPage(),
    //                     'totalPages' => $transactions->lastPage(),
    //                     'hasPrevPage' => $transactions->previousPageUrl() ? true : false,
    //                     'hasNextPage' => $transactions->nextPageUrl() ? true : false,
    //                 ],
    //             ]
    //         ];

    //         return response()->json($responseData);

    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    //     }
    // }




public function getWalletTransactions(Request $request)
{
    try {

        /* =======================
         * AUTH USER (JWT SAFE)
         * ======================= */
        $token = JWTAuth::getToken();
        $user  = $token ? JWTAuth::toUser($token) : null;

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Unauthorized user'
            ]);
        }

        $userId = $user->id;

        /* =======================
         * VALIDATION
         * ======================= */
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer|min:1',
        ], [
            'page.required' => 'Page is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        $page    = $request->page;
        $perPage = 10;

        /* =======================
         * MAIN QUERY (CALL TRANSACTIONS ONLY)
         * ======================= */
        $transactionsQuery = DB::table('wallet_transactions as wt')
            ->leftJoin('call_requests as call', 'wt.order_id', '=', 'call.id')
            ->leftJoin('users as patients', 'patients.id', '=', 'call.user_id')
            ->where('wt.user_id', $userId)
          ->whereIn('wt.transaction_for', ['call', 'chat'])// sirf call transactions
            ->select(
                'wt.*',
                'patients.id as patient_user_id',
                'patients.name as patient_name',
                'patients.image as patient_image'
            );

        /* =======================
         * FILTERS
         * ======================= */
        if ($request->filled('month')) {
            $transactionsQuery->whereMonth('wt.created_at', $request->month);
        }

        if ($request->filled('year')) {
            $transactionsQuery->whereYear('wt.created_at', $request->year);
        }

        if ($request->filled('amount')) {
            $transactionsQuery->where('wt.amount', $request->amount);
        }

        if ($request->filled('user_name')) {
            $transactionsQuery->where('patients.name', 'like', '%' . $request->user_name . '%');
        }

        /* =======================
         * PAGINATION
         * ======================= */
        $transactions = $transactionsQuery
            ->orderBy('wt.id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        /* =======================
         * TRANSFORM DATA
         * ======================= */
        $transactions->getCollection()->transform(function ($transaction) {

            $transaction->amount = (string) $transaction->amount;

            if (!empty($transaction->patient_image)) {
                $transaction->patient_image = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $transaction->patient_image)
                    : asset('storage/app/public/users/' . $transaction->patient_image);
            } else {
                $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                $transaction->patient_image = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
            }

         
            $transaction->patient_data = [
                'id'    => $transaction->patient_user_id ?? 'N/A',
                'name'  => $transaction->patient_name ?? 'N/A',
                'image' => $transaction->patient_image,
            ];

            // unset extra fields
            unset(
                $transaction->patient_user_id,
                $transaction->patient_name,
                $transaction->patient_image
            );

            return $transaction;
        });

        /* =======================
         * DOCTOR DATA
         * ======================= */
        $doctorData = User::find($userId);
        $walletBalance        = $doctorData->wallet_amount ?? "0";
        $commissionPercentage = $doctorData->commission_percentage ?? "0";

        /* =======================
         * BUSINESS STATS (CALL ONLY)
         * ======================= */
      $stats = DB::table('wallet_transactions as wt')
    ->leftJoin('call_requests as call', 'wt.order_id', '=', 'call.id')
    ->where('wt.user_id', $userId)
    ->where('wt.transaction_for', 'call')
    ->select(
        DB::raw('SUM(call.call_rate * IFNULL(call.total_min,0)) as total_business'),
        DB::raw('SUM(wt.amount) as doctor_earnings')
    )
    ->first();

        $totalAmountPaid      = $stats->total_business ?? 0;
        $doctorEarnings       = $stats->doctor_earnings ?? 0;
        $totalAdminCommission = $totalAmountPaid - $doctorEarnings;

        /* =======================
         * RESPONSE
         * ======================= */
        return response()->json([
            'status'  => $transactions->total() > 0 ? 'success' : 'error',
            'message' => $transactions->total() > 0 ? 'Data found' : 'No data found',
            'code'    => 200,
            'data'    => [
                'wallet_balance'         => (string) $walletBalance,
                'total_amount_paid'      => (string) $totalAmountPaid,
                'total_admin_commission' => (string) $totalAdminCommission,
                'commission_percentage'  => (string) $commissionPercentage,
                'data'                   => $transactions->items(),
                'paginator'              => [
                    'totalDocs'   => $transactions->total(),
                    'limit'       => $transactions->perPage(),
                    'page'        => $transactions->currentPage(),
                    'totalPages'  => $transactions->lastPage(),
                    'hasPrevPage' => $transactions->previousPageUrl() ? true : false,
                    'hasNextPage' => $transactions->nextPageUrl() ? true : false,
                ],
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'code'    => 500,
            'message' => $e->getMessage()
        ]);
    }
}
// public function getWalletTransactions(Request $request)
// {
//     try {

//         /* =======================
//          * AUTH
//          * ======================= */
//         $token = JWTAuth::getToken();
//         $user  = $token ? JWTAuth::toUser($token) : null;

//         if (!$user) {
//             return response()->json([
//                 'status' => 'error',
//                 'code'   => 401,
//                 'message'=> 'Unauthorized'
//             ]);
//         }

//         $doctorId = $user->id;

//         /* =======================
//          * PAGINATION
//          * ======================= */
//         $page    = $request->get('page', 1);
//         $perPage = 10;

//         /* ======================================================
//          * 1️⃣ WALLET TRANSACTIONS (PRIMARY SOURCE)
//          * ====================================================== */
//         $walletQuery = DB::table('wallet_transactions as wt')
//             ->leftJoin('call_requests as call', 'wt.order_id', '=', 'call.id')
//             ->leftJoin('users as patient', 'patient.id', '=', 'call.user_id')
//             ->where('wt.user_id', $doctorId)
//             ->whereIn('wt.transaction_for', ['call','chat','video','audio'])
//             ->select(
//                 'wt.id',
//                 'wt.transaction_for',
//                 'wt.amount',
//                 'wt.created_at',

//                 'call.call_type',
//                 'call.total_min',
//                 'call.call_rate',

//                 'patient.name as patient_name',
//                 'patient.email as patient_email',
//                 'patient.gender as patient_gender'
//             );

//         /* ======================================================
//          * 2️⃣ FALLBACK – CALL REQUESTS (NO WALLET ENTRY)
//          * ====================================================== */
//         $callQuery = DB::table('call_requests as call')
//             ->leftJoin('users as patient', 'patient.id', '=', 'call.user_id')
//             ->where('call.doctor_id', $doctorId)
//             ->where('call.call_status', 'complete')
//             ->select(
//                 DB::raw('call.id as id'),
//                 DB::raw('call.call_type as transaction_for'),
//                 DB::raw('(call.call_rate * IFNULL(call.total_min,0)) as amount'),
//                 'call.created_at',

//                 'call.call_type',
//                 'call.total_min',
//                 'call.call_rate',

//                 'patient.name as patient_name',
//                 'patient.email as patient_email',
//                 'patient.gender as patient_gender'
//             );

//         /* ======================================================
//          * 3️⃣ UNION BOTH
//          * ====================================================== */
//         $finalQuery = $walletQuery->unionAll($callQuery);

//         /* ======================================================
//          * 4️⃣ PAGINATE MANUALLY
//          * ====================================================== */
//         $data = DB::query()
//             ->fromSub($finalQuery, 't')
//             ->orderBy('created_at', 'desc')
//             ->paginate($perPage, ['*'], 'page', $page);

//         /* =======================
//          * STATS
//          * ======================= */
//         $stats = DB::table('call_requests')
//             ->where('doctor_id', $doctorId)
//             ->where('call_status','complete')
//             ->select(
//                 DB::raw('SUM(call_rate * IFNULL(total_min,0)) as total_business')
//             )->first();

//         $doctor = User::find($doctorId);

//         /* =======================
//          * RESPONSE
//          * ======================= */
//         return response()->json([
//             'status'  => $data->total() ? 'success' : 'error',
//             'message' => $data->total() ? 'Data found' : 'No data found',
//             'code'    => 200,
//             'data'    => [
//                 'wallet_balance'         => (string) ($doctor->wallet_amount ?? 0),
//                 'total_amount_paid'      => (string) ($stats->total_business ?? 0),
//                 'commission_percentage'  => (string) ($doctor->commission_percentage ?? 0),
//                 'data'                   => $data->items(),
//                 'paginator'              => [
//                     'totalDocs'   => $data->total(),
//                     'limit'       => $data->perPage(),
//                     'page'        => $data->currentPage(),
//                     'totalPages'  => $data->lastPage(),
//                     'hasPrevPage' => $data->previousPageUrl() ? true : false,
//                     'hasNextPage' => $data->nextPageUrl() ? true : false,
//                 ]
//             ]
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'code'   => 500,
//             'message'=> $e->getMessage()
//         ]);
//     }
// }



    public function getPaymentLogs(Request $request)
    {
        try {
            // Get requested data
            $page = $request->get('page');
            $orderBy = 'desc';
            $orderColumn = 'created_at';
            $perPage = 10;
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';


            // Define the validation rules
            $validationRules = [
                'page' => 'required',
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'page.required' => 'Page is required.',
            ]);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            // Query to retrieve items
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user ? $user->id : null;


            $transactions = DB::table('payment_logs')->select('*')->where(array('user_id' => $userId))->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);



            $transactions->getCollection()->transform(function ($transaction) {

                $transaction->amount = (string) $transaction->amount;

                return $transaction;

            });


            if ($transactions->total() > 0) {
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

    public function sendWithDrawalRequest(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user ? $user->id : null;

        $bankData = DB::table("doctor_banks")->where("doctor_id", $userId)->get();
        if (count($bankData) > 0) {

            $doctorData = User::find($userId);
            $doctorEmail = $doctorData->email;
            $doctorWalletAmount = $doctorData->wallet_amount;
            $amount = $request->amount;

            if ($amount > $doctorWalletAmount) {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'You cannot withdraw money more than your wallet balance']);
            }

            // Check if there is a pending withdrawal request
            $pendingRequest = DB::table('withdrawl_requests')
                ->where('request_by', $userId)
                ->where('status', 'pending')
                ->first();

            if ($pendingRequest) {
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'You already have a pending withdrawal request']);
            }

            // Insert new withdrawal request
            DB::table('withdrawl_requests')->insert([
                'request_by' => $userId,
                'amount' => $amount,
                'status' => 'pending',
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Request has been sent successfully']);
        } else {
            return response()->json(['status' => 'error', 'code' => 403, 'message' => 'Please add bank detail to send withdrawal request']);
        }
    }


    public function getPendingWithDrawalRequests(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user ? $user->id : null;
        // Check if there is a pending withdrawal request
        $pendingRequest = DB::table('withdrawl_requests')->where('request_by', $userId)->where('status', 'pending')->first();
        return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found', 'data' => $pendingRequest]);

    }


    public function withDrawWalletAmount(Request $request)
    {

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user ? $user->id : null;

        $bankData = DB::table("doctor_banks")->where("doctor_id", $userId)->get();
        if (count($bankData) > 0) {

            $doctorData = User::find($userId);
            $doctorEmail = $doctorData->email;
            $doctorName = $doctorData->name;
            $doctorWalletAmount = $doctorData->wallet_amount;
            $requestId = $request->request_id;





            $requestData = DB::table('withdrawl_requests')->where(['id' => $requestId, 'request_by' => $userId])->first();
            $amount = $requestData->amount;
            if (!$requestData) {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Invalid request id']);
            }

            if ($requestData->status == 'pending') {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'You can withdraw amount after your request approval by admin']);
            }

            if ($requestData->status == 'reject') {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Your request is rejected by admin']);
            }

            // Retrieve Stripe API key from configuration or environment variables
            $config = Helpers::get_business_settings('stripe');
            //  $config['api_key'] = "";


            try {


                $stripe = new \Stripe\StripeClient($config['api_key']);

                $balance = $stripe->balance->retrieve([]);


                $bankAccountToken = $stripe->tokens->create([
                    'bank_account' => [
                        'country' => 'FR',
                        'currency' => 'eur',
                        'account_holder_name' => 'Jenny Rosen',
                        'account_holder_type' => 'individual',
                        'account_number' => 'FR1420041010050500013M02606',
                    ],
                ]);


                $account = $stripe->accounts->create([
                    'country' => 'FR',
                    'email' => 'jenny.rosen@example.com',
                    'controller' => [
                        'fees' => ['payer' => 'application'],
                        'losses' => ['payments' => 'application'],
                        'stripe_dashboard' => ['type' => 'express'],
                    ],
                ]);

                $extaccount = $stripe->accounts->createExternalAccount(
                    $account->id,
                    ['external_account' => $bankAccountToken->id]
                );



                $stripe->payouts->create([
                    'amount' => 100,
                    'currency' => 'eur',
                ]);


                $payment = Payment::create([
                    'user_id' => $userId,
                    'amount' => $amount,
                ]);

                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Amount habe been withdrawn successfully'], 500);


            } catch (\Exception $e) {
                // Handle exceptions
                return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
            }








        } else {
            return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Please add bank detail to withdraw']);

        }
    }






}