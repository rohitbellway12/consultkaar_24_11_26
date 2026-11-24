<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Food;
use App\Models\BusinessSetting;
use App\Models\Appointment;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\UserLabTest;
use App\Models\WithDrawRequest;
use PDF;
use DB;
use App\Models\User;
use App\Models\CallRequest;
use Carbon\Carbon;
class AppointmentController extends Controller
{
    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $status = $request->status;
        $search = $request->search;

        $orders = Appointment::query()
            ->with([
                'customer',
                'doctor' => function ($query) {
                    $query->select('id', 'name', 'chat_fee', 'audio_fee', 'video_fee', 'consultation_fee');
                }
            ])
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('id', 'like', "%{$value}%");
                    // Add the condition to search by customer name
                    $q->orWhereHas('customer', function ($subQuery) use ($value) {
                        $subQuery->where('name', 'like', "%{$value}%");
                    });
                }
            })
            ->limit(50)->get();

        // Process data for search results
        foreach ($orders as $order) {
            $this->calculateAppointmentFees($order);
        }

        return response()->json([
            'view' => view('admin-views.appointment.partials._table', compact('orders'))->render()
        ]);
    }

    public function appointmentfilter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);
        session()->put('appointment_filter', json_encode($request->all()));
        return back();
    }

    public function appointmentfilter_reset(Request $request)
    {
        session()->forget('appointment_filter');
        return back();
    }

    public function view($id)
    {
        $appointment = Appointment::with(['customer', 'doctor'])->findOrFail($id);
        $this->calculateAppointmentFees($appointment);
        return view('admin.appointment.appointment-view', compact('appointment'));
    }

    public function generateInvoiceForLabTest($id)
    {
        $order = UserLabTest::where('id', $id)->first();
        return view('admin-views.appointment.lab-test-invoice', compact('order'));
    }

    public function restaurnt_filter($id)
    {
        session()->put('restaurnt_filter', $id);
        return back();
    }

    public function filter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);
        session()->put('order_filter', json_encode($request->all()));
        return back();
    }

    public function filter_reset(Request $request)
    {
        session()->forget('order_filter');
        return back();
    }

    function downloadInvoiceForLabTest(Request $request, $orderId)
    {
        $order = UserLabTest::where('id', $orderId)->first();
        if (isset($order)) {
            $businessSettingData = BusinessSetting::where('key', 'logo')->first();
            $businessSettingLogoPath = (isset($businessSettingData) && !empty($businessSettingData)) ? "/storage/app/public/business/" . $businessSettingData->value : '';

            $path = base_path($businessSettingLogoPath);
            ;
            $type = pathinfo($path, PATHINFO_EXTENSION);

            $data = file_get_contents($path);
            $logoPath = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $pdf = PDF::setOptions(['debugKeepTemp' => true, 'defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isPhpEnabled' => true])->loadView('admin-views.appointment.lab-test-order-invoice', compact('order', 'logoPath'));
            return $pdf->download('Invoice' . '# ' . $orderId . '.pdf');
        }
    }

    //  public function list(Request $request, $status)
// {
//     if (!session()->has('zone_filter')) {
//         session()->put('zone_filter', 0);
//     }

    //     $customers = User::where('role_id', 2)->get(['id', 'name']); // Assuming role_id 2 is customer
//     $doctors = User::where('role_id', 3)->get(['id', 'name']); // Assuming role_id 3 is doctor

    //     // Only apply session filters if status is not "all" (REMOVED: Now applies to all)
//     if (session()->has('appointment_filter')) {
//         $request = (object)json_decode(session('appointment_filter'));
//     }

    //     $appointment_type = isset($request->appointment_type) ? $request->appointment_type : null;
//     $from_date = isset($request->from_date) ? $request->from_date : null;
//     $to_date = isset($request->to_date) ? $request->to_date : null;
//     $date_type = isset($request->date_type) ? $request->date_type : null;
//     $customer_id = isset($request->customer_id) ? $request->customer_id : null;
//     $doctor_id = isset($request->doctor_id) ? $request->doctor_id : null;

    //     if ($date_type == 'today') {
