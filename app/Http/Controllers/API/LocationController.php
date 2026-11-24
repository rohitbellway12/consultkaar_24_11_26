<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Location;

class LocationController extends Controller
{   

    public function index(){
        try {
            
            // get location data 
            
            $locations = Location::where('status', 1)->orderBy('id', 'desc')->get();


            if (count($locations) > 0) {
                return response()->json(['status' => 'success', 'code' => 200,'data' => $locations, 'message' => 'Data found']);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found']);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()]);
        }
    }
}
