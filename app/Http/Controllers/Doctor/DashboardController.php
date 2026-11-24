<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CallRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $year = $request->input('year', date('Y')); // Default to current year if not provided
        session()->put('dash_year', $year);
        $doctorId = auth()->user()->id;

        $data = $this->dashboard_data($year, $doctorId);

        $user_list_query = CallRequest::where('doctor_id', $doctorId)
            ->where('call_status', 'complete')
            ->latest();

        $total_patients = $user_list_query->distinct('user_id')->count();
        $total_orders = Order::count();
        $doctor_id = auth()->user()->id;

        $total_appointments = DB::table('call_requests')->where('doctor_id', $doctor_id)->where('call_status', 'complete')->count();
        $total_video_consultations = DB::table('call_requests')->where([
            'doctor_id' => $doctor_id,
            'call_type' => 'video',
            'call_status' => 'complete'
        ])->count();
        $total_sell = $data['total_sell'];

        return view('doctor-views.dashboard', compact(
            'data',
            'total_patients',
            'total_orders',
            'total_appointments',
            'total_video_consultations',
            'total_sell'
        ));
    }

    public function dashboard_data($year, $doctorId)
    {
        $total_sell = [];

        for ($i = 1; $i <= 12; $i++) {
            $totalConsultationFee = DB::table('wallet_transactions')
                ->where('user_id', $doctorId)
                ->whereIn('transaction_for', ['call', 'chat'])
                ->where('is_credit', '1')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', $year)
                ->sum('amount');

            $total_sell[$i] = $totalConsultationFee;
        }

        $data_os = $this->order_stats_calc();

        return array_merge($data_os, ['total_sell' => $total_sell]);
    }

    public function order_stats_calc()
    {
        $ongoing = Order::ServiceOngoing()->count();
        $completed = Order::Completed()->count();
        $cancelled = Order::Cancelled()->count();
        $refund_requested = Order::failed()->count();
        $refunded = Order::Refunded()->count();

        return compact(
            'ongoing',
            'completed',
            'cancelled',
            'refund_requested',
            'refunded'
        );
    }

    public function yearWiseData(Request $request)
    {
        $year = $request->input('year', date('Y')); // Default to current year if not provided
        session()->put('dash_year', $year);

        $data = $this->dashboard_data($year, auth()->user()->id);

        return response()->json([
            'monthly_graph' => view('doctor-views.partials._monthly-earning-graph', compact('data'))->render(),
        ], 200);
    }
}
