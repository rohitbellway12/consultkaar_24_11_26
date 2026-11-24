<?php

namespace App\Http\Controllers\API\Doctor;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FavoriteDoctors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; // Assuming you're using JWTAuth for authentication
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;






class DoctorController extends Controller
{
    

    /**
     * It will set doctor avialilablity.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    
     public function setDoctorAvailablity(Request $request){
        try {
             // Get requested data
             
             $token = JWTAuth::getToken();
             $user = JWTAuth::toUser($token);
             $doctorId = (isset($user) && !empty($user)) ? $user->id : '';

             $availabilityStatus = $request->input('availability_status'); 

             // Define the validation rules
                $validationRules = [
                    'availability_status' => 'required|in:Available,Busy,Offline',
                ];
                 // Validate the input data
                $validation = Validator::make($request->all(), $validationRules, [
                    'availability_status.required' => 'Availability status is required.',
                    'availability_status.in' => 'Availability status should be Available,Busy,Offline.'
                ]);

             
                // Check for validation errors and return error response if any
                 if ($validation->fails()) {
                     return response()->json(['status' => false, 'message' => $validation->errors()->first()]);
                 }

 
                $userData = User::find($doctorId);
                $userData->availability_status = $availabilityStatus;
                $userData->save();
               
                return response()->json(['status' => true, 'message' => 'Status changed successfully']);
        
         } catch (\Exception $e) {
             return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }




    

    
    public function getIdentityProofs()
    {
        try {
            $proofs = \App\Models\IdentityProof::where('status', 1)->get();
            return response()->json([
                'status' => true, 
                'message' => 'Identity proofs fetched successfully', 
                'data' => $proofs
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getRelevantDocuments(Request $request)
    {
        try {
            $query = \App\Models\RelevantDocumentType::where('status', 1);
            
            // Filter by domain_id if provided
            if ($request->has('domain_id') && $request->domain_id) {
                $query->where(function($q) use ($request) {
                    $q->where('domain_id', $request->domain_id)
                      ->orWhereNull('domain_id'); // Include general documents too
                });
            }
            
            $documents = $query->get();
            return response()->json([
                'status' => true, 
                'message' => 'Relevant document types fetched successfully', 
                'data' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get doctor's current status and profile completion info
     */
    public function getProfileStatus()
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            
            $statusInfo = [
                'status' => $user->status, // 0=pending, 1=approved, 2=rejected
                'status_text' => $this->getStatusText($user->status),
                'is_profile_completed' => !empty($user->name) && !empty($user->domain_id) && !empty($user->speciality_id),
                'is_active' => $user->is_active,
                'rejection_reason' => $user->status == 2 ? $user->rejection_reason : null,
                'can_access_dashboard' => $user->status == 1 && $user->is_active == 1,
            ];
            
