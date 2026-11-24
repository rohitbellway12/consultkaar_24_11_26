<?php

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
use App\CentralLogics\Helpers;
use DB;

class ConsultationController extends Controller
{
    //

     /**
     * book items in cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function consult(Request $request){
        try {

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';

            // Get requested data      
   
            $specialityId = $request->input('speciality_id');
            $consultationType = $request->input('consultation_type');
            $doctorId = $request->input('doctor_id');
            $patientName = $request->input('patent_name');
            $patientGender = $request->input('patient_gender');
            $title = $request->input('title');
            $description = $request->input('description');

          
            

            // Define the validation rules
            $validationRules = [
                'speciality_id' => 'required',
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }
            $requestData = [

                    'user_id' => $userId,
                    'doctor_id' => $doctorId,
                    'consultation_type' => $consultationType,
                    'speciality_id' => $specialityId,
                    'patent_name' => $patientName,
                    'patient_gender' => $patientGender,
                    'title' => $title,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

               $appointmentId = DB::table('consultations')->insertgetId($requestData);

         
                 // send push notification 

                 if($consultationType == 'free'){
                    $doctorData = User::find($doctorId);

                    $doctorFcmToken = $doctorData->fcm_token ?? '';
                    if(!empty($doctorFcmToken)){
                            $data = [
                            'title' => 'New consultation',
                            'description' => 'Patient'.$patientName. "has asked a new question",
                            'image' => '',
                            'type'=> 'consulation'
                        ];
        
                        Helpers::send_push_notif_to_device($doctorFcmToken,$data);
                    }
                    
                }

                
                
                $appointmentData =  DB::table('consultations')->where('id',$appointmentId)->first();
             
                
    

             


            return response()->json(['status' => 'success', 'code' => 200,'message' => 'Thanks for consulting .doctor will reply soon','data' => $appointmentData]);
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

       
    public function getConsultations(Request $request){
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $customerId = (isset($user) && !empty($user)) ? $user->id : '';
    
        $page = $request->get('page');
        $orderBy = 'desc';
        $orderColumn = 'created_at';
        $perPage = 10;
    
        // Define the validation rules
        $validationRules = [
            'page' => 'required',
        ];
    
        // Validate the input data
        $validation = Validator::make($request->all(), $validationRules, [
            'page.required' => 'page is required.',
        ]);
    
        // Check for validation errors and return error response if any
        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
        }
    
       


            $bookingData = DB::table('consultations')->select('*')
            ->where(array('user_id' => $customerId))
            ->orderBy($orderColumn, $orderBy)
            ->paginate($perPage, ['*'], 'page', $page);


            $bookingData->getCollection()->transform(function ($item)  {

                $doctorId = $item->doctor_id;
                $doctorData = User::find($doctorId);
                $item->doctor_data = $doctorData;
                $userId = $item->user_id;
                $userData = User::find($userId);
                $item->user_data = $userData;
               
                
                return $item; // Return the modified item
            });
        

        if($bookingData->total() > 0){
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
                'data' => $bookingData->items(),
                'paginator' => [
                    'totalDocs' => $bookingData->total(),
                    'limit' => $bookingData->perPage(),
                    'page' => $bookingData->currentPage(),
                    'totalPages' => $bookingData->lastPage(),
                    'hasPrevPage' => $bookingData->previousPageUrl() ? true : false,
                    'hasNextPage' => $bookingData->nextPageUrl() ? true : false,
                ],
            ]
        ];
    
        return response()->json($responseData);
    }
    


    public function replyPatientQuery(Request $request){
        try {

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';

            // Get requested data      
   
            $consultationId = $request->input('consultation_id');
            $message = $request->input('message');
            $doctorId = 3;
          
            

            // Define the validation rules
            $validationRules = [
                'consultation_id' => 'required',
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }
            $requestData = [

                    'consultation_id' => $consultationId,
                    'message' => $message,      
                    'created_at' => now(),
                    'updated_at' => now(),              

                ];

               $appointmentId = DB::table('consultations_replies')->insertgetId($requestData);

         


                
                
                $appointmentData =  DB::table('consultations_replies')->where('id',$appointmentId)->first();
             
                
    

             


            return response()->json(['status' => 'success', 'code' => 200,'message' => 'Reply given successfully','data' => $appointmentData]);
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

}



