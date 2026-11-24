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
use App\Models\User;
use App\CentralLogics\Helpers;
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
    
    public function bookItems(Request $request){
        try {

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';

            // Get requested data      
   
            $cartItems = $request->input('items');
            $addressId = $request->input('address_id');
            $amount = $request->input('amount');
            $transactionId = $request->input('transaction_id');

          
            

            // Define the validation rules
            $validationRules = [
                'items' =>  'required',
                'address_id' => 'required',
                'transaction_id' => 'required'
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            

         
              

         
            $cartTotalItemAmount = 0;

            
            $itemNames = "";
            if(isset($cartItems) && !empty($cartItems)){
                foreach($cartItems as $key => $val){
                    $itemName = $val['item_name'];
                    $itemId = $val['item_id'];
                    $quantity = $val['quantity'];
                    $itemType = $val['item_type'];
                    if($itemType == 'medicine'){
                        $productData =  Product::find($itemId);
                        $productName = $productData->name;
                        $productStock = $productData->total_stock;
                        if($productStock <= 0){
                            return response()->json(['status' => 'error', 'code' => 400, 'message' => 'Item ' .  $productName . ' is out of stock']);
                        }
                        $remainingStock = $productStock - $quantity;
                        Product::where('id', $itemId)->update(['total_stock' => $remainingStock]);
                        $itemNames = $itemNames." ".$itemName;
                        $cartTotalItemAmount = $cartTotalItemAmount + $val['item_price'];
                        
                    }
                   
                }
            }            
            
                $requestData = [

                    'user_id' => $userId,
                    'status' => 'ongoing',
                    'amount' => $cartTotalItemAmount,
                    'booking_items' => json_encode($cartItems, true),
                    'delivery_address_id' => $addressId,
                    'transaction_id' => $transactionId
                ];

                $newOrder = Order::create($requestData);

               

                $orderId = "URF".$newOrder->id;
                Order::where('id', $newOrder->id)->update(['order_id' => $orderId]);

                // clear cart

                Cart::where('user_id',$userId)->delete();

                $loginUserData = User::find($userId);
                
                
                $orderData = Order::where('id', $newOrder->id)->first();
                $orderData->booking_items = json_encode([]);
                            //  $orderData->booking_items = json_decode($orderData->booking_items);
                $orderData->expected_delivery_date = date("Y-m-d", strtotime($orderData->created_at . " +1 day"));
                
                // send push notification 

               /* $loginUserFcmToken = $loginUserData->fcm_token ?? '';

                $data = [
                    'title' => 'Order Placed',
                    'description' => 'Order placed successfully',
                    'order_id' => $orderId,
                    'image' => '',
                    'type'=> 'order_status'
                ];

                Helpers::send_push_notif_to_device($loginUserFcmToken,$data);*/


            return response()->json(['status' => 'success', 'code' => 200,'message' => 'Item booked successfully','data' => $orderData->toArray()]);
        } catch (\Exception $e) {
            // Handle exceptions, if any
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
    
        $bookingData = Order::select('order_id','created_at','booking_items')
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
        $bookingData = Order::where(array('order_id' => $bookingId,'user_id' => $customerId))->get();
    
        // Check if the order exists
        if (count($bookingData) == 0) {
            return response()->json(['status' => 'error', 'message' => 'Order not found', 'code' => 404]);
        }

    

        $bookingData = $bookingData->map(function ($item)  {
            $allData = array();
            $description = $item->description; 
            $bookingItems = json_decode($item->booking_items);
            $cartTotalItemAmount = 0;
            $totalOrderPrice = $item->paid_amount; 
            $pendingAmount = $item->pending_amount; 
            
            if(isset($bookingItems) && !empty($bookingItems)){
                foreach($bookingItems as $key => $val){
                    $cartTotalItemAmount = $cartTotalItemAmount + $val->item_price;
                }
            }            

            $orderId   = $item->order_id; 
            $cartTotalItemAmount = $cartTotalItemAmount;

            $deliveryCharge = $item->delivery_charge; 

            $addressId = $item->delivery_address_id;

            $shippingAddressData = UsersAddress::find($addressId);

            if(isset($shippingAddressData) && !empty($shippingAddressData)){

                $shippingAddress = $shippingAddressData->house_name . ",";


                // Add floor number with suffix
                $floorNumber = $shippingAddressData->floor_number;
                if ($floorNumber % 100 >= 11 && $floorNumber % 100 <= 13) {
                    $suffix = 'th';
                    } else {
                    switch ($floorNumber % 10) {
                    case 1:
                        $suffix = 'st';
                        break;
                    case 2:
                        $suffix = 'nd';
                        break;
                    case 3:
                        $suffix = 'rd';
                        break;
                    default:
                        $suffix = 'th';
                        break;
                    }
                }

                $shippingAddress .= $floorNumber . "<sup>". $suffix .  "</sup>". " floor " . "," . $shippingAddressData->landmark . "," . $shippingAddressData->area_name;
            } else {
                $shippingAddress = '';
            }

            $finalItemPrice   = $item->final_item_price; 

            array_push($allData,
                array(
                    'description' => $description,
                    'order_id' => $orderId,
                    'arriving_date' => date("M d,Y",strtotime($item->start_date)),
                    'total_items_price' => $cartTotalItemAmount,
                    'delivery_charge' => $deliveryCharge,
                    'total_order_price' => $totalOrderPrice,
                    'shipping_address' => $shippingAddress,
                    'pending_amount' => $pendingAmount,
                    'item_details'  => json_decode($item->cart_items,true),
                    'final_item_price' => $finalItemPrice - $deliveryCharge
                    
                    )
                );
            return $allData;
        });
                    
        if (count($bookingData) == 0) {
            return response()->json(['status' => 'error', 'message' => 'No data found', 'code' => 404]);
        }
        return response()->json(['status' => 'success', 'message' => 'Data found successfully', 'code' => 200, 'data' => $bookingData[0][0]]);
    }



     /**
     * book lab tests
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
     public function bookLabTest(Request $request){
        try {

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';
            $userData = User::find($userId);
            $langCode = $userData->lang_code;

            // Get requested data      
   
            $labTestId = $request->input('lab_test_id');
            $categoryId = $request->input('category_id');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $transactionId = $request->input('transaction_id');    
            $cartItems = $request->input('items');
            $addressId = $request->input('address_id');      
            

            // Define the validation rules
            $validationRules = [
                'lab_test_id' =>  'required',
                'category_id' => 'required',
                'transaction_id' => 'required'
            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            


            $labData = DB::table('labs')->where(['status' => 1,'lang_code' => $langCode])->get();



            $nearestLab = null;
            $minDistance = INF; // Initialize with infinity

            foreach ($labData as $lab) {
                $distance = $this->haversineDistance($lab->latitude, $lab->longitude, $latitude, $longitude);
                // Check if the lab is within 1 km
                if ($distance < 1 && $distance < $minDistance) {
                    $nearestLab = $lab;
                    $minDistance = $distance;
                }
            }



            if ($nearestLab) {
                // Nearest lab found within 1 km
                // echo "Nearest Lab ID: " . $nearestLab->id . ", Distance: " . $minDistance . " km";

                $labId = $nearestLab->id;   
                
                $cartTotalItemAmount = 0;

            
                $itemNames = "";
                if(isset($cartItems) && !empty($cartItems)){
                    foreach($cartItems as $key => $val){
                        $itemName = $val['item_name'];
                        $itemId = $val['item_id'];
                        $quantity = $val['quantity'];
                        $itemType = $val['item_type'];
                        if($itemType == 'lab_test'){
                            $labTestData = LabTest::find($labTestId);
                          
                            $itemNames = $itemNames." ".$itemName;
                            $cartTotalItemAmount = $cartTotalItemAmount + $val['item_price']*$val['quantity'];
                            
                        }
                       
                    }
                }            
                
            
                     







             
                $requestData = [
    
                    'user_id' => $userId,
                    'status' => 'booked',
                    'amount' => $cartTotalItemAmount,
                    'lab_test_id' => $labTestId,
                    'lab_id' => $labId,
                    'category_id' => $categoryId,
                    'transaction_id' => $transactionId,
                    'booking_items' => '',
                    'delivery_address_id' => $addressId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
    
                    $bookingId = DB::table('users_lab_tests')->insertGetId($requestData);
             
    
                    $orderId = "URF".$bookingId;
                    DB::table('users_lab_tests')
                    ->where('id', $bookingId)
                    ->update(['booking_id' => $orderId]);
                
    
                    $loginUserData = User::find($userId);
                    
                    
                    $orderData = DB::table('users_lab_tests')
                    ->where('id', $bookingId)->first();
                 
                    
                    // send push notification 
    
                    $loginUserFcmToken = $loginUserData->fcm_token ?? '';
    
                    $data = [
                        'title' => 'Lab test booked',
                        'description' => 'Lab test successfully',
                        'order_id' => $orderId,
                        'image' => '',
                        'type'=> 'order_status'
                    ];
    
                    Helpers::send_push_notif_to_device($loginUserFcmToken,$data);
    
    
                return response()->json(['status' => 'success', 'code' => 200,'message' => 'Lab test booked successfully','data' => $orderData]);



            } else {
                // No lab found within 1 km
                return response()->json(['status' => 'error', 'code' => 500, 'message' => 'No labs found'], 500);
            }

           // echo "<pre>";

        //    print_r($labData);

            

           
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
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


