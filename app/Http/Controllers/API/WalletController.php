<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Appointment;

class WalletController extends Controller
{
    //

     /**
     * Add amount in wallet
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function addAmountInWallet(Request $request){
        try {
            // Get requested data
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';
            $amount = $request->input('amount');
            
            

            // Define the validation rules
            $validationRules = [
                'amount' => 'numeric|min:50',

            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'amount.required' => 'Amount is required.',
            ]);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            $user = User::where('id', $userId)->first();
            $user->wallet_amount += $amount;
            $user->save();
           
           $paymentId =  DB::table('payment_logs')->insertGetId([
                'user_id' => $userId, 
                'amount' => $amount,
                'created_by' => $userId,
                'description' => "Recharge", 
                'created_at' => now(),
            ]);     
            
            
            DB::table('wallet_transactions')->insertGetId([
                'user_id' => $userId, 
                'amount' => $amount,
                'created_by' => $userId,
                'is_credit' => '1',
                'description' => "Recharge",
                'transaction_for' => "wallet",
                'created_at' => now(),
            ]);
            
            
            
           
                     
            
            return response()->json(['status' => 'success','message' => 'Amount added in wallet successfully', 'code' => 200, 'data' => ['payment_id' => $paymentId,'user_id' => $userId]]);
                

            
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => null], 500);
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
    //             $transactionFor = $transaction->transaction_for;
                
    //             if($transactionFor == 'appointment'){
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
    //             } else {
    //                 $patient_data = User::find($transaction->user_id);
    //                 $transaction->patient_data = $patient_data;
                
    //                 $userImage = $patient_data->image; 
    //                 $transaction->patient_data->name = $patient_data->name;
    //                 $transaction->patient_data->mobile_number = $patient_data->mobile_number;
    //                 if(!empty($userImage)){
    //                     $transaction->patient_data->image =  (env('APP_ENV') == 'local') ? asset('storage/users/' . $userImage) : asset('storage/app/public/users/' . $userImage);  
    //                 } else {
    //                       $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
    //                       $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");
                            
    //                       $transaction->patient_data->image = $imagePath;
    //                 }
                    
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
    
    public function getWalletTransactions(Request $request){
    try {
        // Get requested data
        $page = $request->get('page');
        $perPage = 10; 
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user ? $user->id : null;

        // Define the validation rules
        $validationRules = [
            'page' => 'required|integer|min:1',
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
        $transactions = DB::table('wallet_transactions')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);    

        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->amount = (string) $transaction->amount;
            $transactionFor = $transaction->transaction_for;
            
            if ($transactionFor == 'appointment') {
                $appointmentId = $transaction->order_id;
                $appointmentData = Appointment::find($appointmentId);
                
                // ✅ NULL CHECK ADD KIYA
                if ($appointmentData) {
                    $transaction->patient_data = new \stdClass();
                    $transaction->patient_data->name = $appointmentData->patent_name ?? 'N/A';
                    $transaction->patient_data->mobile_number = $appointmentData->patient_mobile_number ?? 'N/A';
                    
                    // Image handling
                    $userImage = $appointmentData->patient_image ?? null;
                    if (!empty($userImage)) {
                        $transaction->patient_data->image = (env('APP_ENV') == 'local') 
                            ? asset('storage/users/' . $userImage) 
                            : asset('storage/app/public/users/' . $userImage);
                    } else {
                        $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                        $imagePath = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
                        $transaction->patient_data->image = $imagePath;
                    }
                } else {
                    // ✅ Appointment not found - default data
                    $transaction->patient_data = new \stdClass();
                    $transaction->patient_data->name = 'Appointment Deleted';
                    $transaction->patient_data->mobile_number = 'N/A';
                    $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                    $imagePath = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
                    $transaction->patient_data->image = $imagePath;
                }
            } else {
                // For non-appointment transactions
                $patientData = User::find($transaction->user_id);
                
                $transaction->patient_data = new \stdClass();
                
                if ($patientData) {
                    $transaction->patient_data->name = $patientData->name ?? 'N/A';
                    $transaction->patient_data->mobile_number = $patientData->mobile_number ?? 'N/A';
                    
                    $userImage = $patientData->image;
                    if (!empty($userImage)) {
                        $transaction->patient_data->image = (env('APP_ENV') == 'local') 
                            ? asset('storage/users/' . $userImage) 
                            : asset('storage/app/public/users/' . $userImage);
                    } else {
                        $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                        $imagePath = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
                        $transaction->patient_data->image = $imagePath;
                    }
                } else {
                    // User not found
                    $transaction->patient_data->name = 'User Not Found';
                    $transaction->patient_data->mobile_number = 'N/A';
                    $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                    $imagePath = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
                    $transaction->patient_data->image = $imagePath;
                }
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


   

}

