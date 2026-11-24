<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Specialization;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class RegistrationController extends Controller
{
    /**
     * Get all domains for dropdown
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDomains()
    {
        try {
            $domains = Domain::select('id', 'name')->get();
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Domains fetched successfully',
                'data' => $domains
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to fetch domains'
            ]);
        }
    }

    /**
     * Get specializations based on domain_id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecializations(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'domain_id' => 'required|exists:domains,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => $validator->errors()->first()
                ]);
            }

            $specializations = Specialization::where('domain_id', $request->domain_id)
                ->select('id', 'name')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Specializations fetched successfully',
                'data' => $specializations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to fetch specializations'
            ]);
        }
    }

    /**
     * Get all countries for dropdown
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountries()
    {
        try {
            $countries = Country::select('id', 'name', 'code')->get();
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Countries fetched successfully',
                'data' => $countries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to fetch countries'
            ]);
        }
    }

    /**
     * Get states based on country_id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStates(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'country_id' => 'required|exists:countries,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => $validator->errors()->first()
                ]);
            }

            $states = State::where('country_id', $request->country_id)
                ->select('id', 'name')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'States fetched successfully',
                'data' => $states
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to fetch states'
            ]);
        }
    }

    /**
     * Get cities based on state_id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'state_id' => 'required|exists:states,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => $validator->errors()->first()
                ]);
            }

            $cities = City::where('state_id', $request->state_id)
                ->select('id', 'name')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Cities fetched successfully',
                'data' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to fetch cities'
            ]);
        }
    }
}