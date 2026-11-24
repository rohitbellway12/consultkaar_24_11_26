<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\DeliveryMan;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Vendor;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //     public function dashboard(Request $request)
//     {
//         $year = $request->input('year', date('Y')); // Default to current year if not provided
//         session()->put('dash_year', $year);
// //$doctorId = auth('doctor')->user()->id;

    //         $data = $this->dashboard_data($year);

    //         $total_patients = User::where('isregister',1)->where(array('role_id' => 2,'lang_name' => 'en'))->count();
//         $total_orders = Order::All()->count();
//         $total_appointments =  DB::table('appointments')->count();
//         $total_doctors = User::where('isregister',1)->where(array('role_id' => 3,'lang_name' => 'en'))->count();
//          $pending_doctors = User::where('status',0)->where('isregister',1)->where(array('role_id' => 3,'lang_name' => 'en'))->count();
//         $total_sell = $data['total_sell'];


    //         return view('admin-views.dashboard', compact('data', 'total_patients', 'total_orders', 'total_doctors','total_appointments','total_sell','pending_doctors'));
//     }
    public function dashboard(Request $request)
    {
        $year = $request->input('year', date('Y'));
        session()->put('dash_year', $year);

        $data = $this->dashboard_data($year);

        $total_patients = User::where('role_id', 2)
            ->where('isregister', 1)
            ->where('lang_name', 'en')
            ->count();

        $total_doctors = User::where('role_id', 3)
            ->where('isregister', 1)
            ->where('lang_name', 'en')
            ->count();

        $pending_doctors = User::where('role_id', 3)
            ->where('status', 0)        // pending
            ->where('isregister', 1)    // registered
            ->where('lang_name', 'en')
            ->count();

        $total_orders = Order::count();

        $total_appointments = DB::table('call_requests')->count();

        $total_sell = $data['total_sell'];
        // dd($pending_doctors);
        return view(
            'admin-views.dashboard',
            compact(
                'data',
                'total_patients',
                'total_orders',
                'total_doctors',
                'pending_doctors',
                'total_appointments',
                'total_sell'
            )
        );
    }



    public function order_stats_calc()
    {


        $ongoing = Order::ServiceOngoing()->count();
        $completed = Order::Completed()->count();
        $cancelled = Order::Cancelled()->count();
        $refund_requested = Order::failed()->count();
        $refunded = Order::Refunded()->count();



        $data = [
            'ongoing' => $ongoing,
            'cancelled' => $cancelled,
            'completed' => $completed,
            'refund_requested' => $refund_requested,
            'refunded' => $refunded
        ];
        return $data;
    }



    public function dashboard_data($year)
    {
        $total_sell = [];

        for ($i = 1; $i <= 12; $i++) {
            $totalConsultationFee = DB::table('wallet_transactions')
                ->join('users', 'users.id', 'wallet_transactions.user_id')
                ->where('users.role_id', '3')
                ->whereIn('wallet_transactions.transaction_for', ['call', 'chat'])
                ->where('wallet_transactions.is_credit', '1')
                ->whereMonth('wallet_transactions.created_at', $i)
                ->whereYear('wallet_transactions.created_at', $year)
                ->sum('wallet_transactions.amount');

            $total_sell[$i] = $totalConsultationFee;
        }

        $data_os = $this->order_stats_calc();

        return array_merge($data_os, ['total_sell' => $total_sell]);
    }
}
