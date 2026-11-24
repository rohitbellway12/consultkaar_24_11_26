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
    
    // public function getLabTestList(Request $request){
    //     try {
    //         // Get requested data
    //         $categoryId = $request->get('category_id');
    //         $page = $request->get('page');
    //         $orderBy = $request->get('order_by') ?? 'desc';
    //         $orderColumn = $request->get('order_column') ?? 'created_at';
    //         $perPage = 10; // Number of items to load per page
    //         $searchKey = $request->get('search_key');

    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = $user->id;

    //         $userData = User::find($userId);
    //         $langCode = $userData->lang_code;
    
    //         // Define the validation rules
    //         $validationRules = [
    //             'page' => 'required',
    //             'category_id' => 'required',
    //         ];
    
    //         // Validate the input data
    //         $validation = Validator::make($request->all(), $validationRules, [
    //             'page.required' => 'page is required.',
    //             'category_id.required' => 'category id is required.',
    //         ]);
    
    //         // Check for validation errors and return error response if any
    //         if ($validation->fails()) {
    //             return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
    //         }

    
    //         // Query to retrieve items
    //         $items = LabTest::select('*')
    //             ->where('category_id', $categoryId)
    //             ->when(!empty($searchKey), function ($query) use ($searchKey) {
    //                 $query->where('lab_test.name', 'like', '%' . $searchKey . '%');
    //             })
    //             ->orderBy($orderColumn, $orderBy)
    //             ->where(array('lab_test.status'=>1,'lab_test.lang_code' => $langCode))
    //             ->paginate($perPage, ['*'], 'page', $page);
    
    //         // Modify each item in the collection
    //         $items->getCollection()->transform(function ($item) {
    //             // Modify item properties here
                
    //           // Check and set description to blank if null
    //             if ($item->description === null) {
    //                 $item->description = '';
    //             }

    
    //             return $item;
    //         });
    
    //         if($items->total() > 0){
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
    //             'data' => $items->items(),
    //             'paginator' => [
    //                 'totalDocs' => $items->total(),
    //                 'limit' => $items->perPage(),
    //                 'page' => $items->currentPage(),
    //                 'totalPages' => $items->lastPage(),
    //                 'hasPrevPage' => $items->previousPageUrl() ? true : false,
    //                 'hasNextPage' => $items->nextPageUrl() ? true : false,
    //             ],
    //             ]
                
    //         ];

            
       
    
    //         // Return the JSON response
    //         return response()->json($responseData);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    //     }
    // }
    
    public function getLabTestList(Request $request){
    try {
        // Get requested data
        $categoryId = $request->get('category_id');
        $labId = $request->get('lab_id'); // ✅ Get lab_id
        $page = $request->get('page');
        $orderBy = $request->get('order_by') ?? 'desc';
        $orderColumn = $request->get('order_column') ?? 'created_at';
        $perPage = 10;
        $searchKey = $request->get('search_key');

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $userId = $user->id;

        $userData = User::find($userId);
        $langCode = $userData->lang_code;

        // Validate inputs
        $validation = Validator::make($request->all(), [
            'page' => 'required',
            'category_id' => 'required',
        ], [
            'page.required' => 'page is required.',
            'category_id.required' => 'category id is required.',
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
        }

        // Query with filters
        $items = LabTest::select('*')
            ->where('category_id', $categoryId)
            ->when(!empty($labId), function ($query) use ($labId) {
                $query->where('lab_id', $labId); // ✅ Apply lab_id filter
            })
            ->when(!empty($searchKey), function ($query) use ($searchKey) {
                $query->where('lab_test.name', 'like', '%' . $searchKey . '%');
            })
            ->where([
                ['lab_test.status', 1],
                ['lab_test.lang_code', $langCode]
            ])
            ->orderBy($orderColumn, $orderBy)
            ->paginate($perPage, ['*'], 'page', $page);

        // Transform result
        $items->getCollection()->transform(function ($item) {
            if ($item->description === null) {
                $item->description = '';
            }
            return $item;
        });

        // Final response
        $status = $items->total() > 0 ? "success" : "error";
        $message = $items->total() > 0 ? "Data found" : "No data found";

        return response()->json([
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
        ]);

    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    }
}

    
    public function getLabTestDetail(Request $request){
        try {
            
            // get medication data 


            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;

            $userData = User::find($userId);
            $langCode = $userData->lang_code;
            $labTestId = $request->lab_test_id;

            
            $labTests = LabTest::where(['status'=> 1,'lang_code' => $langCode,'id' => $labTestId])->get();

            if (count($labTests) > 0) {
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Data found','data' => $labTests[0]]);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found','data' => null ]);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage(),'data' => null]);
        }
    }
    


}