//         $from_date = date('Y-m-d');
//         $to_date = date('Y-m-d');
//     } elseif ($date_type == 'this_month') {
//         $from_date = date('Y-m-01');
//         $to_date = date('Y-m-t');
//     }

    //     // Load appointments with doctor relation including fees
//     $orders = Appointment::with([
//         'customer',
//         'doctor' => function ($query) {
//             $query->select(
//                 'id', 'name',
//                 'chat_fee',
//                 'audio_fee',
//                 'video_fee',
//                 'consultation_fee',
//                 'enable_chat',
//                 'enable_audio',
//                 'enable_video'
//             );
//         }
//     ])
//     ->when($status == 'pending', fn($query) => $query->Pending())
//     ->when($status == 'completed', fn($query) => $query->Completed())
//     ->when($status == 'cancelled', fn($query) => $query->Cancelled())
//     ->when($status == 'confirmed', fn($query) => $query->Confirmed())
//     ->when($status == 'rejected', fn($query) => $query->Rejected())
//     // ✅ For "all" status, fetch everything
//     ->when($status == 'all', fn($query) => $query)
//     ->when($appointment_type, function ($query) use ($appointment_type) {
//         return $query->where('appoinmtent_type', $appointment_type);
//     })
//     ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
//         return $query->whereBetween('appoinmtent_date', [
//             $from_date,
//             $to_date
//         ]);
//     })
//     ->when($customer_id, function ($query) use ($customer_id) {
//         return $query->where('user_id', $customer_id);
//     })
//     ->when($doctor_id, function ($query) use ($doctor_id) {
//         return $query->where('doctor_id', $doctor_id);
//     })
//     ->orderBy('id', 'desc')
//     ->paginate(config('default_pagination'));

    //     // Calculate fees for each appointment
//     foreach ($orders as $order) {
//         $this->calculateAppointmentFees($order);
//     }

    //     // Count total appointments for selected status
//     switch ($status) {
//         case 'all':
//             $total = Appointment::count(); // ✅ all doctors, all appointments
//             break;
//         case 'pending':
//             $total = Appointment::Pending()->count();
//             break;
//         case 'completed':
//             $total = Appointment::Completed()->count();
//             break;
//         case 'confirmed':
//             $total = Appointment::Confirmed()->count();
//             break;
//         case 'cancelled':
//             $total = Appointment::Cancelled()->count();
//             break;
//         case 'rejected':
//             $total = Appointment::Rejected()->count();
//             break;
//         default:
//             $total = 0;
//     }

    //     return view('admin-views.appointment.appointment-list', compact(
