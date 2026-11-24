<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Order;
use App\Models\UsersAddress;
use Illuminate\Pagination\LengthAwarePaginator;


class CustomerController extends Controller
{

    public function add_new()
    {
        return view('admin-views.customer.add-new');
    }


    // function list(Request $request)
    // {
    //     $user_list = DB::table('users')
    //     ->select('*')
    //     ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, uid FROM users WHERE role_id = 2 GROUP BY uid) as sub'), function ($join) {
    //         $join->on('users.created_at', '=', 'sub.min_created_at')
    //              ->on('users.uid', '=', 'sub.uid');
    //     })
    //     ->where('users.isregister', 1)
    //     ->orderBy('created_at','desc')->get();
        
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $perPage = 10; // Change this as needed
    //     $items = collect($user_list);
    //     $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();
    //     $user_list = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    //     $startIndex = $user_list->firstItem() - 1;

    //     // Set path for pagination links
    //     $user_list->withPath($request->url());
       
    //     return view('admin-views.customer.list', compact('user_list', 'startIndex'));
    // }

// public function list(Request $request)
// {
//     $searchKey = $request->input('search');

//     $query = DB::table('users')
//         ->select('*')
//         ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, uid FROM users WHERE role_id = 2 GROUP BY uid) as sub'), function ($join) {
//             $join->on('users.created_at', '=', 'sub.min_created_at')
//                  ->on('users.uid', '=', 'sub.uid');
//         })
//         ->where('users.isregister', 1)
//         ->orderBy('created_at', 'desc');

//     if ($searchKey) {
//         $query->where(function ($q) use ($searchKey) {
//             $q->where('name', 'like', "%{$searchKey}%")
//               ->orWhere('mobile_number', 'like', "%{$searchKey}%")
//               ->orWhere('email', 'like', "%{$searchKey}%");
//         });
//     }

//     $user_list = $query->paginate(10)->withQueryString();

//     return view('admin-views.customer.list', compact('user_list'));
// }


public function list(Request $request)
{
    $searchKey = $request->input('search');
    $fromDate  = $request->input('from_date');
    $toDate    = $request->input('to_date');
    $filter    = $request->input('filter'); // today | last_7_days | last_30_days

    $query = DB::table('users')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, uid 
                FROM users 
                WHERE role_id = 2 
                GROUP BY uid) as sub'), function ($join) {
            $join->on('users.created_at', '=', 'sub.min_created_at')
                 ->on('users.uid', '=', 'sub.uid');
        })
        ->where('users.role_id', 2)
        ->where('users.isregister', 1)
        ->orderBy('users.created_at', 'desc');

    // 🔍 Search
    if ($searchKey) {
        $query->where(function ($q) use ($searchKey) {
            $q->where('users.name', 'like', "%{$searchKey}%")
              ->orWhere('users.mobile_number', 'like', "%{$searchKey}%")
              ->orWhere('users.email', 'like', "%{$searchKey}%");
        });
    }

    // 📅 Quick filters
    if ($filter == 'today') {
        $query->whereDate('users.created_at', today());
    }

    if ($filter == 'last_7_days') {
        $query->whereBetween('users.created_at', [
            now()->subDays(7)->startOfDay(),
            now()->endOfDay()
        ]);
    }

    if ($filter == 'last_30_days') {
        $query->whereBetween('users.created_at', [
            now()->subDays(30)->startOfDay(),
            now()->endOfDay()
        ]);
    }

    // 📆 Custom date range
    if ($fromDate && $toDate) {
        $query->whereBetween('users.created_at', [
            $fromDate . ' 00:00:00',
            $toDate . ' 23:59:59'
        ]);
    }

    $user_list = $query->select('users.*')
        ->paginate(10)
        ->withQueryString();

    return view('admin-views.customer.list', compact('user_list'));
}


public function edit($id)
{
    $customer = User::where('id', $id)->where('role_id', 2)->first();

    if (!$customer) {
        Toastr::error(__('messages.customer_not_found'));
        return redirect()->route('admin.patient.list');
    }

    return view('admin-views.customer.edit', compact('customer'));
}




public function update(Request $request, $id)
{
    $customer = User::where('id', $id)->where('role_id', 2)->first();

    if (!$customer) {
        Toastr::error(__('messages.customer_not_found'));
        return redirect()->route('admin.patient.list');
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'mobile_number' => 'required|string|max:20|unique:users,mobile_number,' . $id,
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|string|in:Male,Female,Other',
        'martial_status' => 'nullable|string|max:20',
        'occupation' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
    ]);

    $customer->update($validated);

    Toastr::success('Customer updated successfully!');
    return redirect()->route('admin.patient.list');
}

    // public function destroy($id)
    // {
    //      User::where(['id'=>$id])->delete();
    //      // delete user's booking data
         
    //      Order::where(['user_id'=>$id])->delete();
       
         
    //       // delete user's address data 
          
    //      UsersProfession::where(['user_id'=>$id])->delete();
         
    //     Toastr::success('Patient deleted succesfully');
    //     return back();
    // }
