<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;



class ProductController extends Controller
{
    //

     /**
     * It will get items .
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    
    public function getProductList(Request $request){
        try {
            // Get requested data
            $categoryId = $request->get('category_id');
            $page = $request->get('page');
            $orderBy = $request->get('order_by') ?? 'desc';
            $orderColumn = $request->get('order_column') ?? 'created_at';
            $perPage = 10; // Number of items to load per page
            $searchKey = $request->get('search_key');
    
            // Define the validation rules
            $validationRules = [
                'page' => 'required',
                'category_id' => 'required',
            ];
    
            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'page.required' => 'page is required.',
                'category_id.required' => 'category id is required.',
            ]);
    
            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
            }


            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $userData = User::find($userId);
            $langCode = $userData->lang_code;
    
            // Query to retrieve items
            $items = Product::select('products.*')
                ->where('category_id', $categoryId)
                ->when(!empty($searchKey), function ($query) use ($searchKey) {
                    $query->where('products.name', 'like', '%' . $searchKey . '%');
                })
                ->orderBy($orderColumn, $orderBy)
                ->where('products.status', 1)
                ->where('products.lang_code', $langCode)
                ->paginate($perPage, ['*'], 'page', $page);
    
            // Modify each item in the collection
            $items->getCollection()->transform(function ($item) {
                // Modify item properties here
                

                $item->price_with_gst = number_format(($item->price + (($item->gst / 100) * $item->price)), 2); // calculate price with GST
               
               
               

                 // Modify the item's image property
                 $item->image = (env('APP_ENV') == 'local') ? asset('storage/product/' . $item->image) : asset('storage/app/public/product/' . $item->image);
                 if ($item->image === null) {
                     $item->image = '';
                 }

                $all_item_images = array();
                if (isset($item->images) && !empty($item->images)) {
                    foreach ($item->images as $key => $val) {
                        $item_image = (env('APP_ENV') == 'local') ? asset('storage/product/' . $val) : asset('storage/app/public/product/' . $val);
                        array_push($all_item_images, $item_image);
                    }
                    $item->images = $all_item_images;
                }

                if ($item->images === null) {
                    $item->images = [];
                }

                // Check and set description to blank if null
                if ($item->description === null) {
                    $item->description = '';
                }

                $item->price_with_gst = number_format(($item->price + (($item->gst / 100) * $item->price)), 2); // calculate price with GST
    
                return $item;
            });
    
            if($items->total() > 0){
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
                'data' => $items->items(),
                'paginator' => [
                    'totalDocs' => $items->total(),
                    'limit' => $items->perPage(),
                    'page' => $items->currentPage(),
                    'totalPages' => $items->lastPage(),
                    'hasPrevPage' => $items->previousPageUrl() ? true : false,
                    'hasNextPage' => $items->nextPageUrl() ? true : false,
                ],
                ]
                
            ];

            
       
    
            // Return the JSON response
            return response()->json($responseData);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * It will get product detail.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    
    public function getProductDetail(Request $request)
    {
        try {
            // Get requested data
            $itemId = $request->get('product_id');
            
            // Define the validation rules
            $validationRules = [
                'product_id' => 'required',
            ]; 
            
            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'product_id.required' => 'product id is required.',
            ]);

            // Check for validation errors and return an error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            // Query to retrieve product items with associated colored images
            $itemDetail = Product::where('id', $itemId)
                ->select('*')
                ->get();  

                 // Check if the product item exists
                 if (count($itemDetail) == 0) {
                    return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Product not found.']);
                }

            $items = $itemDetail->map(function ($item) {
                 // Modify the item's image property
                 $main_item_image = (env('APP_ENV') == 'local') ? asset('storage/product/' . $item->image) : asset('storage/app/public/product/' . $item->image);
                 if ($main_item_image === null) {
                     $main_item_image = '';
                 }


                $item->price_with_gst = number_format(($item->price + (($item->gst / 100) * $item->price)), 2); // calculate price with GST
               
               
               

                 // Modify the item's image property
                 $item->image = (env('APP_ENV') == 'local') ? asset('storage/product/' . $item->image) : asset('storage/app/public/product/' . $item->image);
                 if ($item->image === null) {
                     $item->image = '';
                 }

                $all_item_images = array();
                if (isset($item->images) && !empty($item->images)) {
                    foreach ($item->images as $key => $val) {
                        $item_image = (env('APP_ENV') == 'local') ? asset('storage/product/' . $val) : asset('storage/app/public/product/' . $val);
                        array_push($all_item_images, $item_image);
                    }
                    $item->images = $all_item_images;
                }

                if ($item->images === null) {
                    $item->images = [];
                }

                // Check and set description to blank if null
                if ($item->description === null) {
                    $item->description = '';
                }

                 


                $item->category_id = $item->category_id;

               

               

                return $item;
            });

            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found successfully', 'data' => $items[0]]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }


}
