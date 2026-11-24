<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LabTest;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Category;


class LabTestController extends Controller
{
    //

     /**
     * It will get items .
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    
    public function getLabTestList(Request $request){
        try {
            // Get requested data
            $categoryId = $request->get('category_id');
            $page = $request->get('page');
            $orderBy = $request->get('order_by') ?? 'desc';
            $orderColumn = $request->get('order_column') ?? 'created_at';
            $perPage = 10; // Number of items to load per page
            $searchKey = $request->get('search_key');

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $userData = User::find($userId);
            $langCode = $userData->lang_code;
    
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

    
            // Query to retrieve items
            $items = LabTest::select('*')
                ->where('category_id', $categoryId)
                ->when(!empty($searchKey), function ($query) use ($searchKey) {
                    $query->where('lab_test.name', 'like', '%' . $searchKey . '%');
                })
                ->orderBy($orderColumn, $orderBy)
                ->where(array('lab_test.status'=>1,'lab_test.lang_code' => $langCode))
                ->paginate($perPage, ['*'], 'page', $page);
    
            // Modify each item in the collection
            $items->getCollection()->transform(function ($item) {
                // Modify item properties here
                
               // Check and set description to blank if null
                if ($item->description === null) {
                    $item->description = '';
                }

    
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
    


}
