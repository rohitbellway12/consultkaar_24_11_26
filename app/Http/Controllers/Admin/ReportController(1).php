<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportController extends Controller
{
    public function earning_index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $doctor_id = $request->input('doctor_id');

        $query = DB::table('wallet_transactions as wt')
            ->join('users as doctor', 'wt.user_id', '=', 'doctor.id')
            ->whereIn('wt.transaction_for', ['call', 'chat'])
            ->where('wt.is_credit', '1')
            ->select(
                'wt.id',
                'wt.amount as gross_amount',
                'wt.created_at',
                'doctor.name as doctor_name',
                'doctor.id as doctor_id',
                'doctor.commission_percentage',
                'wt.order_id',
                'wt.transaction_for'
            );

        if ($from && $to) {
            $query->whereBetween('wt.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        if ($doctor_id) {
            $query->where('doctor.id', $doctor_id);
        }

        $transactions = $query->orderBy('wt.created_at', 'desc')->paginate(25);

        // Pre-calculate summary for the current view
        $summary = [
            'total_gross' => 0,
            'total_commission' => 0,
            'total_net' => 0
        ];

        // For summary, we need the totals of the filtered set, not just the page
        $totalsQuery = clone $query;
        $totals = $totalsQuery->get();

        foreach ($totals as $tx) {
            $comm = ($tx->gross_amount * ($tx->commission_percentage ?? 0)) / 100;
            $summary['total_gross'] += $tx->gross_amount;
            $summary['total_commission'] += $comm;
            $summary['total_net'] += ($tx->gross_amount - $comm);
        }

        $doctors = User::where('role_id', 3)->get();

        return view('admin-views.report.earning', compact('transactions', 'summary', 'doctors', 'from', 'to', 'doctor_id'));
    }

    public function earning_export(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $doctor_id = $request->input('doctor_id');

        $query = DB::table('wallet_transactions as wt')
            ->join('users as doctor', 'wt.user_id', '=', 'doctor.id')
            ->whereIn('wt.transaction_for', ['call', 'chat'])
            ->where('wt.is_credit', '1')
            ->select(
                'wt.id',
                'wt.amount as gross_amount',
                'wt.created_at',
                'doctor.name as doctor_name',
                'doctor.commission_percentage',
                'doctor.id as doctor_id',
                'wt.order_id',
                'wt.transaction_for'
            );

        if ($from && $to) {
            $query->whereBetween('wt.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        if ($doctor_id) {
            $query->where('doctor.id', $doctor_id);
        }

        $transactions = $query->orderBy('wt.created_at', 'desc')->get();

        return (new FastExcel($transactions))->download('earning_report.xlsx', function ($tx) {
            $comm = ($tx->gross_amount * ($tx->commission_percentage ?? 0)) / 100;
            return [
                'Date' => date('Y-m-d H:i:s', strtotime($tx->created_at)),
                'Order ID' => $tx->order_id,
                'Advisor' => $tx->doctor_name,
                'Gross Amount' => $tx->gross_amount,
                'Admin Commission (' . ($tx->commission_percentage ?? 0) . '%)' => $comm,
                'Net Advisor Earning' => $tx->gross_amount - $comm,
            ];
        });
    }

    public function customer_index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = DB::table('users as u')
            ->where('u.role_id', 2)
            ->where('u.isregister', 1)
            ->select(
                'u.id',
                'u.name',
                'u.mobile_number',
                'u.wallet_amount',
                'u.created_at',
                DB::raw('(SELECT COUNT(*) FROM call_requests WHERE call_requests.user_id = u.id AND call_status = "complete") as total_appointments'),
                DB::raw('(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_transactions.user_id = u.id AND is_credit = "0" AND transaction_for IN ("call", "chat")) as total_spent')
            );

        if ($from && $to) {
            $query->whereBetween('u.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $customers = $query->orderBy('total_spent', 'desc')->paginate(25);

        $summary = [
            'total_customers' => DB::table('users')->where('role_id', 2)->where('isregister', 1)->count(),
            'active_customers' => DB::table('wallet_transactions')->where('is_credit', '0')->whereIn('transaction_for', ['call', 'chat'])->distinct('user_id')->count(),
            'total_wallet_balance' => DB::table('users')->where('role_id', 2)->where('isregister', 1)->sum('wallet_amount')
        ];

        return view('admin-views.report.customer-report', compact('customers', 'summary', 'from', 'to'));
    }

    public function customer_export(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = DB::table('users as u')
            ->where('u.role_id', 2)
            ->where('u.isregister', 1)
            ->select(
                'u.name',
                'u.mobile_number',
                'u.wallet_amount',
                'u.created_at',
                DB::raw('(SELECT COUNT(*) FROM call_requests WHERE call_requests.user_id = u.id AND call_status = "complete") as total_appointments'),
                DB::raw('(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_transactions.user_id = u.id AND is_credit = "0" AND transaction_for IN ("call", "chat")) as total_spent')
            );

        if ($from && $to) {
            $query->whereBetween('u.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $customers = $query->orderBy('total_spent', 'desc')->get();

        return (new FastExcel($customers))->download('customer_report.xlsx', function ($c) {
            return [
                'Customer Name' => $c->name,
                'Mobile' => $c->mobile_number,
                'Wallet Balance' => $c->wallet_amount,
                'Total Appointments' => $c->total_appointments,
                'Total Spent' => $c->total_spent ?? 0,
                'Joined At' => date('Y-m-d', strtotime($c->created_at)),
            ];
        });
    }

    public function advisor_index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = DB::table('users as u')
            ->where('u.role_id', 3)
            ->where('u.isregister', 1)
            ->select(
                'u.id',
                'u.name',
                'u.mobile_number',
                'u.wallet_amount',
                'u.commission_percentage',
                'u.created_at',
                DB::raw('(SELECT COUNT(*) FROM call_requests WHERE call_requests.doctor_id = u.id AND call_status = "complete" ' . ($from && $to ? " AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_appointments'),
                DB::raw('(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_transactions.user_id = u.id AND is_credit = "1" AND transaction_for IN ("call", "chat") ' . ($from && $to ? " AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_gross')
            );

        $advisors = $query->orderBy('total_gross', 'desc')->paginate(25);

        // Summary metrics
        $summary_query = DB::table('wallet_transactions')
            ->where('is_credit', '1')
            ->whereIn('transaction_for', ['call', 'chat']);

        if ($from && $to) {
            $summary_query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $total_gross_all = $summary_query->sum('amount');

        // Approximate commission (Note: this is a global sum, for precise per-advisor commission we iterate or use a complex join)
        // For the summary card, we'll just show the total gross.

        $summary = [
            'total_advisors' => DB::table('users')->where('role_id', 3)->where('isregister', 1)->count(),
            'total_appointments' => DB::table('call_requests')->where('call_status', 'complete')->count(),
            'total_gross' => $total_gross_all,
            'total_wallet_balance' => DB::table('users')->where('role_id', 3)->where('isregister', 1)->sum('wallet_amount')
        ];

        return view('admin-views.report.advisor-report', compact('advisors', 'summary', 'from', 'to'));
    }

    public function advisor_export(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = DB::table('users as u')
            ->where('u.role_id', 3)
            ->where('u.isregister', 1)
            ->select(
                'u.id',
                'u.name',
                'u.mobile_number',
                'u.wallet_amount',
                'u.commission_percentage',
                'u.created_at',
                DB::raw('(SELECT COUNT(*) FROM call_requests WHERE call_requests.doctor_id = u.id AND call_status = "complete" ' . ($from && $to ? " AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_appointments'),
                DB::raw('(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_transactions.user_id = u.id AND is_credit = "1" AND transaction_for IN ("call", "chat") ' . ($from && $to ? " AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'" : "") . ') as total_gross')
            );

        $advisors = $query->orderBy('total_gross', 'desc')->get();

        return (new FastExcel($advisors))->download('advisor_report.xlsx', function ($a) {
            $comm = ($a->total_gross * ($a->commission_percentage ?? 0)) / 100;
            return [
                'Advisor Name' => $a->name,
                'Mobile' => $a->mobile_number,
                'Commission %' => $a->commission_percentage ?? 0,
                'Total Appointments' => $a->total_appointments,
                'Total Gross' => $a->total_gross ?? 0,
                'Admin Commission' => $comm,
                'Net Advisor Earning' => ($a->total_gross ?? 0) - $comm,
                'Current Wallet Balance' => $a->wallet_amount,
                'Joined At' => date('Y-m-d', strtotime($a->created_at)),
            ];
        });
    }
    public function set_date(Request $request)
    {
        session()->put('from_date', $request['from']);
        session()->put('to_date', $request['to']);
        return back();
    }

    public function order_index(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-d', strtotime("-30 days")));
            session()->put('to_date', date('Y-m-d'));
        }
        $from = session('from_date');
        $to = session('to_date');

        $orders = DB::table('call_requests')
            ->join('users as patient', 'call_requests.user_id', '=', 'patient.id')
            ->join('users as doctor', 'call_requests.doctor_id', '=', 'doctor.id')
            ->whereBetween('call_requests.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->select('call_requests.*', 'patient.name as patient_name', 'doctor.name as doctor_name')
            ->orderBy('call_requests.created_at', 'desc')
            ->paginate(25);

        return view('admin-views.report.order-report', compact('orders', 'from', 'to'));
    }

    public function day_wise_report(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-d', strtotime("-30 days")));
            session()->put('to_date', date('Y-m-d'));
        }
        $from = session('from_date');
        $to = session('to_date');

        return view('admin-views.report.day-wise-report', compact('from', 'to'));
    }

    public function food_wise_report(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-d', strtotime("-30 days")));
            session()->put('to_date', date('Y-m-d'));
        }
        $from = session('from_date');
        $to = session('to_date');

        // Food wise in Consultkaar might be 'Speciality' wise or 'Disease' wise
        // For now, let's provide what the legacy view expects but based on diseases
        $foods = DB::table('diseases')
            ->leftJoin('call_requests', function ($join) {
                // This is a placeholder since call_requests doesn't have disease_id yet
                // But we need to define $foods to avoid blade errors
                $join->on(DB::raw('1'), '=', DB::raw('0'));
            })
            ->select('diseases.*', DB::raw('COUNT(call_requests.id) as order_count'), DB::raw('"" as image'))
            ->groupBy('diseases.id')
            ->paginate(25);

        return view('admin-views.report.food-wise-report', compact('foods', 'from', 'to'));
    }
}
