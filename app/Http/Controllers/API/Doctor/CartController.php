<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\LabTest;

class CartController extends Controller
{
    //

     /**
     * Add item in cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function addItemInCart(Request $request){
        try {
            // Get requested data
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';
            $itemId = $request->input('item_id');
            $itemType = $request->input('item_type');
            $quantity = $request->input('quantity');
            
            

            // Define the validation rules
            $validationRules = [
                'item_id' => 'required',
                'item_type' => 'required|in:medicine,lab_test',
                'quantity' => 'required',

            ];

            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'item_id.required' => 'Item id is required.',
                'item_type.required' => 'Item type is required.',
                'item_type.in' => 'Item type should be either medicine or lab_test.',
                'quantity.required' => 'Quantity is required.',
            ]);

            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            // Query to retrieve product
            if($itemType == 'medicine'){
                $itemDetail = Product::where('id', $itemId)->select('*')->get(); 

                // Check if the product item exists
                if (count($itemDetail) == 0) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Item not found.','data' => null]);
                }

            }
            
            
           

            // Check if the cart item already exists for the user and item
            $existingCartItem = Cart::where(array('user_id' => $userId , 'item_id' => $itemId , 'item_type' => $itemType))->first();

            if ($existingCartItem) {              

                if($quantity == 0){
                    Cart::where(array('user_id' => $userId , 'item_id' => $itemId , 'item_type' => $itemType))->delete();
                } else {
                    // If the cart item already exists, update the quantity
                    $existingCartItem->quantity = $quantity;
                    $existingCartItem->save();

                }
                $existingCartItem = Cart::where('user_id', $userId)->where('item_id', $itemId)->first();
                return response()->json(['status' => 'success','message' => 'Quantity updated in cart successfully', 'code' => 200, 'data' => $existingCartItem]);
            } else {
                if($quantity > 0){
                    $cart = new Cart;
                    $cart->item_id = $itemId;
                    $cart->item_type = $itemType;
                    $cart->quantity = $quantity;
                    $cart->user_id = $userId;
                    $cart->save();
                    
                    return response()->json(['status' => 'success','message' => 'Item added to cart successfully', 'code' => 200, 'data' => $cart]);
                } else {
                    return response()->json(['status' => 'error','message' => 'Please add quantity', 'code' => 200, 'data' => null]);
                }

            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => null], 500);
        }
    }

    public function getCartItems(Request $request){
        try {
            // Retrieve cart items for the authenticated user
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = (isset($user) && !empty($user)) ? $user->id : '';
            $itemType = $request->input('item_type');
        
          
            $cartItems = Cart::where(array('user_id' => $userId , 'item_type' => $itemType))->get();

            // Additional information related to the cart items
            $formattedCartItems = $cartItems->map(function ($cartItem) use($userId) {
                
               
                
                if($cartItem->item_type == 'medicine'){
                    // Assuming there's a relationship between Cart and Product models
                    $product = $cartItem->product; // Adjust this based on your actual relationship

                    // Modify the item's image property
                    $product->image = (env('APP_ENV') == 'local') ? asset('storage/product/' . $product->image) : asset('storage/app/public/product/' . $product->image);
                    if ($product->image === null) {
                    $product->image = '';
                    }



                    $itemImage = $product->image;

                    $productData = Product::find($product->id);


                    $priceWithGst = number_format(($productData->price + (($productData->gst / 100) * $productData->price)), 2); 

                    return [
                    'id' => $cartItem->id,
                    'item_id' => $product->id,
                    'item_name' => $product->name,
                    'item_type' => $cartItem->item_type,
                    'item_image' => $itemImage,
                    'user_id' => $userId,
                    'quantity' => $cartItem->quantity,
                    'item_price' => $cartItem->quantity*$priceWithGst,
                    ];
                } else {
                     // Assuming there's a relationship between Cart and Lab Test models
                     $lab_test = $cartItem->lab_test; // Adjust this based on your actual relationship

                     // Modify the item's image property
                     $lab_test->image = (env('APP_ENV') == 'local') ? asset('storage/lab_test/' . $lab_test->image) : asset('storage/app/public/lab_test/' . $lab_test->image);
                     if ($lab_test->image === null) {
                     $lab_test->image = '';
                     }
 
 
 
                     $itemImage = $lab_test->image;
 
                     $lab_testData = LabTest::find($lab_test->id);
 
 
                     $price = number_format(($lab_testData->price), 2); 
 
                     return [
                     'id' => $cartItem->id,
                     'item_id' => $lab_test->id,
                     'item_name' => $lab_test->name,
                     'item_type' => $cartItem->item_type,
                     'item_image' => '',
                     'user_id' => $userId,
                     'quantity' => $cartItem->quantity,
                     'item_price' => $cartItem->quantity*$price,
                     ];
                }
            });

            if(count($formattedCartItems) > 0){
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found', 'data' => $formattedCartItems]);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found', 'data' => $formattedCartItems]);
            }

          

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => null], 500);
        }
    }





    public function removeItemFromCart(Request $request){
        try {
            // Delete the cart item based on cart_id
            $cartId = $request->input('cart_id');
            $cartItem = Cart::where('id', $cartId)->find($cartId);

            if (!$cartItem) {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Cart item not found','data' => null]);
            }

            $cartItem->delete();
            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Item removed from cart','data' => $cartItem]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function emptyCart(Request $request){
        try {
            // Delete all items from the cart for the authenticated user
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $customerId = (isset($user) && !empty($user)) ? $user->id : '';

            $cartItems = Cart::where('user_id', $customerId)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Cart items not found','data' => null]);
            }

            // Delete all cart items
            $cartItems->each->delete();

            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'All items removed from cart','data' => null]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

}

