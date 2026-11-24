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
use App\Models\WithDrawRequest;


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

    // ✅ Query with LEFT JOIN to call_requests
    $query = DB::table('appointments')
        ->leftJoin('call_requests', 'appointments.call_request_id', '=', 'call_requests.id')
        ->select(
            'appointments.*',
            'call_requests.call_status as call_status' // fetch call_status directly
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

    // ✅ Transform: enrich with patient data
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

        // Payment status
        $item->payment_status_display = $item->payment_status ?? 'unpaid';

        // ✅ call_request_id already exists in $item (from SELECT *)
        // ✅ call_status already added via join

        return $item;
    });

    $hasData = $bookingData->total() > 0;
    return response()->json([
        'status' => $hasData ? 'success' : 'error',
        'message' => $hasData ? 'Data found' : 'No data found',
        'code' => 200,
        'data' => [
            'data' => $bookingData->items(),
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

        // ✅ Spelling Match: 'appoinment_id' (Double 'n') jaisa aapne validation me rakha tha
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

        // ✅ Join aur Logic wahi jo list function (getAppointments) me hai
        $appointment = DB::table('appointments')
            ->leftJoin('call_requests', 'appointments.call_request_id', '=', 'call_requests.id')
            ->select(
                'appointments.*',
                'call_requests.call_status as call_status' // Fetching call_status directly
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

        // Patient Data fetch
        $patient = User::find($appointment->user_id);

        // ✅ Data Transformation: Nesting user_data jaisa list function me hai
        $appointment->user_data = $patient ?? new \stdClass();
        $appointment->user_data->name = $appointment->patient_name ?? ($patient ? $patient->name : 'N/A');
        $appointment->user_data->mobile_number = $appointment->patient_mobile_number ?? ($patient ? $patient->mobile_number : 'N/A');

        // Image Logic (Consistent with getAppointments)
        if ($patient && !empty($patient->image)) {
            $appointment->user_data->image = (env('APP_ENV') == 'local')
                ? asset('storage/users/' . $patient->image)
                : asset('storage/app/public/users/' . $patient->image);
        } else {
            $appointment->user_data->image = asset('public/assets/admin/img/160x160/img1.jpg');
        }

        // ✅ Spelling Match for Dates/Types: Agar DB me wahi galat spelling h toh ye match karega
        // Note: list function me aapne 'appoinmtent_type' aur 'appoinmtent_date' use kiya h
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
    
    
    
    
    


    public function getSlots(Request $request){
        return response()->json(['status' => 'error', 'message' => 'Slot feature has been removed.', 'code' => 404]);
    }


    public function manageAppointmentStatus(Request $request){
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $appointmentId = $request->appointment_id;
        $status = $request->status;
        
        $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

        if(isset($appointmentData) && !empty($appointmentData)){
            DB::table('appointments')->where('id', $appointmentId)->update(['status' => $status]);
            if($status == "confirmed"){
                $message = "Appointment is already confirmed";

            } else {
                $message = "Appointment is already rejected";
            }

            if($appointmentData->status == $status) {
                
                return response()->json(['status' => 'error', 'code' => 200, 'message' => $message ,'data' => $appointmentData]);
            }
        
            if($status == "confirmed"){
                $message = "Appointment confirmed successfully";

            } else {
                $message = "Appointment rejected successfully";
            }
            // send notification to patient

            $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

            return response()->json(['status' => 'success', 'code' => 200, 'message' => $message ,'data' => $appointmentData]);
        } else {
            return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Invalid appointment id' ,'data' => null]);
        }
    }


    public function getTotalEarnings(Request $request){
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $user = JWTAuth::toUser($token);
        $doctorId = (isset($user) && !empty($user)) ? $user->id : '';   
       
        $appointmentData = DB::table('appointments')->select('*')->where(array('doctor_id' => $doctorId))->get();


        $totalEarnings = 0;
         
        if(isset($appointmentData) && !empty($appointmentData)){            
            foreach($appointmentData as $key => $value){
                $totalEarnings = $totalEarnings +$value->consultation_fee;
            }
        }

        $data = array('total_earnings' => $totalEarnings);

    
       
    
        $responseData = ['status' => 'success','message' => "Data found",'code' => 200,'data' => $data];
    
        return response()->json($responseData);
    }
    
    
    
    


    public function getTotalAppointments(Request $request){
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $doctorId = (isset($user) && !empty($user)) ? $user->id : '';   
       
        $appointmentData = DB::table('appointments')->select('*')->where(array('doctor_id' => $doctorId))->get();


        $data = array('total_appointments' => count($appointmentData));

    
       
    
        $responseData = ['status' => 'success','message' => "Data found",'code' => 200,'data' => $data];
    
        return response()->json($responseData);
    }
    
    
    public function getTotalOrdersAndEarnings(Request $request){
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $user = JWTAuth::toUser($token);
        $doctorId = (isset($user) && !empty($user)) ? $user->id : '';   
       
        $appointmentData = DB::table('appointments')->select('*')->where(array('doctor_id' => $doctorId))->get();
        $earningData    = DB::table('wallet_transactions')->select('*')->where(array('user_id' => $doctorId,'is_credit' => '1'))->get();


        $totalEarnings = 0;
         
        if(isset($earningData) && !empty($earningData)){            
            foreach($earningData as $key => $value){
                $totalEarnings = $totalEarnings +$value->amount;
            }
        }
        


        $data = array('total_earnings' => $totalEarnings,'total_orders' => count($appointmentData));

    
       
    
        $responseData = ['status' => 'success','message' => "Data found",'code' => 200,'data' => $data];
    
        return response()->json($responseData);
    }


    public function deleteSlot(Request $request) {
        return response()->json(['status' => 'error', 'message' => 'Slot feature has been removed.', 'code' => 404]);
    }


    public function getEarningReportGraph(Request $request){
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

        for($i=1;$i<=$lastDay;$i++){
            $dayName = ($i<10) ? "0".$i : $i;
            $date = date("Y-m-$dayName");         
            $earningSum = DB::table('wallet_transactions')
                ->where([
                    ['user_id', '=', $doctorId],
                    ['is_credit', '=', '1']
                ])
                ->whereDate('created_at', $date)
                ->sum('amount');
                
                 $totalEarningsCurrentMonth += $earningSum;

                array_push($earningData,array("date" => $date,'earning' => (string) $earningSum));
            
        }    
   


        if(count($earningData) > 0){
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
    
    
    public function getEarningReport(Request $request){
        try {
            // Get requested data
            $page = $request->get('page');
            $orderBy =  'desc';
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
    
            
             $transactions = DB::table('wallet_transactions')->select('*')->where(array('user_id'=>$userId ))->orderBy('id','desc')->paginate($perPage, ['*'], 'page', $page);     
            
    
            
            $transactions->getCollection()->transform(function ($transaction)  {
                
                $transaction->amount = (string) $transaction->amount;
                    $appointmentId = $transaction->order_id;
                    $appoimtmentData = Appointment::find($appointmentId);
                    $transaction->patient_data = User::find($appoimtmentData->user_id);
                    $userImage = $transaction->patient_data->image; 
                    $transaction->patient_data->name = $appoimtmentData->patent_name;
                    $transaction->patient_data->mobile_number = $appoimtmentData->patient_mobile_number;
                    if(!empty($userImage)){
                        $transaction->patient_data->image =  (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);  
                    } else {
                          $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                          $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");
                            
                          $transaction->patient_data->image = $imagePath;
                    } 
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

    
    
     /**
     * add meidicine
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function addMedicines(Request $request){
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
                    'appointment_id' =>  'required',
                    'medicines' =>  'required',
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
             
             
                         
             



            return response()->json(['status' => 'success', 'code' => 200,'message' => 'Meidicine added successfully','data' => $request->all()]);
        } catch (\Exception $e) {
            // Handle exceptions, if any
            
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }
    
    
    public function getWalletTransactions(Request $request){
        try {
            // Get requested data
            $page = $request->get('page');
            $orderBy =  'desc';
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
    
            
            $transactions = DB::table('wallet_transactions')->select('*')->where('user_id', $userId)->orderBy('id','desc')->paginate($perPage, ['*'], 'page', $page);    

            
            $transactions->getCollection()->transform(function ($transaction)  {
                $transaction->amount = (string) $transaction->amount;
                $appointmentId = $transaction->order_id;
                $appoimtmentData = Appointment::find($appointmentId);
                $transaction->patient_data = User::find($appoimtmentData->user_id);
                $userImage = $transaction->patient_data->image; 
                if(!empty($userImage)){
                    $transaction->patient_data->image =  (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);  
                } else {
                      $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                      $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");
                        
                      $transaction->patient_data->image = $imagePath;
                } 
                return $transaction;
               
            });
            
            
            
            if($transactions->total() > 0){
                $status = "success";
                $message = "Data found";
            } else {
                $status = "error";
                $message = "No data found";
            }
            
            
            $doctorData = User::find($userId);
            $walletBalance = $doctorData->wallet_amount ?? "0";
    
            $responseData = [
                'status' => $status,
                'message' => $message,
                'code' => 200,
                'data' => [
                    'wallet_balance' => $walletBalance,
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


    public function getPaymentLogs(Request $request){
        try {
            // Get requested data
            $page = $request->get('page');
            $orderBy =  'desc';
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
    
            
              $transactions = DB::table('payment_logs')->select('*')->where(array('user_id'=>$userId ))->orderBy('id','desc')->paginate($perPage, ['*'], 'page', $page);     
            
    
            
            $transactions->getCollection()->transform(function ($transaction)  {
                
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
    
    public function sendWithDrawalRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => $validator->errors()->first()]);
        }

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user ? $user->id : null;

        $bankData = DB::table("doctor_banks")->where("doctor_id", $userId)->first();
        if ($bankData) {
            $doctorData = User::find($userId);
            $doctorWalletAmount = $doctorData->wallet_amount;
            $amount = $request->amount;

            if ($amount > $doctorWalletAmount) {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'You cannot withdraw money more than your wallet balance']);
            }

            // Check if there is a pending withdrawal request
            $pendingRequest = WithDrawRequest::where('request_by', $userId)
                ->where('status', 'pending')
                ->first();

            if ($pendingRequest) {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'You already have a pending withdrawal request']);
            }

            // Capture bank details snapshot
            $accountDetails = json_encode([
                'bank_name' => $bankData->bank_name,
                'account_number' => $bankData->account_number,
                'ifsc_code' => $bankData->ifsc_code,
                'account_holder_name' => $bankData->account_holder_name
            ]);

            // Insert new withdrawal request using Model
            WithDrawRequest::create([
                'request_by' => $userId,
                'amount' => $amount,
                'status' => 'pending',
                'approved' => 0,
                'note' => $request->note,
                'account_details' => $accountDetails
            ]);

            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Request has been sent successfully']);
        } else {
            return response()->json(['status' => 'error', 'code' => 403, 'message' => 'Please add bank detail to send withdrawal request']);
        }
    }
    
    
    public function getPendingWithDrawalRequests(Request $request){
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user ? $user->id : null;
            // Check if there is a pending withdrawal request
            $pendingRequest = DB::table('withdrawl_requests')->where('request_by', $userId)->where('status', 'pending')->first();
            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found','data' => $pendingRequest]);
            
    }
    

    public function withDrawWalletAmount(Request $request){
        
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user ? $user->id : null;

        $bankData = DB::table("doctor_banks")->where("doctor_id",$userId)->get();
        if(count($bankData) > 0){
              
              $doctorData         = User::find($userId);
              $doctorEmail        = $doctorData->email;
              $doctorName        = $doctorData->name;
              $doctorWalletAmount = $doctorData->wallet_amount;
              $requestId = $request->request_id;
             
              
             
              
              
              $requestData = DB::table('withdrawl_requests')->where(['id' => $requestId,'request_by' => $userId])->first();
               $amount = $requestData->amount;
              if(!$requestData){
                  return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Invalid request id']);
              }
              
              if($requestData->status == 'pending'){
                  return response()->json(['status' => 'error', 'code' => 200, 'message' => 'You can withdraw amount after your request approval by admin']);
              }
              
              if($requestData->status == 'reject'){
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
                    
                return response()->json(['status' => 'error', 'code' => 200,'message' => 'Amount habe been withdrawn successfully'], 500);

                
                } catch (\Exception $e) {
                    // Handle exceptions
                    return response()->json(['status' => 'error', 'code' => 500,'message' => $e->getMessage()], 500);
                }

             
            
            
            
            
            
            
        } else {
            return response()->json(['status' => 'error', 'code' => 200,'message' => 'Please add bank detail to withdraw']);

        }
    }
    
    
    

       
    
}