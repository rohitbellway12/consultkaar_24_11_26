<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Speciality;
use App\CentralLogics\Helpers;

class DomainController extends Controller
{
    public function getDomains()
    {
        $domains = Domain::all();
        return response()->json(['status' => true, 'message' => 'Domains fetched successfully', 'data' => $domains], 200);
    }

    public function getSpecializations(Request $request)
    {
        $query = Speciality::query();

        if ($request->has('domain_id')) {
            $query->where('domain_id', $request->domain_id);
        }
        
        $specializations = $query->get();
        return response()->json(['status' => true, 'message' => 'Specializations fetched successfully', 'data' => $specializations], 200);
    }
    
    // Placeholder methods for other routes defined in api.php that use this controller
    public function getCountries() {
        return response()->json(['status' => true, 'message' => 'Countries fetched successfully', 'data' => \App\Models\Country::all()]);
    }
    
    public function getStates(Request $request) {
        $query = \App\Models\State::query();
        if($request->has('country_id')){
            $query->where('country_id', $request->country_id);
        }
        $states = $query->get();
        return response()->json(['status' => true, 'message' => 'States fetched successfully', 'data' => $states]);
    }
    
    public function getCities(Request $request) {
        $query = \App\Models\City::query();
        if($request->has('state_id')){
            $query->where('state_id', $request->state_id);
        }
        $cities = $query->get();
        return response()->json(['status' => true, 'message' => 'Cities fetched successfully', 'data' => $cities]);
    }
}
