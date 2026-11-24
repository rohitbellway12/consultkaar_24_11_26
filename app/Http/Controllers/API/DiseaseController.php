<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Speciality;
use App\Models\Domain;
use App\Models\User;
use App\Models\Lab;
use App\Models\LabTest;
use App\Models\Disease;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\CallRequest;

class DiseaseController extends Controller
{
    //

    /**
     * get specialities
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    // public function index(Request $request){
    //      try {            

    //           $diseases   = Disease::where(['status' => 1])->get();
    //          if (count($diseases) > 0) {
    //              foreach($diseases as $key => $value){
    //                 $diseaseImagePath = (env('APP_ENV') == 'local') ? asset('storage/disease/' . $value->image) : asset('storage/app/public/disease/' . $value->image); 
    //                 $value->image = $diseaseImagePath;
    //                 $speciality_data = Speciality::find($value->speciality_id);
    //                 $speciality_name = ($speciality_data) ? $speciality_data->name : 'N/A';
    //                 $value->speciality_name = $speciality_name;
    //              } 
    //              return response()->json(['status' => 'success', 'code' => 200,'data' => $diseases, 'message' => 'Data found']);
    //          } else {
    //              return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found'], 200);
    //          }
    //      } catch (\Exception $e) {
    //          // Handle exceptions, if any
    //          return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
    //      }
    // }


    public function index(Request $request)
    {
        try {

            $diseases = Domain::get();
            if (count($diseases) > 0) {
                foreach ($diseases as $key => $value) {
                    $diseaseImagePath = (env('APP_ENV') == 'local') ? asset('storage/disease/' . $value->image) : asset('storage/app/public/disease/' . $value->image);
                    $value->image = $diseaseImagePath;
                    $speciality_data = Speciality::find($value->speciality_id);
                    $speciality_name = ($speciality_data) ? $speciality_data->name : 'N/A';
                    $value->speciality_name = $speciality_name;
                }
                return response()->json(['status' => 'success', 'code' => 200, 'data' => $diseases, 'message' => 'Data found']);
            } else {
                return response()->json(['status' => 'error', 'code' => 200, 'message' => 'No data found'], 200);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $type = $request->input('type');

            $diseases = collect();
            $doctors = collect();
            $labs = collect();

            if ($type === 'disease') {
                $diseaseQuery = Disease::where('status', 1);
                if ($search) {
                    $diseaseQuery->where('name', 'LIKE', '%' . $search . '%');
                }
                $diseases = $diseaseQuery->get();

            } elseif ($type === 'doctor') {
                $doctorQuery = User::where('role_id', 3)->where('status', 1);
                if ($search) {
                    $doctorQuery->where('name', 'LIKE', '%' . $search . '%');
                }
                $doctors = $doctorQuery->orderBy('online_status', 'desc')->get();

            } elseif ($type === 'lab') {
                $labQuery = Lab::query();
                if ($search) {
                    $labQuery->where('name', 'LIKE', '%' . $search . '%');
                }
                $labs = $labQuery->get()->map(function ($lab) {
                    // Set lab image
                    $lab->image = !empty($lab->image)
                        ? ((env('APP_ENV') === 'local')
                            ? asset('storage/labs/' . $lab->image)
                            : asset('storage/app/public/labs/' . $lab->image))
                        : asset((env('APP_ENV') === 'local') ? 'assets/admin/img/160x160/img1.jpg' : 'public/assets/admin/img/160x160/img1.jpg');

                    // Get lab tests
                    $labTests = LabTest::where('lab_id', $lab->id)->get()->map(function ($test) {
                        $test->image = !empty($test->image)
                            ? ((env('APP_ENV') === 'local')
                                ? asset('storage/' . str_replace('public/', '', $test->image))
                                : asset('storage/app/public/public/' . str_replace('public/', '', $test->image)))
                            : asset((env('APP_ENV') === 'local') ? 'assets/admin/img/160x160/img1.jpg' : 'public/assets/admin/img/160x160/img1.jpg');
                        return $test;
                    });

                    // Attach tests to lab
                    $lab->lab_tests = $labTests;
                    return $lab;
                });

            } else {
                if ($search) {
                    $diseases = Disease::where('status', 1)
                        ->where('name', 'LIKE', '%' . $search . '%')
                        ->get();

                    $doctors = User::where('role_id', 3)
                        ->where('status', 1)
                        ->where('isregister', 1)
                        ->where('name', 'LIKE', '%' . $search . '%')
                        ->orderBy('online_status', 'desc')
                        ->get();

                    $labs = Lab::where('name', 'LIKE', '%' . $search . '%')
                        ->get()->map(function ($lab) {
                            $lab->image = !empty($lab->image)
                                ? ((env('APP_ENV') === 'local')
                                    ? asset('storage/labs/' . $lab->image)
                                    : asset('storage/app/public/labs/' . $lab->image))
                                : asset((env('APP_ENV') === 'local') ? 'assets/admin/img/160x160/img1.jpg' : 'public/assets/admin/img/160x160/img1.jpg');

                            $lab->lab_tests = LabTest::where('lab_id', $lab->id)->get()->map(function ($test) {
                                $test->image = !empty($test->image)
                                    ? ((env('APP_ENV') === 'local')
                                        ? asset('storage/' . str_replace('public/', '', $test->image))
                                        : asset('storage/app/public/public/' . str_replace('public/', '', $test->image)))
                                    : asset((env('APP_ENV') === 'local') ? 'assets/admin/img/160x160/img1.jpg' : 'public/assets/admin/img/160x160/img1.jpg');
                                return $test;
                            });

                            return $lab;
                        });
                }
            }

            $doctors = $doctors->map(function ($user) {
                $user->image = !empty($user->image)
                    ? ((env('APP_ENV') === 'local')
                        ? asset('storage/users/' . $user->image)
                        : asset('storage/app/public/users/' . $user->image))
                    : asset((env('APP_ENV') === 'local') ? 'assets/admin/img/160x160/img1.jpg' : 'public/assets/admin/img/160x160/img1.jpg');

                $speciality = \App\Models\Speciality::find($user->speciality_id);
                $user->speciality_name = $speciality ? $speciality->name : null;


                $completedCalls = CallRequest::where('doctor_id', $user->id)->where('call_status', 'complete')->count();
                $user->consultation_count = $completedCalls;
                $user->consultation_count_complete = $completedCalls;

                return $user;
            });

            $diseases = $diseases->map(function ($disease) {
                $disease->image = !empty($disease->image)
                    ? ((env('APP_ENV') === 'local')
                        ? asset('storage/disease/' . $disease->image)
                        : asset('storage/app/public/disease/' . $disease->image))
                    : asset((env('APP_ENV') === 'local') ? 'assets/admin/img/160x160/img1.jpg' : 'public/assets/admin/img/160x160/img1.jpg');
                return $disease;
            });

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'diseases' => $diseases,
                    'doctors' => $doctors,
                    'labs' => $labs,
                ],
                'message' => 'Data found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }




}
