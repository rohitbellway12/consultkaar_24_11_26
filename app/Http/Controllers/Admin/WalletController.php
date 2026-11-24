<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('users as u')
            ->where('u.role_id', 2)
            ->where('u.isregister', 1);

        if ($request->filled('search')) {
            $query->where('u.name', 'LIKE', '%' . $request->get('search') . '%');
        }

        $customers = $query->select(
            'u.id',
            'u.name',
            'u.wallet_amount',
            DB::raw("(SELECT wt.created_at
                          FROM wallet_transactions wt
                          WHERE wt.user_id = u.id 
                            AND wt.is_credit = '1'
                            AND wt.transaction_for = 'wallet'
                          ORDER BY wt.created_at DESC 
                          LIMIT 1) AS last_recharge_date")
        )
            ->orderBy('u.name')
            ->paginate(10);

        return view('admin-views.wallet.index', compact('customers'));
    }


    public function viewCustomerWallet(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        $from = $request->from;
        $to = $request->to;
        $advisor_name = $request->advisor_name;

        // Get initial balance before the from date if filtering by date
        $initialBalance = 0;
        if ($from) {
            $prevTransactions = DB::table('wallet_transactions')
                ->where('user_id', $id)
                ->where('created_at', '<', $from . ' 00:00:00')
                ->get();

            foreach ($prevTransactions as $ptx) {
                if ($ptx->is_credit == '1') {
                    $initialBalance += $ptx->amount;
                } else {
                    $initialBalance -= $ptx->amount;
                }
            }
        }

        // Get customer transactions
        $query = DB::table('wallet_transactions as customer_tx')
            ->where('customer_tx.user_id', $id);

        if ($from && $to) {
            $query->whereBetween('customer_tx.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $transactions = $query->select(
            'customer_tx.id',
            'customer_tx.amount',
            'customer_tx.description',
            'customer_tx.created_at',
            'customer_tx.is_credit',
            'customer_tx.order_id',
            'customer_tx.transaction_for'
        )
            ->orderBy('customer_tx.created_at', 'ASC')
            ->get();

        // Enrich with doctor name using order_id
        $enrichedTransactions = $transactions->map(function ($tx) {
            $doctorName = 'System';

            if ($tx->is_credit == '0' && $tx->order_id && $tx->transaction_for == 'appointment') {
                $doctorTx = DB::table('wallet_transactions')
                    ->where('order_id', $tx->order_id)
                    ->where('is_credit', '1')
                    ->where('transaction_for', 'appointment')
                    ->first();

                if ($doctorTx && $doctorTx->user_id) {
                    $doctor = DB::table('users')
                        ->where('id', $doctorTx->user_id)
                        ->where('role_id', 3)
                        ->first();
                    $doctorName = $doctor ? $doctor->name : 'Unknown Doctor';
                }
            } else {
                $doctorName = 'Self Recharge';
            }

            $tx->doctor_name = $doctorName;
            return $tx;
        });

        // Filter by advisor name if provided
        if ($advisor_name) {
            $enrichedTransactions = $enrichedTransactions->filter(function ($tx) use ($advisor_name) {
                return stripos($tx->doctor_name, $advisor_name) !== false;
            });
        }

        // Compute running balance starting from initialBalance
        $balance = $initialBalance;
        $transactionsWithBalance = $enrichedTransactions->map(function ($tx) use (&$balance) {
            if ($tx->is_credit == '1') {
                $balance += $tx->amount;
            } else {
                $balance -= $tx->amount;
            }
            $tx->current_balance = $balance;
            $tx->type = ($tx->is_credit == '1') ? 'Credit' : 'Debit';
            return $tx;
        })->reverse(); // latest first

        return view('admin-views.wallet.view', compact('customer', 'transactionsWithBalance'));
    }
}