//         'orders', 'from_date', 'to_date', 'appointment_type', 'total', 'date_type', 'customer_id', 'doctor_id', 'customers', 'doctors'
//     ));
// }
    public function list(Request $request, $status)
    {
        $customers = User::where('role_id', 2)->get(['id', 'name']);
        $doctors = User::where('role_id', 3)->get(['id', 'name']);

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $call_type = $request->call_type;
        $customer_id = $request->customer_id;
        $doctor_id = $request->doctor_id;
        $search = $request->search;
        $date_filter = $request->date_filter;

        $query = CallRequest::with(['user', 'doctor'])

            // Status filter
            ->when($status != 'all', function ($q) use ($status) {
                $q->where('call_status', $status);
            })

            // Call type filter (chat/audio/video)
            ->when($call_type, function ($q) use ($call_type) {
                $q->where('call_type', $call_type);
            })

            // Customer filter
            ->when($customer_id, function ($q) use ($customer_id) {
                $q->where('user_id', $customer_id);
            })

            // Doctor filter
            ->when($doctor_id, function ($q) use ($doctor_id) {
                $q->where('doctor_id', $doctor_id);
            })

            // Search by name
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('user', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%$search%");
                    })
                        ->orWhereHas('doctor', function ($qq) use ($search) {
                            $qq->where('name', 'like', "%$search%");
                        });
                });
            })

            // Daily filter
            ->when($date_filter == 'today', function ($q) {
                $q->whereDate('created_at', Carbon::today());
            })

            // Weekly filter
            ->when($date_filter == 'week', function ($q) {
                $q->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
            })

            // Custom date filter
            ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                $q->whereBetween('created_at', [
                    Carbon::parse($from_date)->startOfDay(),
                    Carbon::parse($to_date)->endOfDay()
                ]);
            });

        $orders = $query->orderBy('id', 'desc')
            ->paginate(config('default_pagination'))
            ->appends($request->all());

        // IMPORTANT: total should use same filtered query
        $total = $query->count();

        return view('admin-views.appointment.appointment-list', compact(
            'orders',
            'total',
            'customers',
            'doctors',
            'from_date',
            'to_date',
            'call_type',
            'customer_id',
            'doctor_id',
            'search',
            'date_filter'
        ));
    }
    /**
     * Helper function to calculate appointment fees
     */
    private function calculateAppointmentFees($appointment)
    {
        $appointmentType = $appointment->appoinmtent_type;

        // ✅ FIXED: Parse duration from TIME format to minutes
        $duration = $this->parseDurationFromTime($appointment->duration);
        $appointment->duration = $duration;

        // Initialize fee variables
        $appointment->chat_fee = 0;
        $appointment->audio_fee = 0;
        $appointment->video_fee = 0;
        $appointment->offline_fee = 0;
        $appointment->calculated_total = 0;
        $appointment->active_fee_type = $appointmentType;

        if ($appointment->doctor) {
            // Get per minute fees from doctor
            $appointment->chat_fee = is_numeric($appointment->doctor->chat_fee) ? (float) $appointment->doctor->chat_fee : 0;
            $appointment->audio_fee = is_numeric($appointment->doctor->audio_fee) ? (float) $appointment->doctor->audio_fee : 0;
            $appointment->video_fee = is_numeric($appointment->doctor->video_fee) ? (float) $appointment->doctor->video_fee : 0;
            $appointment->offline_fee = is_numeric($appointment->doctor->consultation_fee) ? (float) $appointment->doctor->consultation_fee : 0;

            // Calculate total based on appointment type
            if (in_array($appointmentType, ['chat', 'audio', 'video'])) {
                $feeField = $appointmentType . '_fee';
                $feePerMinute = $appointment->$feeField;

                // Ensure fee per minute is numeric
                $feePerMinute = is_numeric($feePerMinute) ? (float) $feePerMinute : 0;

                // Calculate total: fee per minute × duration
                $appointment->calculated_total = $feePerMinute * $duration;

                // Store the fee per minute used for calculation
                $appointment->fee_per_minute = $feePerMinute;

                // Also update the consultation_fee in database for consistency
                if (is_numeric($appointment->consultation_fee) && $appointment->consultation_fee != $appointment->calculated_total) {
                    // Store original consultation fee
                    $appointment->original_consultation_fee = $appointment->consultation_fee;

                    // Update to calculated total for display
                    $appointment->consultation_fee = $appointment->calculated_total;
                }
            } elseif ($appointmentType == 'offline') {
                // For offline appointments, use the fixed consultation fee
                $appointment->calculated_total = $appointment->offline_fee;

                // Store original consultation fee
                $appointment->original_consultation_fee = $appointment->consultation_fee;

                // Update to calculated total for display
                $appointment->consultation_fee = $appointment->calculated_total;
            } else {
                // Fallback to existing consultation fee
                $appointment->calculated_total = is_numeric($appointment->consultation_fee) ? (float) $appointment->consultation_fee : 0;
            }

            // Store calculation formula for display
            $appointment->calculation_formula = '';
            if ($appointmentType == 'chat' && $duration > 0 && $appointment->chat_fee > 0) {
                $appointment->calculation_formula = number_format($appointment->chat_fee, 2) . " × " . $duration . " min";
                $appointment->fee_per_minute = $appointment->chat_fee;
            } elseif ($appointmentType == 'audio' && $duration > 0 && $appointment->audio_fee > 0) {
                $appointment->calculation_formula = number_format($appointment->audio_fee, 2) . " × " . $duration . " min";
                $appointment->fee_per_minute = $appointment->audio_fee;
            } elseif ($appointmentType == 'video' && $duration > 0 && $appointment->video_fee > 0) {
                $appointment->calculation_formula = number_format($appointment->video_fee, 2) . " × " . $duration . " min";
                $appointment->fee_per_minute = $appointment->video_fee;
            } elseif ($appointmentType == 'offline' && $appointment->offline_fee > 0) {
                $appointment->calculation_formula = "Fixed: Rs. " . number_format($appointment->offline_fee, 2);
            }

            // Add display text for duration
            if ($appointmentType == 'offline') {
                $appointment->duration_display = 'N/A';
            } else {
                $appointment->duration_display = $duration > 0 ? $duration . ' min' : '0 min';
            }

            // Add which fee was used
            if (in_array($appointmentType, ['chat', 'audio', 'video'])) {
                $appointment->active_fee_amount = $appointment->$appointmentType . '_fee';
            } else {
                $appointment->active_fee_amount = $appointment->offline_fee;
            }
        } else {
            // If no doctor data, set default values
            $appointment->duration_display = $appointmentType == 'offline' ? 'N/A' : '0 min';
            $appointment->calculated_total = is_numeric($appointment->consultation_fee) ? (float) $appointment->consultation_fee : 0;
            $appointment->calculation_formula = 'No doctor data';
        }

        return $appointment;
    }

    /**
     * Parse duration from TIME format to minutes
     * TIME format: "HH:MM:SS" or "MM:SS" or just minutes
     */
    private function parseDurationFromTime($duration)
    {
        if (empty($duration) || $duration == '00:00:00') {
            return 0;
        }

        // If it's already numeric (minutes), return as is
        if (is_numeric($duration)) {
            return (int) $duration;
        }

        // If it's in TIME format (HH:MM:SS or MM:SS)
        if (is_string($duration)) {
            // Check if it's in time format
            if (strpos($duration, ':') !== false) {
                $parts = explode(':', $duration);

                // Handle HH:MM:SS format
                if (count($parts) == 3) {
                    $hours = (int) $parts[0];
                    $minutes = (int) $parts[1];
                    $seconds = (int) $parts[2];

                    // Convert to total minutes
                    $totalMinutes = ($hours * 60) + $minutes;
                    if ($seconds >= 30) {
                        $totalMinutes += 1; // Round up if 30+ seconds
                    }
                    return $totalMinutes;
                }
                // Handle MM:SS format
                elseif (count($parts) == 2) {
                    $minutes = (int) $parts[0];
                    $seconds = (int) $parts[1];

                    $totalMinutes = $minutes;
                    if ($seconds >= 30) {
                        $totalMinutes += 1; // Round up if 30+ seconds
                    }
                    return $totalMinutes;
                }
            }

            // Try to convert string to integer
            $numericValue = (int) $duration;
            if ($numericValue > 0) {
                return $numericValue;
            }
        }

        return 0;
    }

    public function walletWithdrawRequests(Request $request)
    {
        $withdrawal_requests = WithDrawRequest::with(['advisor'])
            ->orderBy('id', 'desc')
            ->paginate(config('default_pagination'));

        $total = $withdrawal_requests->total();

        return view('admin-views.order.wallet-withdraw-requests', compact('withdrawal_requests', 'total'));
    }

    public function searchWalletWithdrawRequests(Request $request)
    {
        $key = explode(' ', $request['search']);

        $withdrawal_requests = WithDrawRequest::with(['advisor'])
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhereHas('advisor', function ($subQuery) use ($value) {
                        $subQuery->where('name', 'like', "%{$value}%");
                    });
                }
            })
            ->limit(50)->get();

        return response()->json([
            'view' => view('admin-views.order.partials._wallet_table', compact('withdrawal_requests'))->render()
        ]);
    }

    public function viewWalletWithdrawRequest($id)
    {
        $request = WithDrawRequest::with('advisor')->findOrFail($id);
        return view('admin-views.order.wallet-withdraw-request-view', compact('request'));
    }

    public function deleteWalletWithdrawRequest(Request $request)
    {
        WithDrawRequest::where('id', $request->id)->delete();
        return redirect()->route('admin.wallet-withdrawal-requests')
            ->with('success', 'Withdrawal request deleted successfully.');
    }

    public function manageWalletWithdrawRequest(Request $request)
    {
        $requestId = $request->request_id;
        $requestStatus = $request->request_status;
        $bookingData = WithDrawRequest::find($requestId);

        // Check if the booking data exists
        if ($bookingData) {
            $userId = $bookingData->request_by;

            // Handle rejection with reason
            if ($requestStatus === 'reject' && $request->has('rejection_reason')) {
                $bookingData->rejection_reason = $request->rejection_reason;
            }

            // Handle approval with payment details
            if ($requestStatus === 'approve') {
                $bookingData->payment_method = $request->payment_method;
                $bookingData->account_details = $request->account_details;

                // Handle file upload if present
                if ($request->hasFile('receipt_upload')) {
                    $file = $request->file('receipt_upload');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/receipts'), $filename);
                    $bookingData->receipt_upload = 'uploads/receipts/' . $filename;
                }
            }

            $bookingData->status = $requestStatus;
            $bookingData->save();

            // Retrieve the necessary user and admin data
            $customerData = User::find($userId);
            $adminData = User::where('role_id', 1)->first();

            $msg = "Wallet request $requestStatus" . "ed";

            // Insert a new notification
            DB::table('notifications')->insert([
                'title' => $msg,
                'from_user_id' => $adminData->id,
                'to_user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $msg,
                'data' => [
                    'request_id' => $requestId,
                    'status' => $requestStatus,
                    'payment_method' => $bookingData->payment_method,
                    'account_details' => $bookingData->account_details,
                    'receipt_upload' => $bookingData->receipt_upload
                ]
            ]);
        }

        return response()->json(['status' => 'error', 'code' => 404, 'message' => 'Request not found']);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return back()->with('success', 'Appointment deleted successfully!');
    }




    public function approveWithdraw(Request $request)
    {
        $request->validate([
            'withdraw_id' => 'required|exists:withdrawl_requests,id',
            'payment_method' => 'required|in:check,upi,bank,online',
            'transaction_id' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'note' => 'nullable|string'
        ]);

        $withdraw = WithDrawRequest::find($request->withdraw_id);
        $withdraw->approved = 1; // 1 for approved
        $withdraw->status = 'approve';
        $withdraw->payment_method = $request->payment_method;
        $withdraw->transaction_id = $request->transaction_id;
        $withdraw->note = $request->note;

        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $receiptName = time() . '.' . $receipt->getClientOriginalExtension();
            $receipt->move(public_path('uploads/receipts'), $receiptName);
            $withdraw->receipt_upload = 'uploads/receipts/' . $receiptName;
        }

        $withdraw->save();

        // Deduct from wallet
        $vendor = User::find($withdraw->request_by);
        if ($vendor) {
            $vendor->wallet_amount -= $withdraw->amount;
            $vendor->save();

            // Create wallet transaction
            DB::table('wallet_transactions')->insert([
                'user_id' => $vendor->id,
                'amount' => $withdraw->amount,
                'description' => "Withdrawal: " . ($request->transaction_id ?? $withdraw->id),
                'transaction_for' => 'wallet',
                'is_credit' => '0',
                'created_by' => auth('admin')->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send notification
            DB::table('notifications')->insert([
                'title' => 'Withdrawal Request Approved',
                'description' => "Your withdrawal request of {$withdraw->amount} has been approved.",
                'from_user_id' => auth('admin')->id(),
                'to_user_id' => $vendor->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal request approved successfully'
        ]);
    }

    public function rejectWithdraw(Request $request)
    {
        $request->validate([
            'withdraw_id' => 'required|exists:withdrawl_requests,id',
            'reason' => 'required|string'
        ]);

        $withdraw = WithDrawRequest::find($request->withdraw_id);
        $withdraw->approved = 2; // 2 for rejected
        $withdraw->status = 'reject';
        $withdraw->rejection_reason = $request->reason;
        $withdraw->save();

        // Send notification to vendor
        $vendor = User::find($withdraw->request_by);
        if ($vendor) {
            DB::table('notifications')->insert([
                'title' => 'Withdrawal Request Rejected',
                'description' => "Your withdrawal request was rejected. Reason: {$request->reason}",
                'from_user_id' => auth('admin')->id(),
                'to_user_id' => $vendor->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal request rejected successfully'
        ]);
    }
}