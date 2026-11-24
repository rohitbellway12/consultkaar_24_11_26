<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\UsersAddress;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersAddressController extends Controller
{
    //


    /**
     * Add / update delivery address by user_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    
     public function manageAddress(Request $request)
     {
         try {
             // Get requested data
             $token = JWTAuth::getToken();
             $user = JWTAuth::toUser($token);
             $userId = (isset($user) && !empty($user)) ? $user->id : '';
             $addressId = $request->input('address_id'); // Assume you pass an 'address_id' for updating
     
             // Define the validation rules
             $validationRules = [
                 'house_name' => 'required',
                 'zip_code' => 'required',
                 'floor_number' => 'required',
                 'address_type' => 'required',
             ];
     
             // If it's an update (address_id is provided), validate and update the existing address
             if ($addressId) {
                 $validationRules['address_id'] = 'required|exists:users_addresses,id,user_id,' . $userId;
             }
     
             // Validate the input data
             $validation = Validator::make($request->all(), $validationRules, [
                 'house_name.required' => 'Please enter a house name.',
                 'zip_code.required' => 'Please enter a zip code.',
                 'floor_number.required' => 'Please enter a floor number.',
                 'address_id.required' => 'Address ID is required for updates.',
                 'address_id.exists' => 'The provided address ID does not exist for this customer.',
             ]);
     
             // Check for validation errors and return error response if any
             if ($validation->fails()) {
                 return response()->json(['status' => 'error', 'code' => 422, 'message' => $validation->errors()->first()]);
             }

             if (!in_array($request->address_type,array('Home','Work','Other'))) {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Address type should be either Home,Work or Other']);
            }

             
     
             // Create or update the address based on whether 'address_id' is provided
             if ($addressId) {
                 // Update an existing address
     

                 $addressRecord = UsersAddress::where([
                    'user_id' => $userId,
                    'address_type' => $request->address_type
                ])->whereNotIn('id', [$addressId])->get();
     
                 if (count($addressRecord) > 0) {
                    return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Address already exist']);
                 } else {
                    $address = UsersAddress::where(array('user_id' => $userId))->find($addressId);

                    $address->update([
                        'house_name' => $request->house_name,
                        'floor_number' => $request->floor_number,
                        'landmark' => $request->landmark,
                        'area_name' => $request->area_name,
                        'zip_code' => $request->zip_code,
                        'address_type' => $request->address_type,
                        'user_id' => $userId
                    ]);

                    $addressData = [
                        'house_name' => $request->house_name,
                        'floor_number' => $request->floor_number,
                        'landmark' => $request->landmark,
                        'zip_code' => $request->zip_code,
                        'area_name' => $request->area_name,
                        'address_type' => $request->address_type,
                        'address_id' =>  $addressId 
                    ];
    
                    return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Address updated successfully','data' => $addressData]);

                 }




                     
                 
             } else {
                 
                 $addressRecord = UsersAddress::where(array('user_id' => $userId,'address_type' => $request->address_type));
     
                 if ($addressRecord->exists()) {
                    return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Address already exist']);
                 } else {
                     // Add a new address
                     UsersAddress::create([
                         'house_name' => $request->house_name,
                         'floor_number' => $request->floor_number,
                         'landmark' => $request->landmark,
                         'zip_code' => $request->zip_code,
                         'area_name' => $request->area_name,
                         'address_type' => $request->address_type,
                         'user_id' => $userId
                     ]);

                     $addressData = [
                        'house_name' => $request->house_name,
                        'floor_number' => $request->floor_number,
                        'landmark' => $request->landmark,
                        'zip_code' => $request->zip_code,
                        'area_name' => $request->area_name,
                        'address_type' => $request->address_type,
                    ];
     
                     return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Address added successfully','data' => $addressData]);
                 }
             }
         } catch (\Exception $e) {
             return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
         }
     }
     
     /**
     * get delivery address by user_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    public function getAddress(Request $request)
    {
        try {
            // Get user_id and verification_code from the request
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $userId = $user->id;
           

            // Find the user's address by user_id
            $addressData = UsersAddress::where('user_id', $userId)->get();
            $allAddresses = [];
            if (isset($addressData) && !empty($addressData)) {
               foreach($addressData as $key => $value){
                $data =  [
                    'house_name' => $value->house_name,
                    'floor_number' => (int) $value->floor_number,
                    'landmark' => $value->landmark,
                    'zip_code' => $value->zip_code,
                    'area_name' => $value->area_name,
                    'address_type' => $value->address_type,
                    'address_id' => $value->id
               ];
               array_push($allAddresses,$data);
               } 
               
               if (count($allAddresses) > 0) {
                return response()->json(['status' => 'success', 'code' => 200 , 'message' => 'Data found','data' => $allAddresses]);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found']);
            }
               
               
            } else {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'User has no address'], 404);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }


}

