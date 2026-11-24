<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;



class VendorController extends Controller
{
    //

     /**
     * It will get items .
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    
   
    
 public function getVendorList(Request $request)
{
    // role_id = 4 wale vendors fetch karo
    $vendors = User::where('role_id', 4)->get();

    // Modify image path for each vendor
    $vendors->transform(function ($vendor) {
        if (!empty($vendor->image)) {
            $vendor->image = (env('APP_ENV') === 'local')
                ? asset('storage/user/' . $vendor->image)
                : asset('storage/app/public/' . $vendor->image);
        } else {
            $vendor->image = ''; // default empty string if image not set
        }
        return $vendor;
    });

    // API response with status code 200
    return response()->json([
        'status' => 'success',
        'code' => 200,
        'message' => 'Vendor list fetched successfully.',
        'data' => $vendors
    ], 200);
}



}
