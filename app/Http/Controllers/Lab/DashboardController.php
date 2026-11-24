<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\LabTest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
         $labData = Session::get('lab_data'); 
         $lab_id = $labData->id;
         
        $year = $request->input('year', date('Y')); // Default to current year if not provided
        session()->put('dash_year', $year);
        $labData = session('lab_data');
        $labId = $labData->id;
   

        $data = $this->dashboard_data($year,$labId);

        $total_bookings = DB::table('users_lab_tests')->where('lab_id', $labId)->count();
        
        $total_lab_test = LabTest::where('lab_id', $lab_id)->count();
        $total_sell = $data['total_sell'];
        return view('lab-views.dashboard', compact(
            'data',
            'total_bookings',
            'total_sell',
            'total_lab_test'
        ));
    }

    public function dashboard_data()
    {
        $total_sell = [];
        $labData = session('lab_data');
        $labId = $labData->id;

        for ($i = 1; $i <= 12; $i++) {
            $totalAmount = DB::table('users_lab_tests')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at',date('Y'))
                ->where('lab_id', $labId)
                ->sum('amount');

            $total_sell[$i] = $totalAmount;
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

        $data = $this->dashboard_data();

        return response()->json([
            'monthly_graph' => view('lab-views.partials._monthly-earning-graph', compact('data'))->render(),
        ], 200);
    }
}
