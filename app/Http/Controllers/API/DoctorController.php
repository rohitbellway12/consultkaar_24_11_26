<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CallRequest;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Pagination\Paginator;
use App\Models\Disease;
use App\Models\Zone;
use App\Models\FavoriteDoctors;
use App\Models\RateReview;
use App\Models\Domain;
use App\Models\Specialization;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;

use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{


    // public function getDoctorList(Request $request)
// {
//     try {
//         // Get requested data
//         $specialityId = $request->get('speciality_id');
//         $diseaseId = $request->get('disease_id');
//         $page = $request->get('page');
//         $orderBy = 'desc';
//         $orderColumn = 'created_at';
//         $perPage = 10;
//         $searchKey = $request->get('search_key');
//         $latitudeToCheck = $request->get('latitude');
//         $longitudeToCheck = $request->get('longitude');
//         $locationId = $request->get('location_id');

    //         // Define the validation rules
//         $validationRules = [
//             'page' => 'required|integer|min:1',
//         ];

    //         // Validate the input data
//         $validation = Validator::make($request->all(), $validationRules, [
//             'page.required' => 'Page is required.',
//             'page.integer' => 'Page must be an integer.',
//             'page.min' => 'Page must be at least 1.',
//         ]);

    //         // Check for validation errors and return error response if any
//         if ($validation->fails()) {
//             return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
//         }

    //         // Query to retrieve items
//         $token = JWTAuth::getToken();
//         $user = JWTAuth::toUser($token);
//         $userId = $user ? $user->id : null;

    //         $users = User::select('users.*')->where('role_id', 3)
//             ->when($specialityId, function ($query) use ($specialityId) {
//                 $query->where('speciality_id', '=', $specialityId);
//             })
//             ->when($locationId, function ($query) use ($locationId) {
//                 $query->where('location_id', $locationId);
//             })
//             ->orderBy($orderColumn, $orderBy)
//             ->where('status', 1)
//             ->paginate($perPage, ['*'], 'page', $page);

    //         $users->getCollection()->transform(function ($user) use ($diseaseId, $specialityId, $userId) {
//             if(!empty($user->image)){
//                 $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $user->image) : asset('storage/app/public/users/' . $user->image);

    //             } else {
//                   $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
//                     $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");
//             }
//             $user->image = $imagePath;

    //             $diseaseData = Disease::find($diseaseId);

    //             $options = [
//                 "arunachal_pradesh" => "Arunachal Pradesh",
//                 "assam" => "Assam",
//                 "bihar" => "Bihar",
//                 "chhattisgarh" => "Chhattisgarh",
//                 "delhi" => "Delhi",
//                 "goa" => "Goa",
//                 "gujarat" => "Gujarat",
//                 "haryana" => "Haryana",
//                 "himachal_pradesh" => "Himachal Pradesh",
//                 "jammu_and_kashmir" => "Jammu and Kashmir",
//                 "jharkhand" => "Jharkhand",
//                 "karnataka" => "Karnataka",
//                 "kerala" => "Kerala",
//                 "madhya_pradesh" => "Madhya Pradesh",
//                 "maharashtra" => "Maharashtra",
//                 "manipur" => "Manipur",
//                 "meghalaya" => "Meghalaya",
//                 "mizoram" => "Mizoram",
//                 "nagaland" => "Nagaland",
//                 "odisha" => "Odisha",
//                 "punjab" => "Punjab",
//                 "rajasthan" => "Rajasthan",
//                 "andhra_pradesh" => "Andhra Pradesh",
//                 "sikkim" => "Sikkim",
//                 "tamil_nadu" => "Tamil Nadu",
//                 "telangana" => "Telangana",
//                 "tripura" => "Tripura",
//                 "uttar_pradesh" => "Uttar Pradesh",
//                 "west_bengal" => "West Bengal"
//             ];

    //             $user->state = $user->state ? $options[$user->state] : null;
//             $user->consultation_fee = $user->consultation_fee;

    //             if (isset($diseaseData) && !empty($diseaseData)) {
//                 if ($diseaseData->speciality_id == $user->speciality_id) {
//                     return $user;
//                 }
//             }

    //             $specialityData = Speciality::find($specialityId);
//             if (isset($specialityData) && !empty($specialityData)) {
//                 $user->speciality_name = $specialityData->name;
//             } else {
//                 $user->speciality_name = null;
//             }
//             $doctorId = $user->id;

    //             $favoriteDoctorData = FavoriteDoctors::where(['doctor_id' => $doctorId, 'user_id' => $userId])->first();
//             $user->is_favorite = ($favoriteDoctorData !== null) ? 1 : 0;

    //             $rateReviewData = RateReview::where(['user_id' => $userId, 'doctor_id' => $doctorId, 'type' => 'doctor'])->first();
//             $user->rating = ($rateReviewData !== null) ? $rateReviewData->rating : "0";
//             $user->name = $user->name;

    //             return $user;
//         });

    //         $status = $users->total() > 0 ? "success" : "error";
//         $message = $users->total() > 0 ? "Data found" : "No data found";

    //         $responseData = [
//             'status' => $status,
//             'message' => $message,
//             'code' => 200,
//             'data' => [
//                 'data' => $users->items(),
//                 'paginator' => [
//                     'totalDocs' => $users->total(),
//                     'limit' => $users->perPage(),
//                     'page' => $users->currentPage(),
//                     'totalPages' => $users->lastPage(),
//                     'hasPrevPage' => $users->previousPageUrl() ? true : false,
//                     'hasNextPage' => $users->nextPageUrl() ? true : false,
//                 ],
//             ]
//         ];

    //         return response()->json($responseData);

    //     } catch (\Exception $e) {
//         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//     }
// }

    public function getSpecialitiesByDomain(Request $request)
    {
        // dd($request->all());
        try {

            $domainId = $request->get('domain_id'); // DOMAIN ID
            $page = $request->get('page', 1);
            $locationId = $request->get('location_id');
            $perPage = 10;

            $validator = Validator::make($request->all(), [
                'page' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => $validator->errors()->first()
                ]);
            }


            $token = JWTAuth::getToken();
            $authUser = $token ? JWTAuth::toUser($token) : null;
            $userId = $authUser ? $authUser->id : null;


            $specialityIds = [];
            if ($domainId) {
                $specialityIds = Speciality::where('domain_id', $domainId)
                    ->pluck('id')
                    ->toArray();
            }

            $users = User::where('role_id', 3)
                ->where('status', 1)

                ->when($locationId, function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                })

                ->when(!empty($specialityIds), function ($q) use ($specialityIds) {
                    $q->whereIn('speciality_id', $specialityIds);
                })

                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);


            $users->getCollection()->transform(function ($user) use ($userId) {

                // Image
                if (!empty($user->image)) {
                    $user->image = (env('APP_ENV') == 'local')
                        ? asset('storage/users/' . $user->image)
                        : asset('storage/app/public/users/' . $user->image);
                } else {
                    $user->image = asset('assets/admin/img/160x160/img1.jpg');
                }

                // State Mapping
                $states = [
                    "andhra_pradesh" => "Andhra Pradesh",
                    "arunachal_pradesh" => "Arunachal Pradesh",
                    "assam" => "Assam",
                    "bihar" => "Bihar",
                    "chhattisgarh" => "Chhattisgarh",
                    "delhi" => "Delhi",
                    "goa" => "Goa",
                    "gujarat" => "Gujarat",
                    "haryana" => "Haryana",
                    "himachal_pradesh" => "Himachal Pradesh",
                    "jammu_and_kashmir" => "Jammu and Kashmir",
                    "jharkhand" => "Jharkhand",
                    "karnataka" => "Karnataka",
                    "kerala" => "Kerala",
                    "madhya_pradesh" => "Madhya Pradesh",
                    "maharashtra" => "Maharashtra",
                    "odisha" => "Odisha",
                    "punjab" => "Punjab",
                    "rajasthan" => "Rajasthan",
                    "tamil_nadu" => "Tamil Nadu",
                    "telangana" => "Telangana",
                    "uttar_pradesh" => "Uttar Pradesh",
                    "west_bengal" => "West Bengal"
                ];

                $user->state = $user->state ? ($states[$user->state] ?? null) : null;

                // Speciality Name
                $user->speciality_name = Speciality::where('id', $user->speciality_id)->value('name');

                // Favorite Doctor
                $user->is_favorite = FavoriteDoctors::where([
                    'doctor_id' => $user->id,
                    'user_id' => $userId
                ])->exists() ? 1 : 0;

                //    $user->consultation_count = Appointment::where('doctor_id', $user->id)->where('status', 'confirmed')->count();
                //   $doctorData->consultation_count_complete = Appointment::where('doctor_id', $user->id)->where('status', 'completed')->count();
                $completedCalls = CallRequest::where('doctor_id', $user->id)->where('call_status', 'complete')->count();
                $user->consultation_count = $completedCalls;
                $user->consultation_count_complete = $completedCalls;

                // $user->consultation_count = CallRequest::where('doctor_id', $user->id)->where('call_status', 'complete')->count();
                // Followers Count
                $user->followers_count = FavoriteDoctors::where('doctor_id', $user->id)->count();

                // 1. User's specific rating (Your Rating)
                $rateReviewData = RateReview::where([
                    'user_id' => $userId,
                    'doctor_id' => $user->id,
                    'type' => 'doctor'
                ])->first();
                $user->rating = ($rateReviewData !== null) ? $rateReviewData->rating : "0";

                // 2. Overall Average Rating
                $avgRating = RateReview::where([
                    'doctor_id' => $user->id,
                    'type' => 'doctor'
                ])->avg('rating');
                $user->average_rating = $avgRating ? round($avgRating, 1) : "0";

                // 3. Total Reviews Count
                $user->total_reviews = RateReview::where([
                    'doctor_id' => $user->id,
                    'type' => 'doctor'
                ])->count();

                return $user;
            });


            return response()->json([
                'status' => $users->total() > 0 ? 'success' : 'error',
                'message' => $users->total() > 0 ? 'Data found' : 'No data found',
                'code' => 200,

                'data' => $users->items(),
                'paginator' => [
                    'totalDocs' => $users->total(),
                    'limit' => $users->perPage(),
                    'page' => $users->currentPage(),
                    'totalPages' => $users->lastPage(),
                    'hasPrevPage' => $users->previousPageUrl() ? true : false,
                    'hasNextPage' => $users->nextPageUrl() ? true : false,

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


    // 

    // public function getSpecialitiesByDomain(Request $request)
// {
//     try {

    //         $domainId = $request->get('domain_id');

    //         $validator = Validator::make($request->all(), [
//             'domain_id' => 'required|integer|exists:domains,id',
//         ], [
//             'domain_id.required' => 'Domain ID is required',
//             'domain_id.exists'   => 'Invalid Domain ID',
//         ]);

    //         if ($validator->fails()) {
//             return response()->json([
//                 'status'  => 'error',
//                 'code'    => 422,
//                 'message' => $validator->errors()->first(),
//             ]);
//         }

    //         $specialities = Speciality::where('domain_id', $domainId)
//             ->select('id', 'name', 'domain_id', 'image')
//             ->orderBy('name', 'asc')
//             ->get();

    //         // ✅ IMAGE FULL PATH
//         $specialities->transform(function ($item) {

    //             if (!empty($item->image)) {
//                 $item->image = (env('APP_ENV') == 'local')
//                     ? asset('storage/speciality/' . $item->image)
//                     : asset('storage/app/public/speciality/' . $item->image);
//             } else {
//                 $item->image = asset('assets/admin/img/160x160/img1.jpg');
//             }

    //             return $item;
//         });

    //         return response()->json([
//             'status'  => $specialities->count() > 0 ? 'success' : 'error',
//             'code'    => 200,
//             'message' => $specialities->count() > 0 ? 'Data found' : 'No data found',
//             'data'    => $specialities
//         ]);

    //     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => 'error',
//             'code'    => 500,
//             'message' => $e->getMessage(),
//         ]);
//     }
// }

    private function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
    {
        $c = false;
        $j = $points_polygon - 1;

        for ($i = 0; $i < $points_polygon; $i++) {
            if (
                ($vertices_y[$i] > $latitude_y) != ($vertices_y[$j] > $latitude_y) &&
                ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i])
            ) {
                $c = !$c;
            }
            $j = $i;
        }
        return $c;
    }


    /**
     * Get details of a doctor.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    //  public function getDoctorDetail(Request $request)
//     {

    //         // Try to get customer details
//         try {

    //             // Get customer_id from the token

    //             $token = JWTAuth::getToken();
//             $user = JWTAuth::toUser($token);
//             $userId = $user ? $user->id : null;


    //             $doctorId = $request->get('doctor_id');         


    //             // Find the user by user_id
//             $doctorData = User::where('id', $doctorId)->where('role_id', 3)->first();

    //             if ($doctorData) {
//                 $doctorImage = $doctorData->image; 
//                 if(!empty($doctorImage)){
//                     $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $doctorImage) : asset('storage/app/public/users/' . $doctorImage);
//                     $doctorData->image =  $imagePath;
//                 }
//                 else {
//                     $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
//                     $imagePath =  asset($assetPrefixPath  . "/assets/admin/img/160x160/img1.jpg");
//                     $doctorData->image =  $imagePath;
//                 }

    //                 $favoriteDoctorData = FavoriteDoctors::where(array('doctor_id' => $doctorId , 'user_id' =>  $userId))->first();

    //                 // Set is_favorite to 1 if it's not NULL, otherwise set it to 0
//                 $doctorData->is_favorite = ($favoriteDoctorData !== null) ? 1 : 0;

    //                 // Get followers count
//                 $doctorData->followers_count = FavoriteDoctors::where('doctor_id', $doctorId)->count();
//                 $rateReviewData =  RateReview::where(array('user_id' => $userId , 'doctor_id' => $doctorId , 'type' => 'doctor'))->first();

    //                 // Get consultation count (total completed appointments)
//                 $doctorData->consultation_count = Appointment::where('doctor_id', $doctorId)->where('status', 'confirmed')->count();




    //                 $specialityData = Speciality::find($doctorData->speciality_id);

    //                 if (isset($specialityData) && !empty($specialityData)) {
//                     $doctorData->speciality_name = $specialityData->name;
//                 } else {
//                     $doctorData->speciality_name = null;
//                 }
//                 $doctorData->rating = ($rateReviewData !== null) ? $rateReviewData->rating : "0";  
//                 $doctorData->name = "Dr. ". $doctorData->name;

    //                 return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Data found successfully' , 'data' => $doctorData]);
//             } else {
//                 return response()->json(['code' => 200, 'status' => 'error', 'message' => 'No data found','data' => null]);
//             }
//         } catch (\Exception $e) {
//             //die;
//             // Handle exceptions, if any
//             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
//         }
//     }

    // public function getDoctorDetail(Request $request)
    // {

    //     // Try to get customer details
    //     try {

    //         // Get customer_id from the token

    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $userId = $user ? $user->id : null;


    //         $doctorId = $request->get('doctor_id');


    //         // Find the user by user_id
    //         $doctorData = User::where('id', $doctorId)->where('role_id', 3)->first();

    //         if ($doctorData) {
    //             $doctorImage = $doctorData->image;
    //             if (!empty($doctorImage)) {
    //                 $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $doctorImage) : asset('storage/app/public/users/' . $doctorImage);
    //                 $doctorData->image = $imagePath;
    //             } else {
    //                 $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
    //                 $imagePath = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
    //                 $doctorData->image = $imagePath;
    //             }

    //             $favoriteDoctorData = FavoriteDoctors::where(array('doctor_id' => $doctorId, 'user_id' => $userId))->first();

    //             // Set is_favorite to 1 if it's not NULL, otherwise set it to 0
    //             $doctorData->is_favorite = ($favoriteDoctorData !== null) ? 1 : 0;

    //             // Get followers count
    //             $doctorData->followers_count = FavoriteDoctors::where('doctor_id', $doctorId)->count();

    //             // Get consultation count (total completed appointments)
    //             // Get consultation count (total confirmed appointments)
    //             $doctorData->consultation_count = Appointment::where('doctor_id', $doctorId)->where('status', 'confirmed')->count();
    //             $doctorData->consultation_count_complete = Appointment::where('doctor_id', $doctorId)->where('status', 'completed')->count();


    //             // 1. User's specific rating (Your Rating)
    //             $rateReviewData = RateReview::where(array('user_id' => $userId, 'doctor_id' => $doctorId, 'type' => 'doctor'))->first();
    //             $doctorData->rating = ($rateReviewData !== null) ? $rateReviewData->rating : "0";

    //             // 2. Overall Average Rating
    //             $avgRating = RateReview::where('doctor_id', $doctorId)->where('type', 'doctor')->avg('rating');
    //             $doctorData->average_rating = $avgRating ? round($avgRating, 1) : "0";

    //             // 3. Total Reviews Count
    //             $doctorData->total_reviews = RateReview::where('doctor_id', $doctorId)->where('type', 'doctor')->count();

    //             $specialityData = Speciality::find($doctorData->speciality_id);

    //             if (isset($specialityData) && !empty($specialityData)) {
    //                 $doctorData->speciality_name = $specialityData->name;
    //             } else {
    //                 $doctorData->speciality_name = null;
    //             }

    //             $doctorData->name = $doctorData->name;

    //             return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Data found successfully', 'data' => $doctorData]);
    //         } else {
    //             return response()->json(['code' => 200, 'status' => 'error', 'message' => 'No data found', 'data' => null]);
    //         }
    //     } catch (\Exception $e) {
    //         //die;
    //         // Handle exceptions, if any
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    //     }
    // }


   public function getDoctorDetail(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user ? $user->id : null;

            $doctorId = $request->get('doctor_id');

            if (!$doctorId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'doctor_id is required'
                ], 422);
            }

            $doctorData = User::where('id', $doctorId)
                ->where('role_id', 3)
                ->first();

            if (!$doctorData) {
                return response()->json([
                    'code' => 200,
                    'status' => 'error',
                    'message' => 'No data found',
                    'data' => null
                ]);
            }

            // 🖼 Image Handling
            if (!empty($doctorData->image)) {
                $imagePath = (env('APP_ENV') == 'local')
                    ? asset('storage/users/' . $doctorData->image)
                    : asset('storage/app/public/users/' . $doctorData->image);

                $doctorData->image = $imagePath;
            } else {
                $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
                $doctorData->image = asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
            }

            // ❤️ Favourite Check
            $favoriteDoctorData = FavoriteDoctors::where([
                'doctor_id' => $doctorId,
                'user_id' => $userId
            ])->first();

            $doctorData->is_favorite = $favoriteDoctorData ? 1 : 0;

            // 👥 Followers Count
            $doctorData->followers_count = FavoriteDoctors::where('doctor_id', $doctorId)->count();

            // ⭐ Rating (User Specific)
            $rateReviewData = RateReview::where([
                'user_id' => $userId,
                'doctor_id' => $doctorId,
                'type' => 'doctor'
            ])->first();

            $doctorData->rating = $rateReviewData ? $rateReviewData->rating : "0";

            // ⭐ Average Rating
            $avgRating = RateReview::where('doctor_id', $doctorId)
                ->where('type', 'doctor')
                ->avg('rating');

            $doctorData->average_rating = $avgRating ? round($avgRating, 1) : "0";

            // ⭐ Total Reviews
            $doctorData->total_reviews = RateReview::where('doctor_id', $doctorId)
                ->where('type', 'doctor')
                ->count();

            // 🩺 Speciality
            $specialityData = Speciality::find($doctorData->speciality_id);
            $doctorData->speciality_name = $specialityData ? $specialityData->name : null;

            // 🟢 Availability Status (busy ya available) — online_status se alag hai
            // online_status: 0=offline, 1=online  |  availability_status: available/busy
            // Naye/purane doctors jinka null hai unka default 'available' hoga
            $doctorData->availability_status = in_array($doctorData->availability_status, ['available', 'busy'])
                ? $doctorData->availability_status
                : 'available';


            $callCounts = DB::table('call_requests')
                ->select('call_type', DB::raw('COUNT(*) as total'))
                ->where('doctor_id', $doctorId)
                ->where('call_status', 'complete')
                ->whereIn('call_type', ['chat', 'audio', 'video'])
                ->groupBy('call_type')
                ->pluck('total', 'call_type');

            $doctorData->chat_completed_count = $callCounts['chat'] ?? 0;
            $doctorData->audio_completed_count = $callCounts['audio'] ?? 0;
            $doctorData->video_completed_count = $callCounts['video'] ?? 0;

            $doctorData->total_calls =
                $doctorData->chat_completed_count +
                $doctorData->audio_completed_count +
                $doctorData->video_completed_count;


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data found successfully',
                'data' => $doctorData
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * It will mark favorite / unfavorite as doctor.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    // public function markFavUnFavAsDoctor(Request $request)
    // {
    //     try {
    //         // Get requested data

    //         $token = JWTAuth::getToken();
    //         $user = JWTAuth::toUser($token);
    //         $customerId = (isset($user) && !empty($user)) ? $user->id : '';
    //         $doctorId = $request->input('doctor_id');

    //         // Define the validation rules
    //         $validationRules = [
    //             'doctor_id' => 'required',
    //         ];



    //         // Validate the input data
    //         $validation = Validator::make($request->all(), $validationRules, [
    //             'doctor_id.required' => 'Doctor id is required.',
    //         ]);


    //         // Check for validation errors and return error response if any
    //         if ($validation->fails()) {
    //             return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
    //         }

    //         // Find the user by customer_id
    //         $userData = User::find($customerId);

    //         if (!$userData) {
    //             return response()->json(['status' => 'error', 'code' => 404, 'message' => 'User does not exist', 'data' => null]);
    //         }



    //         // Check if the doctor is already favorite for the user
    //         $existingFavoriteDoctor = FavoriteDoctors::where(array('doctor_id' => $doctorId, 'user_id' => $customerId))->first();



    //         // Update an existing favorite item
    //         if ($existingFavoriteDoctor) {
    //             $existingFavoriteDoctor->delete();
    //             return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Doctor is removed from favorite', 'data' => null]);
    //         } else {
    //             $result = FavoriteDoctors::create([
    //                 'doctor_id' => $doctorId,
    //                 'user_id' => $customerId,
    //             ]);
    //             return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Doctor is added to favorite', 'data' => $result]);
    //         }

    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    //     }
    // }
     public function markFavUnFavAsDoctor(Request $request)
    {
        try {
            // Get requested data

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $customerId = (isset($user) && !empty($user)) ? $user->id : '';
            $doctorId = $request->input('doctor_id');

            // Define the validation rules
            $validationRules = [
                'doctor_id' => 'required',
            ];



            // Validate the input data
            $validation = Validator::make($request->all(), $validationRules, [
                'doctor_id.required' => 'Doctor id is required.',
            ]);


            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            // Find the user by customer_id
            $userData = User::find($customerId);

            if (!$userData) {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'User does not exist', 'data' => null]);
            }



            // Check if the doctor is already favorite for the user
            $existingFavoriteDoctor = FavoriteDoctors::where(array('doctor_id' => $doctorId, 'user_id' => $customerId))->first();



            // Update an existing favorite item
            if ($existingFavoriteDoctor) {
                FavoriteDoctors::where(array('doctor_id' => $doctorId, 'user_id' => $customerId))->delete();
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Doctor is removed from favorite', 'data' => null]);
            } else {
                $result = FavoriteDoctors::create([
                    'doctor_id' => $doctorId,
                    'user_id' => $customerId,
                ]);
                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Doctor is added to favorite', 'data' => $result]);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }


    /**
     * It will get favorite doctor list of users .
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    //  public function getFavoriteDoctorList(Request $request){
    //     try {
    //         // Get requested data
    //         $page = $request->get('page');
    //         $orderBy =  'desc';
    //         $orderColumn = 'created_at';
    //         $perPage = 10; 
    //         $token = JWTAuth::getToken();
    //          $user = JWTAuth::toUser($token);
    //         $customerId = (isset($user) && !empty($user)) ? $user->id : '';

    //         // Define the validation rules
    //         $validationRules = [
    //             'page' => 'required',
    //         ];

    //         // Validate the input data
    //         $validation = Validator::make($request->all(), $validationRules, [
    //             'page.required' => 'page is required.',
    //         ]);

    //         // Check for validation errors and return error response if any
    //         if ($validation->fails()) {
    //             return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
    //         }



    //         // Query to retrieve items
    //         $items = FavoriteDoctors::select('*')->where('user_id',$customerId)->orderBy($orderColumn, $orderBy)->paginate($perPage, ['*'], 'page', $page);

    //         // Modify each item in the collection
    //         $items->getCollection()->transform(function ($item) use($customerId) {
    //              $doctorData = User::find($item->doctor_id);
    //              $specialityData = Speciality::find($doctorData->speciality_id);

    //              $rateReviewData = RateReview::where(array('user_id' => $customerId , 'doctor_id' => $item->doctor_id , 'type' => 'doctor'))->first();

    //           // $doctorData->rating =  $existingReviewItemData->rating;

    //             $doctorData->rating = ($rateReviewData !== null) ? $rateReviewData->rating : "0";
    //             $doctorImage = $doctorData->image;
    //              if(!empty($doctorImage)){
    //                 $doctorData->image =  (env('APP_ENV') == 'local') ? asset('storage/users/' . $doctorImage) : asset('storage/app/public/users/' . $doctorImage);  
    //             }

    //             $favoriteDoctorData = FavoriteDoctors::where(array('doctor_id' => $item->doctor_id , 'user_id' =>  $customerId))->first();


    //             $doctorData->is_favorite = ($favoriteDoctorData !== null) ? 1 : 0;

    //              $doctorData->speciality_name =  $specialityData->name;

    //              $doctorData->name = "Dr. ". $doctorData->name;

    //              return $doctorData;

    //         });

    // dd($items->items());
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

    //         return response()->json($responseData);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
    //     }
    // }





    // public function getTopFavoriteDoctors()
    // {
    //     try {
    //         // Get top 10 doctors with most favorites
    //         $topDoctors = FavoriteDoctors::select(
    //             'users.id as doctor_id',
    //             'users.name',
    //             'users.email',
    //             'users.mobile_number',

    //             'users.image',
    //             'users.consultation_fee',
    //             'users.experience',
    //             'users.qualification',
    //             'users.state',
    //             'users.city',
    //             DB::raw('COUNT(favorite_doctors.doctor_id) as total_favorites')
    //         )
    //             ->join('users', 'users.id', '=', 'favorite_doctors.doctor_id')
    //             ->where('users.role_id', 3) // Only doctors
    //             ->where('users.status', 1)  // Only active doctors
    //             ->groupBy(
    //                 'users.id',
    //                 'users.name',
    //                 'users.email',
    //                 'users.mobile_number',

    //                 'users.image',
    //                 'users.consultation_fee',
    //                 'users.experience',
    //                 'users.qualification',
    //                 'users.state',
    //                 'users.city'
    //             )
    //             ->orderBy('total_favorites', 'desc') // Most favorites first
    //             ->limit(10) // Top 10 only
    //             ->get();

    //         // Transform the collection to add additional information
    //         $topDoctors->transform(function ($doctor) {
    //             // Add "Dr." prefix to name (only if not already there)
    //             if (strpos($doctor->name, 'Dr. ') !== 0) {
    //                 $doctor->name = $doctor->name;
    //             }

    //             // Set doctor image
    //             if (!empty($doctor->image)) {
    //                 $doctor->image = (env('APP_ENV') === 'local')
    //                     ? asset('storage/users/' . $doctor->image)
    //                     : asset('storage/app/public/users/' . $doctor->image);
    //             } else {
    //                 $assetPrefixPath = (env('APP_ENV') === 'local') ? '' : 'public';
    //                 $doctor->image = asset($assetPrefixPath . '/assets/admin/img/160x160/img1.jpg');
    //             }






    //             // State mapping
    //             $states = [
    //                 'andhra_pradesh' => 'Andhra Pradesh',
    //                 'arunachal_pradesh' => 'Arunachal Pradesh',
    //                 'assam' => 'Assam',
    //                 'bihar' => 'Bihar',
    //                 'chhattisgarh' => 'Chhattisgarh',
    //                 'delhi' => 'Delhi',
    //                 'goa' => 'Goa',
    //                 'gujarat' => 'Gujarat',
    //                 'haryana' => 'Haryana',
    //                 'himachal_pradesh' => 'Himachal Pradesh',
    //                 'jammu_and_kashmir' => 'Jammu and Kashmir',
    //                 'jharkhand' => 'Jharkhand',
    //                 'karnataka' => 'Karnataka',
    //                 'kerala' => 'Kerala',
    //                 'madhya_pradesh' => 'Madhya Pradesh',
    //                 'maharashtra' => 'Maharashtra',
    //                 'odisha' => 'Odisha',
    //                 'punjab' => 'Punjab',
    //                 'rajasthan' => 'Rajasthan',
    //                 'tamil_nadu' => 'Tamil Nadu',
    //                 'telangana' => 'Telangana',
    //                 'uttar_pradesh' => 'Uttar Pradesh',
    //                 'west_bengal' => 'West Bengal',
    //             ];

    //             $doctor->state = $doctor->state
    //                 ? ($states[$doctor->state] ?? null)
    //                 : null;

    //             // Ensure total_favorites is included in the response
    //             $doctor->total_favorites = (int) $doctor->total_favorites;

    //             return $doctor;
    //         });

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Top 10 favorite doctors found.',
    //             'data' => $topDoctors,
    //         ], 200);

    //     } catch (\Exception $e) {
    //         Log::error('getTopFavoriteDoctors API Error', [
    //             'error' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //         ]);

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //         ], 500);
    //     }
    // }
    
      public function getTopFavoriteDoctors()
    {
        try {
            // Get user from JWT Token
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $customerId = (isset($user) && !empty($user)) ? $user->id : '';

            // Get top 10 doctors with most favorites
            $topDoctors = FavoriteDoctors::select(
                'users.id as doctor_id',
                'users.name',
                'users.email',
                'users.mobile_number',

                'users.image',
                'users.consultation_fee',
                'users.experience',
                'users.qualification',
                'users.state',
                'users.city',
                DB::raw('COUNT(favorite_doctors.doctor_id) as total_favorites')
            )
                ->join('users', 'users.id', '=', 'favorite_doctors.doctor_id')
                ->where('favorite_doctors.user_id', $customerId)
                ->where('users.role_id', 3) // Only doctors
                ->where('users.status', 1)  // Only active doctors
                ->groupBy(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.mobile_number',

                    'users.image',
                    'users.consultation_fee',
                    'users.experience',
                    'users.qualification',
                    'users.state',
                    'users.city'
                )
                ->orderBy('total_favorites', 'desc') // Most favorites first
                ->limit(10) // Top 10 only
                ->get();

            // Transform the collection to add additional information
            $topDoctors->transform(function ($doctor) {
                // Add "Dr." prefix to name (only if not already there)
                if (strpos($doctor->name, 'Dr. ') !== 0) {
                    $doctor->name = $doctor->name;
                }

                // Set doctor image
                if (!empty($doctor->image)) {
                    $doctor->image = (env('APP_ENV') === 'local')
                        ? asset('storage/users/' . $doctor->image)
                        : asset('storage/app/public/users/' . $doctor->image);
                } else {
                    $assetPrefixPath = (env('APP_ENV') === 'local') ? '' : 'public';
                    $doctor->image = asset($assetPrefixPath . '/assets/admin/img/160x160/img1.jpg');
                }






                // State mapping
                $states = [
                    'andhra_pradesh' => 'Andhra Pradesh',
                    'arunachal_pradesh' => 'Arunachal Pradesh',
                    'assam' => 'Assam',
                    'bihar' => 'Bihar',
                    'chhattisgarh' => 'Chhattisgarh',
                    'delhi' => 'Delhi',
                    'goa' => 'Goa',
                    'gujarat' => 'Gujarat',
                    'haryana' => 'Haryana',
                    'himachal_pradesh' => 'Himachal Pradesh',
                    'jammu_and_kashmir' => 'Jammu and Kashmir',
                    'jharkhand' => 'Jharkhand',
                    'karnataka' => 'Karnataka',
                    'kerala' => 'Kerala',
                    'madhya_pradesh' => 'Madhya Pradesh',
                    'maharashtra' => 'Maharashtra',
                    'odisha' => 'Odisha',
                    'punjab' => 'Punjab',
                    'rajasthan' => 'Rajasthan',
                    'tamil_nadu' => 'Tamil Nadu',
                    'telangana' => 'Telangana',
                    'uttar_pradesh' => 'Uttar Pradesh',
                    'west_bengal' => 'West Bengal',
                ];

                $doctor->state = $doctor->state
                    ? ($states[$doctor->state] ?? null)
                    : null;

                // Ensure total_favorites is included in the response
                $doctor->total_favorites = (int) $doctor->total_favorites;

                return $doctor;
            });

            return response()->json([
                'status' => true,
                'message' => 'Top 10 favorite doctors found.',
                'data' => $topDoctors,
            ], 200);

        } catch (\Exception $e) {
            Log::error('getTopFavoriteDoctors API Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }





    /**
     * It will give rate and review to doctor / item.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    public function giveRateReview(Request $request)
    {
        try {
            // Get requested data

            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $customerId = (isset($user) && !empty($user)) ? $user->id : '';
            $doctorId = $request->input('doctor_id');
            $itemId = $request->input('item_id');
            $rating = $request->input('rating');
            $review = $request->input('review');
            $type = $request->input('type');

            // Define the validation rules
            if ($type == 'doctor') {
                $validationRules = [
                    'doctor_id' => 'required',
                    'rating' => 'required', // assuming rating is numeric
                    'review' => 'required',
                    'type' => 'required',
                ];
                // Validate the input data
                $validation = Validator::make($request->all(), $validationRules, [
                    'doctor_id.required' => 'Doctor id is required.',
                    'rating.required' => 'Rating is required.',
                    'review.required' => 'Review is required.',
                    'type.required' => 'Type is required.',
                ]);
            } else {
                $validationRules = [
                    'item_id' => 'required',
                    'rating' => 'required',
                    'type' => 'required',
                ];
                // Validate the input data
                $validation = Validator::make($request->all(), $validationRules, [
                    'doctor_id.required' => 'Doctor id is required.',
                    'rating.required' => 'Rating is required.',
                    'type.required' => 'Type is required.',
                ]);

            }
            // Check for validation errors and return error response if any
            if ($validation->fails()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
            }

            if ($rating > 5) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => 'The rating must not be greater than 5']);
            }




            if ($type == 'doctor') {

                $existingReviewItem = RateReview::where(array('user_id' => $customerId, 'doctor_id' => $doctorId, 'type' => $type))->first();

                if ($existingReviewItem) {
                    $existingReviewItem->rating = $rating;
                    $existingReviewItem->review = $review;
                    $existingReviewItem->save();

                } else {
                    $result = RateReview::create([
                        'doctor_id' => $doctorId,
                        'type' => $type,
                        'user_id' => $customerId,
                        'rating' => $rating,
                        'review' => $review
                    ]);


                }


                $existingReviewItem = RateReview::where(array('user_id' => $customerId, 'doctor_id' => $doctorId, 'type' => $type))->first();

                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Rate and review has been given successfully', 'data' => $existingReviewItem]);


            } else {
                $existingReviewItem = RateReview::where(array('user_id' => $customerId, 'doctor_id' => $doctorId, 'type' => $type))->first();

                if ($existingReviewItem) {
                    $existingReviewItem->rating = $rating;
                    $existingReviewItem->review = $review;
                    $existingReviewItem->save();

                } else {
                    RateReview::create([
                        'item_id' => $itemId,
                        'type' => $type,
                        'user_id' => $customerId,
                        'rating' => $rating,
                    ]);

                }




                $existingReviewItem = RateReview::where(array('user_id' => $customerId, 'item_id' => $itemId, 'type' => $type))->first();

                return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Rate and review has been given successfully', 'data' => $existingReviewItem]);
            }







        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function getOnlineAdviser()
    {
        try {

            $users = User::where('role_id', 3)
                ->where('online_status', 1)
                ->select('id', 'name', 'email', 'mobile_number', 'online_status', 'image')
                ->get();

            // Image URL set karna (collection ke liye loop)
            $users->transform(function ($user) {

                if (!empty($user->image)) {
                    $user->image = (env('APP_ENV') === 'local')
                        ? asset('storage/users/' . $user->image)
                        : asset('storage/app/public/users/' . $user->image);
                } else {
                    $user->image = null;
                }

                return $user;
            });

            return response()->json([
                'status' => true,
                'total_online_users' => $users->count(),
                'data' => $users
            ]);

        } catch (\Exception $e) {

            Log::error('getOnlineAdviser API Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }





}