public function destroy($id)
{
    try {
        \Log::info('Deleting patient ID: ' . $id);
        
        // Delete user
        $user = User::find($id);
        if (!$user) {
            Toastr::error('Patient not found');
            return back();
        }
        
        // Delete user's orders
        Order::where('user_id', $id)->delete();
        
        // Delete user's addresses
        UsersAddress::where('user_id', $id)->delete();
        
        // Delete user
        $user->delete();
        
        \Log::info('Patient deleted successfully: ' . $id);
        Toastr::success('Patient deleted successfully');
        return back();
        
    } catch (\Exception $e) {
        \Log::error('Error deleting patient: ' . $e->getMessage());
        Toastr::error('Error deleting patient: ' . $e->getMessage());
        return back();
    }
}
    public function search(Request $request){
        $searchKey = $request['search'];
        $customers = DB::table('users')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, uid FROM users WHERE role_id = 2 GROUP BY uid) as sub'), function ($join) {
            $join->on('users.created_at', '=', 'sub.min_created_at')
                 ->on('users.uid', '=', 'sub.uid');
        })
        ->where(function ($q) use ($searchKey) {
            $q->where('name', 'like', "%{$searchKey}%")
                ->orWhere('mobile_number', 'like', "%{$searchKey}%")
                ->orWhere('email', 'like', "%{$searchKey}%");
        })
        
        ->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($customers);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();
        $customers = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        $startIndex = $customers->firstItem() - 1;

        // Set path for pagination links
        $customers->withPath($request->url());
        return response()->json([
            'view'=>view('admin-views.customer.partials._table',compact('customers','startIndex'))->render(),
            'count'=>$customers->count()
        ]);

    }

    

 
   



    public function view($id)
    {
        $customer = User::find($id);
        if (isset($customer)) {

            if(session()->has('customer_order_filter'))
            {
                $request = json_decode(session('customer_order_filter'));
                $status = 'all';

          

                $orders = Order::latest()
            ->when(isset($request->zone), function($query)use($request){
                return $query->whereHas('vendor', function($q)use($request){
                    return $q->whereIn('zone_id',$request->zone);
                });
            })
       
            
            ->when($status == 'ongoing', function($query){
                return $query->ServiceOngoing();
            })
            ->when($status == 'completed', function($query){
                return $query->Completed();
            })
            ->when($status == 'cancelled', function($query){
                return $query->Cancelled();
            })
            ->when($status == 'failed', function($query){
                return $query->failed();
            })
            ->when($status == 'refunded', function($query){
                return $query->Refunded();
            })
    
            ->when($status == 'all', function($query){
                return $query->All();
            })
    
            ->when(isset($request->vendor), function($query)use($request){
                return $query->whereHas('vendor', function($query)use($request){
                    return $query->whereIn('id',$request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function($query)use($request){
                return $query->whereIn('status',$request->orderStatus);
            })
            ->when(isset($request->scheduled) && $status == 'all', function($query){
                return $query->scheduled();
            })
          
            ->when(isset($request->from_date)&&isset($request->to_date)&&$request->from_date!=null&&$request->to_date!=null, function($query)use($request){
                return $query->whereBetween('created_at', [$request->from_date." 00:00:00",$request->to_date." 23:59:59"]);
            })
            ->where(['user_id' => $id])
            ->orderBy('id', 'desc')
            ->paginate(config('default_pagination'));



        $orderstatus = isset($request->orderStatus)?$request->orderStatus:[];
        $from_date =isset($request->from_date)?$request->from_date:null;
        $to_date =isset($request->to_date)?$request->to_date:null;

    
    
    
              //  $orders = Order::latest()->where(['user_id' => $id])->paginate(config('default_pagination'));
                   return view('admin-views.customer.view', compact('customer', 'orders','status','orderstatus','from_date', 'to_date'));
            } else {
                  $orders = Order::latest()->where(['user_id' => $id])->paginate(config('default_pagination'));
                  $status = 'all';
                  $orderstatus = [];
                  $from_date = null;
                  $to_date = null;
            } 

            // Wallet Transactions logic with Pagination
            $perPage = 10;
            $wallet_transactions_paginated = DB::table('wallet_transactions as customer_tx')
                ->where('customer_tx.user_id', $id)
                ->orderBy('customer_tx.created_at', 'desc')
                ->paginate($perPage, ['*'], 'wallet_page');

            // Calculate starting balance for the current page
            // Total balance = current wallet_amount
            // We need to work backwards from the current balance
            $current_balance = $customer->wallet_amount;

            // Subtract/Add all transactions that are MORE RECENT than the first item of the current page
            $more_recent_transactions_sum = DB::table('wallet_transactions')
                ->where('user_id', $id)
                ->where('created_at', '>', $wallet_transactions_paginated->first() ? $wallet_transactions_paginated->first()->created_at : now())
                ->selectRaw("SUM(CASE WHEN is_credit = '1' THEN amount ELSE -amount END) as total")
                ->first()->total ?? 0;

            $running_balance = $current_balance - $more_recent_transactions_sum;

            $wallet_transactions = collect($wallet_transactions_paginated->items())->map(function ($tx) use (&$running_balance) {
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
                } elseif ($tx->is_credit == '1') {
                    $doctorName = 'Self Recharge';
                }

                $tx->doctor_name = $doctorName;
                $tx->current_balance = $running_balance;
                $tx->type = ($tx->is_credit == '1') ? 'Credit' : 'Debit';

                // Update running balance for the NEXT item (going backwards in time)
                if ($tx->is_credit == '1') {
                    $running_balance -= $tx->amount;
                } else {
                    $running_balance += $tx->amount;
                }

                return $tx;
            });

            return view('admin-views.customer.view', compact('customer', 'orders','status','orderstatus','from_date', 'to_date', 'wallet_transactions', 'wallet_transactions_paginated'));

          

         
        }
        Toastr::error(__('messages.customer_not_found'));
        return back();
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
