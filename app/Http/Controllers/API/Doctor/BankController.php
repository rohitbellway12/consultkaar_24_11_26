<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers; // Add this line
use Illuminate\Validation\Rule;


class BankController extends Controller
{
    public function manageBankDetails(Request $request)
{
    try {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $doctorId = $user ? $user->id : null;

        if (!$doctorId) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthorized user.'
            ], 401);
        }

        $bankDetails = Bank::where('doctor_id', $doctorId)->first();

        $validationRules = [
            'account_number' => [
                $bankDetails ? 'nullable' : 'required',
                'digits_between:9,18',
                Rule::unique('doctor_banks', 'account_number')->ignore($bankDetails?->id),
            ],
            'ifsc_code' => [
                $bankDetails ? 'nullable' : 'required',
                'regex:/^[A-Za-z]{4}\d{7}$/',
            ],
            'account_holder_name' => $bankDetails ? 'nullable' : 'required',
            'bank_name' => $bankDetails ? 'nullable' : 'required',
            'account_proof_file' => $bankDetails ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120' : 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        $validation = Validator::make($request->all(), $validationRules, [
            'account_number.required' => 'Please enter the account number.',
            'account_number.digits_between' => 'Account number must be between 9 and 18 digits.',
            'account_number.unique' => 'The account number has already been taken.',
            'ifsc_code.required' => 'Please enter the IFSC code.',
            'ifsc_code.regex' => 'Invalid IFSC code format.',
            'bank_name.required' => 'Please enter the bank name.',
            'account_holder_name.required' => 'Please enter the account holder name.',
            'account_proof_file.required' => 'Please upload account proof file.',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => $validation->errors()->first()
            ], 422);
        }

        $data = [];

        if ($request->filled('account_number')) {
            $data['account_number'] = $request->account_number;
        }

        if ($request->filled('ifsc_code')) {
            $data['ifsc_code'] = strtoupper($request->ifsc_code);
        }

        if ($request->filled('bank_name')) {
            $data['bank_name'] = $request->bank_name;
        }

        if ($request->filled('account_holder_name')) {
            $data['account_holder_name'] = $request->account_holder_name;
        }

        if ($request->hasFile('account_proof_file')) {
            $data['account_proof_file'] = Helpers::upload(
                'bank_documents/',
                'png',
                $request->file('account_proof_file')
            );
        }

        if ($request->filled('upi_id')) {
            $data['upi_id'] = $request->upi_id;
        }

        if ($bankDetails) {
            if (empty($data)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 422,
                    'message' => 'Please provide at least one field to update.'
                ], 422);
            }

            $bankDetails->update($data);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Bank details updated successfully',
                'data' => $bankDetails->fresh()
            ]);
        }

        $data['doctor_id'] = $doctorId;

        $bankDetails = Bank::create($data);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Bank details added successfully',
            'data' => $bankDetails
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'code' => 500,
            'message' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }
}
   
// public function manageBankDetails(Request $request)
// {
//     try {
//         $token = JWTAuth::getToken();
//         $user = JWTAuth::toUser($token);
//         $doctorId = $user ? $user->id : null;

//         if (!$doctorId) {
//             return response()->json([
//                 'status' => 'error',
//                 'code' => 401,
//                 'message' => 'Unauthorized user.'
//             ], 401);
//         }

//         $bankDetails = Bank::where('doctor_id', $doctorId)->first();

//         $validationRules = [
//             'account_number' => [
//                 $bankDetails ? 'nullable' : 'required',
//                 'digits_between:9,18',
//                 Rule::unique('doctor_banks', 'account_number')->ignore($bankDetails?->id),
//             ],
//             'ifsc_code' => [
//                 $bankDetails ? 'nullable' : 'required',
//                 'regex:/^[A-Za-z]{4}\d{7}$/',
//             ],
//             'account_holder_name' => $bankDetails ? 'nullable' : 'required',
//             'bank_name' => $bankDetails ? 'nullable' : 'required',
//             'account_proof_file' => $bankDetails ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120' : 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
//         ];

//         $validation = Validator::make($request->all(), $validationRules, [
//             'account_number.required' => 'Please enter the account number.',
//             'account_number.digits_between' => 'Account number must be between 9 and 18 digits.',
//             'account_number.unique' => 'The account number has already been taken.',
//             'ifsc_code.required' => 'Please enter the IFSC code.',
//             'ifsc_code.regex' => 'Invalid IFSC code format.',
//             'bank_name.required' => 'Please enter the bank name.',
//             'account_holder_name.required' => 'Please enter the account holder name.',
//             'account_proof_file.required' => 'Please upload account proof file.',
//         ]);

//         if ($validation->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'code' => 422,
//                 'message' => $validation->errors()->first()
//             ], 422);
//         }

//         $data = [];

//         if ($request->filled('account_number')) {
//             $data['account_number'] = $request->account_number;
//         }

//         if ($request->filled('ifsc_code')) {
//             $data['ifsc_code'] = strtoupper($request->ifsc_code);
//         }

//         if ($request->filled('bank_name')) {
//             $data['bank_name'] = $request->bank_name;
//         }

//         if ($request->filled('account_holder_name')) {
//             $data['account_holder_name'] = $request->account_holder_name;
//         }

//         if ($request->hasFile('account_proof_file')) {
//             $data['account_proof_file'] = Helpers::upload(
//                 'bank_documents/',
//                 'png',
//                 $request->file('account_proof_file')
//             );
//         }

//         if ($bankDetails) {
//             if (empty($data)) {
//                 return response()->json([
//                     'status' => 'error',
//                     'code' => 422,
//                     'message' => 'Please provide at least one field to update.'
//                 ], 422);
//             }

//             $bankDetails->update($data);

//             return response()->json([
//                 'status' => 'success',
//                 'code' => 200,
//                 'message' => 'Bank details updated successfully',
//                 'data' => $bankDetails->fresh()
//             ]);
//         }

//         $data['doctor_id'] = $doctorId;

//         $bankDetails = Bank::create($data);

//         return response()->json([
//             'status' => 'success',
//             'code' => 200,
//             'message' => 'Bank details added successfully',
//             'data' => $bankDetails
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'code' => 500,
//             'message' => $e->getMessage(),
//             'line' => $e->getLine()
//         ], 500);
//     }
// }
    
    
    

      /**
     * Get bank details by doctor_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBankDetail(Request $request)
    {
        try {
            // Get customer_id from the request
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $doctorId = (isset($user) && !empty($user)) ? $user->id : '';

            // Find the astrologer's bank details by doctor_id
            $bankDetails = Bank::where('doctor_id', $doctorId)->first();
            
            

            if ($bankDetails) {
                
                $bankData = [
                    'account_number' => $bankDetails->account_number,
                    'ifsc_code' => $bankDetails->ifsc_code,
                    'bank_name' => $bankDetails->bank_name,
                    'account_holder_name' => $bankDetails->account_holder_name,
                    'account_proof_file' => asset('storage/app/public/bank_documents/'.$bankDetails->account_proof_file),
                        'upi_id' => $bankDetails->upi_id,
                ];
                
                return response()->json(['status' => 'success', 'code' => 200,  'message' => 'Data found', 'data' => $bankData]);
            } else {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'No data found'], 404);
            }
        } catch (\Exception $e) {
            // Handle exceptions, if any
            return response()->json(['status' => 'error', 'code' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