            return response()->json([
                'status' => true,
                'message' => 'Profile status fetched successfully',
                'data' => $statusInfo
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Pending Approval';
            case 1:
                return 'Approved';
            case 2:
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }
    
    public function doctorFavoriteCount($doctor_id)
   {
    try {

        $doctor = User::where('id', $doctor_id)
                      ->where('role_id', 3)
                      ->first();

        if (!$doctor) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        $favoriteCount = FavoriteDoctors::where('doctor_id', $doctor_id)->count();

        return response()->json([
            'status' => true,
            'doctor_id' => $doctor_id,
            'total_favorites' => $favoriteCount
        ]);

    } catch (\Exception $e) {
        Log::error('doctorFavoriteCount API Error', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong'
        ], 500);
    }
   }
   
   
   
// public function getTopFavoriteDoctors()
// {
//     try {
//         // Get top 10 doctors with most favorites
//         $topDoctors = FavoriteDoctors::select(
//                 'users.id as doctor_id',
//                 'users.name',
//                 'users.email',
//                 'users.mobile_number',
               
//                 'users.image',
//                 'users.consultation_fee',
//                 'users.experience',
//                 'users.qualification',
//                 'users.state',
//                 'users.city',
//                 DB::raw('COUNT(favorite_doctors.doctor_id) as total_favorites')
//             )
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
//                 'andhra_pradesh'       => 'Andhra Pradesh',
//                 'arunachal_pradesh'    => 'Arunachal Pradesh',
//                 'assam'                => 'Assam',
//                 'bihar'                => 'Bihar',
//                 'chhattisgarh'         => 'Chhattisgarh',
//                 'delhi'                => 'Delhi',
//                 'goa'                  => 'Goa',
//                 'gujarat'              => 'Gujarat',
//                 'haryana'              => 'Haryana',
//                 'himachal_pradesh'     => 'Himachal Pradesh',
//                 'jammu_and_kashmir'    => 'Jammu and Kashmir',
//                 'jharkhand'            => 'Jharkhand',
//                 'karnataka'            => 'Karnataka',
//                 'kerala'               => 'Kerala',
//                 'madhya_pradesh'       => 'Madhya Pradesh',
//                 'maharashtra'          => 'Maharashtra',
//                 'odisha'               => 'Odisha',
//                 'punjab'               => 'Punjab',
//                 'rajasthan'            => 'Rajasthan',
//                 'tamil_nadu'           => 'Tamil Nadu',
//                 'telangana'            => 'Telangana',
//                 'uttar_pradesh'        => 'Uttar Pradesh',
//                 'west_bengal'          => 'West Bengal',
//             ];

//             $doctor->state = $doctor->state
//                 ? ($states[$doctor->state] ?? null)
//                 : null;

//             // Ensure total_favorites is included in the response
//             $doctor->total_favorites = (int) $doctor->total_favorites;

//             return $doctor;
//         });

//         return response()->json([
//             'status'  => true,
//             'message' => 'Top 10 favorite doctors found.',
//             'data'    => $topDoctors,
//         ], 200);

//     } catch (\Exception $e) {
//         Log::error('getTopFavoriteDoctors API Error', [
//             'error' => $e->getMessage(),
//             'file'  => $e->getFile(),
//             'line'  => $e->getLine(),
//         ]);

//         return response()->json([
//             'status'  => false,
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
                'andhra_pradesh'       => 'Andhra Pradesh',
                'arunachal_pradesh'    => 'Arunachal Pradesh',
                'assam'                => 'Assam',
                'bihar'                => 'Bihar',
                'chhattisgarh'         => 'Chhattisgarh',
                'delhi'                => 'Delhi',
                'goa'                  => 'Goa',
                'gujarat'              => 'Gujarat',
                'haryana'              => 'Haryana',
                'himachal_pradesh'     => 'Himachal Pradesh',
                'jammu_and_kashmir'    => 'Jammu and Kashmir',
                'jharkhand'            => 'Jharkhand',
                'karnataka'            => 'Karnataka',
                'kerala'               => 'Kerala',
                'madhya_pradesh'       => 'Madhya Pradesh',
                'maharashtra'          => 'Maharashtra',
                'odisha'               => 'Odisha',
                'punjab'               => 'Punjab',
                'rajasthan'            => 'Rajasthan',
                'tamil_nadu'           => 'Tamil Nadu',
                'telangana'            => 'Telangana',
                'uttar_pradesh'        => 'Uttar Pradesh',
                'west_bengal'          => 'West Bengal',
            ];

            $doctor->state = $doctor->state
                ? ($states[$doctor->state] ?? null)
                : null;

            // Ensure total_favorites is included in the response
            $doctor->total_favorites = (int) $doctor->total_favorites;

            return $doctor;
        });

        return response()->json([
            'status'  => true,
            'message' => 'Top 10 favorite doctors found.',
            'data'    => $topDoctors,
        ], 200);

    } catch (\Exception $e) {
        Log::error('getTopFavoriteDoctors API Error', [
            'error' => $e->getMessage(),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
        ]);

        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong',
        ], 500);
    }
}
   

  

}
