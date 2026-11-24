<?php

namespace App\Http\Controllers\Doctor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\CallRequest;
use App\Models\Order;
use App\Models\UserPassword;
use App\Models\UsersAddress;
use App\Models\User; // ADD THIS IMPORT

class CustomerController extends Controller
{
    function list()
    {
        $doctorId = auth()->user()->id;

        // Get unique patients for this doctor from CallRequests
        $user_list = CallRequest::where('doctor_id', $doctorId)
            ->where('call_status', 'complete')
            ->join('users', 'call_requests.user_id', '=', 'users.id')
            ->select('user_id', 'users.name as patient_name', 'users.mobile_number as patient_mobile_number', DB::raw('MAX(call_requests.created_at) as last_appointment'))
            ->groupBy('user_id', 'users.name', 'users.mobile_number')
            ->orderBy('last_appointment', 'desc')
            ->paginate(config('default_pagination'));

        return view('doctor-views.customer.list', compact('user_list'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);

        $doctorId = auth()->user()->id;

        $customers = CallRequest::where('doctor_id', $doctorId)
            ->where('call_status', 'complete')
            ->join('users', 'call_requests.user_id', '=', 'users.id')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('users.name', 'like', "%{$value}%");
                    $q->orWhere('users.mobile_number', 'like', "%{$value}%");
                }
            })
            ->select('user_id', 'users.name as patient_name', 'users.mobile_number as patient_mobile_number', DB::raw('MAX(call_requests.created_at) as last_appointment'))
            ->groupBy('user_id', 'users.name', 'users.mobile_number')
            ->orderBy('last_appointment', 'desc')
            ->paginate(config('default_pagination'));

        return response()->json([
            'view' => view('doctor-views.customer.partials._table', compact('customers'))->render(),
            'count' => $customers->total() // Changed from count() to total() for pagination
        ]);
    }

    public function view($userId) // Changed parameter name for clarity
    {
        $doctorId = auth()->user()->id;

        // Verify this patient has call requests with this doctor
        $appointmentExists = CallRequest::where('doctor_id', $doctorId)
            ->where('user_id', $userId)
            ->exists();

        if (!$appointmentExists) {
            Toastr::error(__('messages.customer_not_found'));
            return back();
        }

        // Get patient details
        $customer = User::find($userId);

        if (!$customer) {
            Toastr::error(__('messages.customer_not_found'));
            return back();
        }

        // Get patient's call history with this doctor
        $appointments = CallRequest::where('doctor_id', $doctorId)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(config('default_pagination'));

        // Get orders if needed (your existing order logic)
        if (session()->has('customer_order_filter')) {
            $request = json_decode(session('customer_order_filter'));
            $status = 'all';

            $orders = Order::latest()
                ->when(isset($request->zone), function ($query) use ($request) {
                    return $query->whereHas('vendor', function ($q) use ($request) {
                        return $q->whereIn('zone_id', $request->zone);
                    });
                })
                ->when($status == 'ongoing', function ($query) {
                    return $query->ServiceOngoing();
                })
                ->when($status == 'completed', function ($query) {
                    return $query->Completed();
                })
                // ... rest of your order filtering logic
                ->where(['user_id' => $userId])
                ->orderBy('id', 'desc')
                ->paginate(config('default_pagination'));

            $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
            $from_date = isset($request->from_date) ? $request->from_date : null;
            $to_date = isset($request->to_date) ? $request->to_date : null;
        } else {
            $orders = Order::latest()
                ->where(['user_id' => $userId])
                ->paginate(config('default_pagination'));
            $status = 'all';
            $orderstatus = [];
            $from_date = null;
            $to_date = null;
        }

        return view('doctor-views.customer.customer-view', compact(
            'customer',
            'appointments', // Added appointments to view
            'orders',
            'status',
            'orderstatus',
            'from_date',
            'to_date'
        ));
    }

    public function filter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);

        session()->put('customer_order_filter', json_encode($request->all()));
        return back();
    }

    public function filter_reset(Request $request)
    {
        session()->forget('customer_order_filter');
        return back();
    }
}