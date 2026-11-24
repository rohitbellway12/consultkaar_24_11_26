<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvisorWalletController extends Controller
{
    /**
     * List all advisors (doctors) with wallet info and total consultations
     */
    public function index(Request $request)
    {
        $advisors = \App\Models\User::where('role_id', 3) // doctor/advisor
            ->where('isregister', 1)
            ->select('id', 'name', 'wallet_amount')
            ->withCount([
                'callRequests as total_consultations' => function ($query) {
                    $query->where('call_status', 'complete');
                }
            ])
            ->paginate(10);

        foreach ($advisors as $advisor) {
            $advisor->doctor_id = $advisor->id;
            $advisor->doctor_name = $advisor->name;
            $advisor->wallet_amount = number_format($advisor->wallet_amount, 2, '.', '');
        }

        return view('admin-views.wallet.advisor-index', compact('advisors'));
    }

    /**
     * Show detailed transactions for a specific advisor (doctor)
     */
    public function show($doctor_id, Request $request)
    {
        // Fetch doctor
        $doctor = DB::table('users')
            ->where('id', $doctor_id)
            ->where('role_id', 3)
            ->first();

        if (!$doctor) {
            abort(404, 'Advisor not found');
        }

        // Fetch transactions WHERE user_id = doctor_id
        $query = DB::table('wallet_transactions as wt')
            ->leftJoin('users as patient', 'wt.created_by', '=', 'patient.id')
            ->select(
                'wt.id',
                'wt.amount',
                'wt.is_credit',
                'wt.description',
                'wt.created_at',
                'patient.name as patient_name',
                'wt.order_id'
            )
            ->where('wt.user_id', $doctor_id)
            ->orderBy('wt.created_at', 'asc'); // ASC for running balance

        // Date filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('wt.created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        // Customer name filter
        if ($request->filled('customer_name')) {
            $query->where('patient.name', 'like', '%' . $request->customer_name . '%');
        }

        $transactions = $query->get();

        // ====== NEW: CALCULATE SUMMARY ======
        $summary = (object) [
            'total_earnings' => 0,
            'total_commission' => 0,
            'total_consultations' => 0,
        ];

        $commissionPercent = $doctor->commission_percentage ?? 0;

        foreach ($transactions as $tx) {
            if ($tx->is_credit == '1') {
                $summary->total_earnings += $tx->amount;
                // $summary->total_consultations++;  // Removed old logic
            }
            $summary->total_commission += ($tx->amount * $commissionPercent) / 100;
        }

        // Calculate total consultations from CallRequest
        $summary->total_consultations = \App\Models\CallRequest::where('doctor_id', $doctor_id)
            ->where('call_status', 'complete')
            ->count();

        // Calculate running balance
        $balance = 0;
        $enrichedTransactions = [];
        foreach ($transactions as $tx) {
            if ($tx->is_credit == '1') {
                $balance += $tx->amount;
            } else {
                $balance -= $tx->amount;
            }

            $commissionAmount = ($tx->amount * $commissionPercent) / 100;

            $enrichedTransactions[] = (object) array_merge((array) $tx, [
                'amount' => number_format($tx->amount, 2, '.', ''),
                'current_amount' => number_format($balance, 2, '.', ''),
                'commission_amount' => number_format($commissionAmount, 2, '.', ''),
            ]);
        }

        // Reverse to show latest first
        $enrichedTransactions = array_reverse($enrichedTransactions);

        // Pass summary to the view
        return view('admin-views.wallet.advisor-show', compact('doctor', 'enrichedTransactions', 'summary'));
    }
}