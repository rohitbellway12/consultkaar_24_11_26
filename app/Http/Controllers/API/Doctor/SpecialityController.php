<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Speciality;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;


class SpecialityController extends Controller
{
    //

     /**
     * get specialities
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    /**
     * Return a limited list of experts (specialities) with domain name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listFive(Request $request)
    {
        try {
            // Get up to 5 specialities with their domain
            $specialities = Speciality::with('domain')
                ->where('status', 1)
                ->limit(5)
                ->get();

            if ($specialities->isNotEmpty()) {
                $specialities->transform(function ($item) {
                    $imagePath = (env('APP_ENV') == 'local')
                        ? asset('storage/speciality/' . $item->image)
                        : asset('storage/app/public/speciality/' . $item->image);
                    $item->image = $imagePath;
                    $item->domain_name = $item->domain ? $item->domain->name : null;
                    unset($item->domain);
                    return $item;
                });
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Data found',
                    'data' => $specialities,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 200,
                    'message' => 'No data found',
                    'data' => [],
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
