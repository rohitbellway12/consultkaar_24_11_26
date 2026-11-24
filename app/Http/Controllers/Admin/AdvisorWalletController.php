<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class AdvisorWalletController extends Controller
{
    /**
     * List all advisors (doctors) with wallet info and total consultations
     */
    public function index(Request $request)
    {
        $query = \App\Models\User::where('role_id', 3) // doctor/advisor
            ->where('isregister', 1)
            ->select('id', 'name', 'wallet_amount');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $advisors = $query->withCount([
                'callRequests as total_consultations' => function ($q) {
                    $q->where('call_status', 'complete');
                }
            ])
            ->paginate(10);

        $advisors->appends(['search' => $request->search]);

        foreach ($advisors as $advisor) {
            $advisor->doctor_id = $advisor->id;
            $advisor->doctor_name = $advisor->name;

            $runningBalance = 0;
            foreach (DB::table('wallet_transactions')->where('user_id', $advisor->id)->orderBy('created_at', 'asc')->get() as $tx) {
                if ($tx->is_credit == '1') {
                    $runningBalance += $tx->amount;
                } else {
                    $runningBalance -= $tx->amount;
                }
            }
            $advisor->wallet_amount = number_format($runningBalance, 2, '.', '');
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
            ->leftJoin('call_requests as call', 'wt.order_id', '=', 'call.id')
            ->leftJoin('users as patients', 'patients.id', '=', 'call.user_id')
            ->leftJoin('users as admin_user', 'wt.created_by', '=', 'admin_user.id')
            ->select(
                'wt.id',
                'wt.amount',
                'wt.is_credit',
                'wt.description',
                'wt.created_at',
                'wt.transaction_for',
                'patients.name as patient_name',
                'admin_user.name as admin_name',
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
            $query->where(function ($q) use ($request) {
                $q->where('patients.name', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('admin_user.name', 'like', '%' . $request->customer_name . '%');
            });
        }

        $transactions = $query->get();

        // ====== NEW: CALCULATE SUMMARY ======
        $summary = (object) [
            'total_earnings' => 0,
            'total_commission' => 0,
            'total_consultations' => 0,
            'reel_earnings' => 0,
        ];

        $commissionPercent = $doctor->commission_percentage ?? 0;

        foreach ($transactions as $tx) {
            if ($tx->is_credit == '1') {
                if (strpos($tx->transaction_for, 'reel') !== false) {
                    $summary->reel_earnings += $tx->amount;
                } else {
                    $summary->total_earnings += $tx->amount;
                    $summary->total_commission += ($tx->amount * $commissionPercent) / 100;
                }
            }
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

            $isReelTransaction = strpos($tx->transaction_for, 'reel') !== false;
            $commissionAmount = $isReelTransaction ? 0 : ($tx->amount * $commissionPercent) / 100;

            $enrichedTransactions[] = (object) array_merge((array) $tx, [
                'amount' => number_format($tx->amount, 2, '.', ''),
                'current_amount' => number_format($balance, 2, '.', ''),
                'commission_amount' => number_format($commissionAmount, 2, '.', ''),
            ]);
        }

        // Reverse to show latest first
        $enrichedTransactions = array_reverse($enrichedTransactions);

        $currentBalance = (float) ($doctor->wallet_amount ?? 0);

        // Pass summary to the view
        return view('admin-views.wallet.advisor-show', compact('doctor', 'enrichedTransactions', 'summary', 'currentBalance'));
    }

    public function credit(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $doctor = DB::table('users')->where('id', $request->doctor_id)->where('role_id', 3)->first();
        if (!$doctor) {
            Toastr::error('Advisor not found');
            return back();
        }

        $creditAmount = (float) $request->amount;
        $transactionFor = $request->filled('reel_earning') ? 'reel_credit' : 'manual_credit';

        DB::table('wallet_transactions')->insert([
            'user_id' => $request->doctor_id,
            'amount' => $creditAmount,
            'description' => $request->description,
            'transaction_for' => $transactionFor,
            'is_credit' => '1',
            'created_by' => auth('admin')->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newBalance = 0;
        foreach (DB::table('wallet_transactions')->where('user_id', $request->doctor_id)->orderBy('created_at', 'asc')->get() as $tx) {
            if ($tx->is_credit == '1') {
                $newBalance += $tx->amount;
            } else {
                $newBalance -= $tx->amount;
            }
        }

        DB::table('users')->where('id', $request->doctor_id)->update([
            'wallet_amount' => $newBalance
        ]);

        Toastr::success('Amount credited successfully.');
        return redirect()->route('admin.wallet.advisor.show', $request->doctor_id);
    }

    public function debit(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $doctor = DB::table('users')->where('id', $request->doctor_id)->where('role_id', 3)->first();
        if (!$doctor) {
            Toastr::error('Advisor not found');
            return back();
        }

        $runningBalance = 0;
        foreach (DB::table('wallet_transactions')->where('user_id', $request->doctor_id)->orderBy('created_at', 'asc')->get() as $tx) {
            if ($tx->is_credit == '1') {
                $runningBalance += $tx->amount;
            } else {
                $runningBalance -= $tx->amount;
            }
        }

        $debitAmount = (float) $request->amount;

        if ($runningBalance < $debitAmount) {
            Toastr::error('Insufficient wallet balance. Current balance: ' . number_format($runningBalance, 2));
            return back();
        }

        $transactionFor = $request->filled('reel_earning') ? 'reel_debit' : 'manual_debit';

        DB::table('wallet_transactions')->insert([
            'user_id' => $request->doctor_id,
            'amount' => $debitAmount,
            'description' => $request->description,
            'transaction_for' => $transactionFor,
            'is_credit' => '0',
            'created_by' => auth('admin')->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newBalance = 0;
        foreach (DB::table('wallet_transactions')->where('user_id', $request->doctor_id)->orderBy('created_at', 'asc')->get() as $tx) {
            if ($tx->is_credit == '1') {
                $newBalance += $tx->amount;
            } else {
                $newBalance -= $tx->amount;
            }
        }

        DB::table('users')->where('id', $request->doctor_id)->update([
            'wallet_amount' => $newBalance
        ]);

        Toastr::success('Amount debited successfully.');
        return redirect()->route('admin.wallet.advisor.show', $request->doctor_id);
    }
}