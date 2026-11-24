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
use App\Models\LabTest;
use App\Models\Lab;
use App\Models\User;
use App\CentralLogics\Helpers;
use App\Models\UserLabTest;
use DB;

class BookingController extends Controller
{
    //

     /**
     * book items in cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
//     public function bookItems(Request $request){
//         try {
// // print_r($request->all());
// // die;
//             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $userId = (isset($user) && !empty($user)) ? $user->id : '';

//             // Get requested data      
   
//             $cartItems = $request->input('items');
//             $addressId = $request->input('address_id');
//             $amount = $request->input('amount');
//             $paymentBy =  $request->input('payment_by');

          
            

//             // Define the validation rules
//             $validationRules = [
//                 'items' =>  'required',
//                 'address_id' => 'required',
//                 'payment_by' => 'required|in:wallet,online'

               
//             ];

//             // Validate the input data
//             $validation = Validator::make($request->all(), $validationRules);

//             // Check for validation errors and return error response if any
//             if ($validation->fails()) {
//                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
//             }


//             $loginUserData = User::find($userId);

//             if($loginUserData->wallet_amount == 0){
//                 return response()->json(['status' => 'error', 'code' => 200, 'message' => "Insufficient wallet balance"]);
//             }

            

         
              

         
//             $cartTotalItemAmount = 0;

            
//             $itemNames = "";
//             if(isset($cartItems) && !empty($cartItems)){
//                 foreach($cartItems as $key => $val){
//                     $itemName = $val['item_name'];
//                     $itemId = $val['item_id'];
//                     $quantity = $val['quantity'];
//                     $itemType = $val['item_type'];
//                     if($itemType == 'medicine'){
//                         $productData =  Product::find($itemId);
//                         $productName = $productData->name;
//                         $productStock = $productData->total_stock;
//                         if($productStock <= 0){
//                             return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Item ' .  $productName . ' is out of stock']);
//                         }
//                         $remainingStock = $productStock - $quantity;
//                         Product::where('id', $itemId)->update(['total_stock' => $remainingStock]);
//                         $itemNames = $itemNames." ".$itemName;
//                         $cartTotalItemAmount = $cartTotalItemAmount + $val['item_price'];
                        
//                     }
                   
//                 }
//             }            
            
//                 $requestData = [

//                     'user_id' => $userId,
//                     'status' => 'ongoing',
//                     'amount' => $cartTotalItemAmount,
//                     'booking_items' => json_encode($cartItems, true),
//                     'delivery_address_id' => $addressId,
//                     'payment_by' => $paymentBy
//                 ];
//                 $loginUserData = User::find($userId);
                
//                 if($paymentBy == 'wallet'){
                    
//                     $walletAmount = $loginUserData->wallet_amount;
//                     if($walletAmount == 0){
//                         return response()->json(['status' => 'error', 'code' => 500, 'message' => 'Please recharge your wallet to book appointment'], 500);
    
//                     }
                    
//                     if($cartTotalItemAmount > $walletAmount){
//                         return response()->json(['status' => 'error', 'code' => 500, 'message' => 'Insufficient balance'], 500);
//                     }
//                 }

                  
                  
                  
                  
//                 $newOrder = Order::create($requestData);
                
               

//                 $orderId = "URF".$newOrder->id;
//                 Order::where('id', $newOrder->id)->update(['order_id' => $orderId]);

//                 // clear cart

//                 Cart::where('user_id',$userId)->delete();

                 
//                  if($loginUserData){
                     
//                   $orderId =   $newOrder->id;
                   
//                   if($paymentBy == 'wallet'){
//                         DB::table('wallet_transactions')->insert([
//                             'user_id' => $userId, 
//                             'amount' => $cartTotalItemAmount,
//                             'order_id' => $orderId,
//                             'created_by' => $userId,
//                             'transaction_for' => 'medicine',
//                             'is_credit' => '0',
//                             'description' => "Medicine booked", 
//                             'created_at' => now(),
//                         ]);      
//                   } else {
//                       DB::table('payment_logs')->insertGetId([
//                                 'user_id' => $userId, 
//                                 'amount' => $cartTotalItemAmount,
//                                 'created_by' => $userId,
//                                 'order_id' => $orderId,
//                                 'description' => "Medicine booked", 
//                                 'created_at' => now(),
//                             ]);
//                   }
                   
                 
                     
//                       $newwalletamount =   $cartTotalItemAmount - $loginUserData->wallet_amount;
//                       DB::table('users')->where('id',$userId)->update(['wallet_amount' => $newwalletamount]);
//                  }else{
//                      return response()->json(['status' => 'error', 'code' => 400,'message' => 'Something went wrong']);
//                  }
                
//                 $orderData = Order::where('id', $newOrder->id)->first();
//                 $orderData->booking_items = json_encode([]);
//                             //  $orderData->booking_items = json_decode($orderData->booking_items);
//                 $orderData->expected_delivery_date = date("Y-m-d", strtotime($orderData->created_at . " +1 day"));

                
//                 // send push notification 

//               $loginUserFcmToken = $loginUserData->fcm_token ?? '';

//                 $data = [
//                     'title' => 'Order Placed',
//                     'description' => 'Order placed successfully',
//                     'order_id' => $orderId,
//                     'image' => '',
//                     'type'=> 'order_status'
//                 ];

//                 Helpers::send_push_notif_to_device($loginUserFcmToken,$data);


//             return response()->json(['status' => 'success', 'code' => 200,'message' => 'Item booked successfully','data' => $orderData->toArray()]);
//         } catch (\Exception $e) {
//             // Handle exceptions, if any
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
//         }
//     }


public function leblist(Request $request)
{
    $categoryId = $request->input('category_id');

    $labTestsQuery = LabTest::query();

    if ($categoryId) {
        $labTestsQuery->where('category_id', $categoryId);
    }

    $labTests = $labTestsQuery->get();

    if ($labTests->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'code' => 404,
            'message' => 'No lab tests found',
            'data' => []
        ], 404);
    }

    $labTests = $labTests->map(function ($test) {
        if (!empty($test->image)) {
            $test->image = (env('APP_ENV') === 'local')
                ? asset('storage/' . str_replace('public/', '', $test->image))
                : asset('storage/app/public/public/' . str_replace('public/', '', $test->image));
        } else {
            $defaultPath = (env('APP_ENV') === 'local') ? '' : 'public/';
            $test->image = asset($defaultPath . 'assets/admin/img/160x160/img1.jpg');
        }
        return $test;
    });

    $groupedTests = $labTests->groupBy('lab_id');

    $labIds = $groupedTests->keys()->filter(function ($id) {
        return $id > 0;
    })->toArray();

 $labs = Lab::whereIn('id', $labIds)->get()->map(function ($lab) {
    if (!empty($lab->image)) {
        $lab->image = (env('APP_ENV') === 'local')
            ? asset('storage/lab/' . $lab->image)
            : asset('storage/app/public/lab/' . $lab->image);
    } else {
        $defaultPath = (env('APP_ENV') === 'local') ? '' : 'public/';
        $lab->image = asset($defaultPath . 'assets/admin/img/160x160/img1.jpg');
    }
    return $lab;
})->keyBy('id');


    $data = [];

    foreach ($groupedTests as $labId => $tests) {
        if ($labId == 0) {
            $labInfo = [
                'id' => 0,
                'name' => 'Admin Lab',
                'location' => 'Admin Panel',
                'status' => 1
            ];
        } else {
            $labInfo = $labs[$labId] ?? null;
        }

        $data[] = [
            'lab' => $labInfo,
            'lab_tests' => $tests->values()
        ];
    }

    return response()->json([
        'status' => 'success',
        'code' => 200,
        'message' => 'Lab tests fetched successfully',
        'category_id' => $categoryId,
        'data' => $data
    ]);
}



public function bookItems(Request $request){
    try {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user ? $user->id : '';

        // Get requested data      
        $cartItems = $request->input('items');
        $addressId = $request->input('address_id');
        $amount = $request->input('amount');
        $paymentBy =  $request->input('payment_by');

        $validationRules = [
            'items' =>  'required',
            'address_id' => 'required',
            'payment_by' => 'required|in:wallet,online'
        ];

        $validation = Validator::make($request->all(), $validationRules);
        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
        }

        $loginUserData = User::find($userId);
        if ($loginUserData->wallet_amount == 0) {
            return response()->json(['status' => 'error', 'code' => 200, 'message' => "Insufficient wallet balance"]);
        }

        $cartTotalItemAmount = 0;
        $vendorId = null; 

        if (!empty($cartItems)) {
            foreach ($cartItems as $val) {
                $itemId = $val['item_id'];
                $quantity = $val['quantity'];
                $itemType = $val['item_type'];

                if ($itemType == 'medicine') {
                    $productData = Product::find($itemId);
                    if (!$productData) {
                        return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Product not found']);
                    }

                    if (!$vendorId) {
                        $vendorId = $productData->user_id; 
                    }

                    if ($productData->total_stock <= 0) {
                        return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Item ' . $productData->name . ' is out of stock']);
                    }

                    $remainingStock = $productData->total_stock - $quantity;
                    Product::where('id', $itemId)->update(['total_stock' => $remainingStock]);

                    $cartTotalItemAmount += $val['item_price'];
                }
            }
        }

        $requestData = [
            'user_id' => $userId,
            'vendor_id' => $vendorId, 
            'status' => 'ongoing',
            'amount' => $cartTotalItemAmount,
            'booking_items' => json_encode($cartItems, true),
            'delivery_address_id' => $addressId,
            'payment_by' => $paymentBy
        ];

        if ($paymentBy == 'wallet') {
            if ($cartTotalItemAmount > $loginUserData->wallet_amount) {
                return response()->json(['status' => 'error', 'code' => 500, 'message' => 'Insufficient balance'], 500);
            }
        }

        $newOrder = Order::create($requestData);
        $orderId = "URF" . $newOrder->id;
        Order::where('id', $newOrder->id)->update(['order_id' => $orderId]);

        // Clear cart after order
        Cart::where('user_id', $userId)->delete();

        if ($paymentBy == 'wallet') {
            DB::table('wallet_transactions')->insert([
                'user_id' => $userId, 
                'amount' => $cartTotalItemAmount,
                'order_id' => $newOrder->id,
                'created_by' => $userId,
                'transaction_for' => 'medicine',
                'is_credit' => '0',
                'description' => "Medicine booked", 
                'created_at' => now(),
            ]);

            $newWalletAmount = $loginUserData->wallet_amount - $cartTotalItemAmount;
            DB::table('users')->where('id', $userId)->update(['wallet_amount' => $newWalletAmount]);
        } else {
            DB::table('payment_logs')->insert([
                'user_id' => $userId, 
                'amount' => $cartTotalItemAmount,
                'created_by' => $userId,
                'order_id' => $newOrder->id,
                'description' => "Medicine booked", 
                'created_at' => now(),
            ]);
        }

        $orderData = Order::where('id', $newOrder->id)->first();
        $orderData->booking_items = json_encode([]);
        $orderData->expected_delivery_date = date("Y-m-d", strtotime($orderData->created_at . " +1 day"));

        $loginUserFcmToken = $loginUserData->fcm_token ?? '';
        $data = [
            'title' => 'Order Placed',
            'description' => 'Order placed successfully',
            'order_id' => $orderId,
            'image' => '',
            'type'=> 'order_status'
        ];
        Helpers::send_push_notif_to_device($loginUserFcmToken, $data);

        return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Item booked successfully', 'data' => $orderData->toArray()]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
    }
}

       
    public function getBookings(Request $request){
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $customerId = (isset($user) && !empty($user)) ? $user->id : '';
    
        $page = $request->get('page');
        $orderBy = 'desc';
        $orderColumn = 'created_at';
        $perPage = 10;
    
        $validationRules = [
            'page' => 'required',
        ];
    
        $validation = Validator::make($request->all(), $validationRules, [
            'page.required' => 'page is required.',
        ]);
    
        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
        }
    
        $bookingData = Order::select('*')
            ->where(array('user_id' => $customerId))
            ->orderBy($orderColumn, $orderBy)
            ->paginate($perPage, ['*'], 'page', $page);

            $bookingData->getCollection()->transform(function ($item)  {

            $bookingItems = json_decode($item->booking_items);
            $totalItemAmount = 0;
            $itemNames = '';
        
            if (isset($bookingItems) && !empty($bookingItems)) {
                foreach ($bookingItems as $key => $val) {
                    if($val->item_type == 'medicine'){
                        $totalItemAmount += $val->item_price;
                        $itemNames .= "," . $val->item_name; 
                    }
                    
                }
                $itemNames = trim($itemNames, ','); 
            }
        
            $orderId = $item->order_id;
            $totalItemAmount = number_format($totalItemAmount, 0);
            $item->order_id = $orderId;
            $item->booking_date = date("d F Y", strtotime($item->created_at));
            $item->total_items = count($bookingItems);
            $item->item_names = $itemNames;
            $item->amount = $item->amount;
            $userId = $item->user_id;
            $userData = User::find($userId);
            $item->user_data = $userData;
            $deliveryAddressId = $item->delivery_address_id;
            $addressData = UsersAddress::find($deliveryAddressId);
            $item->delivery_address_data = $addressData;
            unset($item->created_at);
            unset($item->booking_items);

           
            
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
    


    public function getBookingDetail(Request $request){
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $customerId = (isset($user) && !empty($user)) ? $user->id : '';
       

        $bookingId = $request->get('booking_id');
        $bookingData = Order::where(array('order_id' => "URF".$bookingId))->get();
    
        // Check if the order exists
        if (count($bookingData) == 0) {
            return response()->json(['status' => 'error', 'message' => 'Order not found', 'code' => 404]);
        }

    

        $bookingData = Order::select('*')
        ->where(array('order_id' => "URF".$bookingId))->get();

        $bookingData->transform(function ($item)  {


  
        $bookingItems = json_decode($item->booking_items);
        $totalItemAmount = 0;
        $itemNames = '';
    
        if (isset($bookingItems) && !empty($bookingItems)) {
            foreach ($bookingItems as $key => $val) {
                if($val->item_type == 'medicine'){
                    $totalItemAmount += $val->item_price;
                    $itemNames .= "," . $val->item_name; // Append without leading comma
                }
                
            }
            $itemNames = trim($itemNames, ','); // Remove leading and trailing commas
        }
    
        $orderId = $item->order_id;
        $totalItemAmount = number_format($totalItemAmount, 0);
        $item->order_id = $orderId;
        $item->booking_date = date("d F Y", strtotime($item->created_at));
        $item->total_items = count($bookingItems);
        $item->item_names = $itemNames;
        $item->amount = $item->amount;
        $userId = $item->user_id;
        $userData = User::find($userId);
        $item->user_data = $userData;
        $deliveryAddressId = $item->delivery_address_id;
        $addressData = UsersAddress::find($deliveryAddressId);
        $item->delivery_address_data = $addressData;
        unset($item->created_at);
        unset($item->booking_items);

       
        
        return $item; // Return the modified item
    });

   
                    
        if (count($bookingData) == 0) {
            return response()->json(['status' => 'error', 'message' => 'No data found', 'code' => 404]);
        }
        return response()->json(['status' => 'success', 'message' => 'Data found successfully', 'code' => 200, 'data' => $bookingData[0]]);
    }



     /**
     * book lab tests
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    //  public function bookLabTest(Request $request){
    //     try {

    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = (isset($user) && !empty($user)) ? $user->id : '';
    //         $userData = User::find($userId);
    //         $langCode = $userData->lang_code;

    //         // Get requested data      
   
    //         $cartItems = $request->input('items');
            
    //         $labData = DB::table('labs')->where(['status' => 1,'lang_code' => $langCode])->get();
    //         $paymentBy =  $request->input('payment_by');


    //         $nearestLab = null;
    //         $minDistance = INF; // Initialize with infinity

    //             // Nearest lab found within 1 km
    //             // echo "Nearest Lab ID: " . $nearestLab->id . ", Distance: " . $minDistance . " km";

    //           //  $labId = $nearestLab->id;   
                
    //             $cartTotalItemAmount = 0;

            
    //             $itemNames = "";
    //             if(isset($cartItems) && !empty($cartItems)){
    //                 foreach($cartItems as $key => $val){
    //                     $itemName = $val['item_name'];
    //                     $itemId = $val['item_id'];
    //                     $quantity = $val['quantity'];
    //                     $itemType = $val['item_type'];
    //                     if($itemType == 'lab_test'){
    //                      //   $labTestData = LabTest::find($labTestId);
                          
    //                         $itemNames = $itemNames." ".$itemName;
    //                         $cartTotalItemAmount = $cartTotalItemAmount + $val['item_price']*$val['quantity'];
                            
    //                     }
                       
    //                 }
    //             }            
                
            
    //             $requestData = [
    
    //                 'user_id' => $userId,
    //                 'status' => 'booked',
    //                 'amount' => $cartTotalItemAmount,
    //                 //'lab_test_id' => $labTestId,
    //                 // 'lab_id' => $labId,
    //                 //'category_id' => $categoryId,
    //                 'booking_items' => json_encode($cartItems, true),
    //                 'payment_by' => $paymentBy,
    //               //  'delivery_address_id' => $addressId,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //             $loginUserData = User::find($userId);

                
    //             if($paymentBy == 'wallet'){
                    
    //                 $walletAmount = $loginUserData->wallet_amount;
    //                 if($walletAmount == 0){
    //                     return response()->json(['status' => 'error', 'code' => 500, 'message' => 'Please recharge your wallet to book appointment'], 500);
    
    //                 }
                    
    //                 if($cartTotalItemAmount > $walletAmount){
    //                     return response()->json(['status' => 'error', 'code' => 500, 'message' => 'Insufficient balance'], 500);
    //                 }
    //             }
    
    //                 $bookingId = DB::table('users_lab_tests')->insertGetId($requestData);
                    
                    
    //                 $orderId = "URF".$bookingId;
    //                 DB::table('users_lab_tests')
    //                 ->where('id', $bookingId)
    //                 ->update(['booking_id' => $orderId]);
                    
                    
    //                 $orderData = DB::table('users_lab_tests')->where('id', $bookingId)->first();
                    
    //                 if($loginUserData){
                        
                        
    //                     if($paymentBy == 'wallet'){    
                        
                        
    //                         DB::table('wallet_transactions')->insert([
    //                                 'user_id' => $userId, 
    //                                 'amount' => $cartTotalItemAmount,
    //                                 'order_id' => $orderId,
    //                                 'created_by' => $userId,
    //                                 'is_credit' => '0',
    //                                 'transaction_for' => 'lab_test',
    //                                 'description' => "Lab Test booked", 
    //                                 'created_at' => now(),
    //                         ]);
    //                     } else {
                            
    //                         DB::table('payment_logs')->insert([
    //                                 'user_id' => $userId, 
    //                                 'amount' => $cartTotalItemAmount,
    //                                 'order_id' => $orderId,
    //                                 'created_by' => $userId,
    //                                 'description' => "Lab Test booked", 
    //                                 'created_at' => now(),
    //                         ]);
    //                     }
                
                        
    //                     $newwalletamount = $loginUserData->wallet_amount - $cartTotalItemAmount;
    //                     DB::table('users')->where('id',$userId)->update(['wallet_amount' => $newwalletamount]);
                        
    //               }else{
    //                   return response()->json(['status' => 'error', 'code' => 400,'message' => 'Something went wrong']);
    //               }
                 
    //                 $loginUserFcmToken = $loginUserData->fcm_token ?? '';
                 
    //                 $data = [
    //                     'title' => 'Lab test booked',
    //                     'description' => 'Lab  booked successfully',
    //                     'order_id' => $orderId,
    //                     'image' => '',
    //                     'type'=> 'order_status'
    //                 ];
                   
    //                  Helpers::send_push_notif_to_device($loginUserFcmToken,$data);
    //                 Cart::where('user_id',$userId)->delete();

    
    //             return response()->json(['status' => 'success', 'code' => 200,'message' => 'Lab test booked successfully','data' => $orderData]);


           
    //     } catch (\Exception $e) {
    //         // Handle exceptions, if any
           
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
    //     }
    // }
      
public function bookLabTest(Request $request)
{
    try {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user->id ?? '';

        if (!$userId) {
            return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $userData = User::find($userId);
        $langCode = $userData->lang_code;
   
        $cartItems = $request->input('items');
        $paymentBy = $request->input('payment_by');

        $cartTotalItemAmount = 0;
        $itemNames = "";
        $labIds = []; 

        if (!empty($cartItems)) {
            foreach ($cartItems as $key => &$val) {  
                $itemName = $val['item_name'];
                $itemId = $val['item_id'];
                $quantity = $val['quantity'];
                $itemType = $val['item_type'];

                if ($itemType == 'lab_test') {
                    $labTest = LabTest::find($itemId);
                    $val['lab_id'] = $labTest ? $labTest->lab_id : 0; 
                    
                    if ($labTest && $labTest->lab_id > 0) {
                        $labIds[] = $labTest->lab_id; 
                    }

                    $itemNames .= " " . $itemName;
                    $cartTotalItemAmount += $val['item_price'] * $quantity;
                }
            }
        }

        $labId = !empty($labIds) ? $labIds[0] : 0;

        $requestData = [
            'user_id' => $userId,
            'lab_id' => $labId, 
            'status' => 'booked',
            'amount' => $cartTotalItemAmount,
            'booking_items' => json_encode($cartItems, true),
            'payment_by' => $paymentBy,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $loginUserData = User::find($userId);

        if ($paymentBy == 'wallet') {
            $walletAmount = $loginUserData->wallet_amount;
            if ($walletAmount == 0) {
                return response()->json(['status' => 'error', 'code' => 500, 'message' => 'Please recharge your wallet to book appointment'], 500);
            }
            if ($cartTotalItemAmount > $walletAmount) {
                return response()->json(['status' => 'error', 'code' => 500, 'message' => 'Insufficient balance'], 500);
            }
        }

        $bookingId = DB::table('users_lab_tests')->insertGetId($requestData);
        $orderId = "URF" . $bookingId;
        DB::table('users_lab_tests')->where('id', $bookingId)->update(['booking_id' => $orderId]);

        $orderData = DB::table('users_lab_tests')->where('id', $bookingId)->first();

        if ($loginUserData) {

            if ($paymentBy == 'wallet') {
                DB::table('wallet_transactions')->insert([
                    'user_id' => $userId,
                    'amount' => $cartTotalItemAmount,
                    'order_id' => $orderId,
                    'created_by' => $userId,
                    'is_credit' => '0',
                    'transaction_for' => 'lab_test',
                    'description' => "Lab Test booked",
                    'created_at' => now(),
                ]);
            } else {
                DB::table('payment_logs')->insert([
                    'user_id' => $userId,
                    'amount' => $cartTotalItemAmount,
                    'order_id' => $orderId,
                    'created_by' => $userId,
                    'description' => "Lab Test booked",
                    'created_at' => now(),
                ]);
            }

            $newWalletAmount = $loginUserData->wallet_amount - $cartTotalItemAmount;
            DB::table('users')->where('id', $userId)->update(['wallet_amount' => $newWalletAmount]);
        } else {
            return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Something went wrong']);
        }

        $loginUserFcmToken = $loginUserData->fcm_token ?? '';
        $data = [
            'title' => 'Lab test booked',
            'description' => 'Lab booked successfully',
            'order_id' => $orderId,
            'image' => '',
            'type' => 'order_status'
        ];
        Helpers::send_push_notif_to_device($loginUserFcmToken, $data);

        Cart::where('user_id', $userId)->delete();

        return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Lab test booked successfully', 'data' => $orderData]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
    }
}

public function getUserLabTests(Request $request)
{
    try {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json(['status' => 'error', 'code' => 400, 'message' => 'User ID is required'], 400);
        }

        $labTests = DB::table('users_lab_tests')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        $finalTests = [];

        foreach ($labTests as $test) {
            $items = json_decode($test->booking_items, true);
            $labTestItems = [];
            $labInfo = null;

            foreach ($items as $item) {
                if ($item['item_type'] == 'lab_test') {
                    $labTest = DB::table('lab_test')->where('id', $item['item_id'])->first();

                    if ($labTest) {
                        if (!$labInfo) {
                            $labInfo = DB::table('labs')->where('id', $labTest->lab_id)->first();
                        }

                        $labTestItems[] = [
                            'test_id' => $labTest->id,
                            'test_name' => $labTest->test_name ?? '',
                            'test_price' => $item['item_price'] ?? 0,
                            'quantity' => $item['quantity'] ?? 1,
                            'total' => ($item['item_price'] ?? 0) * ($item['quantity'] ?? 1),
                        ];
                    }
                }
            }

            $finalTests[] = [
                'id' => $test->id,
                'booking_id' => $test->booking_id,
                'user_id' => $test->user_id,
                'status' => $test->status,
                'amount' => $test->amount,
                'created_at' => $test->created_at,
                'payment_status' => $test->payment_status,
                'lab_status' => $test->lab_status,
                'transaction_id' => $test->transaction_id,
                'payment_by' => $test->payment_by,
                'lab_info' => [
                    'lab_id' => $labInfo->id ?? null,
                    'lab_name' => $labInfo->name ?? '',
                    'lab_address' => $labInfo->address ?? '',
                ],
                'lab_test' => $labTestItems,
            ];
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $finalTests
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'code' => 500,
            'message' => $e->getMessage()
        ]);
    }
}



    
    
    
    public function cancelItem(Request $request) {
        $bookingId = $request->input('booking_id');
        $itemType = $request->input('item_type');

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = (isset($user) && !empty($user)) ? $user->id : '';

        $validationRules = [
            'booking_id' => 'required', // Updated validation rule to accept strings
        ];
    
        // Validate the input data
        $validation = Validator::make($request->all(), $validationRules);
        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
        }
      
        $loginUserData = User::find($userId);

    
        $bookingId = "URF". $bookingId;
        // Assuming 'booking_id' is a string, fetch data based on the string value
       if($itemType == 'lab_test'){

            $BookingData = UserLabTest::where('booking_id', $bookingId)->first(); // Use first() to get a single result
            if (!$BookingData) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Booking not found.']);
            }
        
            if($BookingData->status == 'cancelled'){
                return response()->json(['status' => 'error', 'code' => 404,'message' => 'Booking already cancelled.']);
            }else{
                $BookingData->status = 'cancelled';
            }
            $BookingData->save();
            
       } else {

        $BookingData = Order::where('order_id', $bookingId)->first(); // Use first() to get a single result
        if (!$BookingData) {
            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Booking not found.']);
        }

        if($BookingData->status == 'cancelled'){
            return response()->json(['status' => 'error', 'code' => 404,'message' => 'Booking already cancelled.']);
        }else{
            $BookingData->status = 'cancelled';
        }
        $BookingData->save();
       

       }

       $Bookingamount = $BookingData->amount;


       if($BookingData->status == 'cancelled'){
            $UserData = DB::table('users')->where('id',$userId)->first();
            if($UserData){
                $newwalletamount = $UserData->wallet_amount + $Bookingamount;
                DB::table('users')->where('id',$userId)->update(['wallet_amount' => $newwalletamount]);

            }
        }
       
        
    
        // Returning the updated booking data as a JSON response
        return response()->json(['status' => 'success', 'message' => 'Booking cancelled successfully.', 'data' => $BookingData]);
    }
    
    

    function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        // Radius of the Earth in km
        $R = 6371;
    
        // Convert latitude and longitude from degrees to radians
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
    
        // Haversine formula
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $R * $c;
    
        return $distance; // Distance in kilometers
    }

    
    

}

