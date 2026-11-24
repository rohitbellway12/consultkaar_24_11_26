<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Order;
use App\Models\UsersAddress;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;


class CustomerController extends Controller
{

    public function add_new()
    {
        return view('admin-views.customer.add-new');
    }


    function list(Request $request)
    {
        $user_list = DB::table('users')
        ->select('*')
        ->join(DB::raw('(SELECT MIN(created_at) as min_created_at, uid FROM users WHERE role_id = 2 GROUP BY uid) as sub'), function ($join) {
            $join->on('users.created_at', '=', 'sub.min_created_at')
                 ->on('users.uid', '=', 'sub.uid');
        })->orderBy('created_at','desc')->get();
        // Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Change this as needed
        $items = collect($user_list);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();
        $user_list = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        $startIndex = $user_list->firstItem() - 1;

        // Set path for pagination links
        $user_list->withPath($request->url());
        return view('admin-views.customer.list', compact('user_list', 'startIndex'));
    }

    public function distroy($id)
    {
         User::where(['id'=>$id])->delete();
         // delete user's booking data
         
         Order::where(['user_id'=>$id])->delete();
       
         
          // delete user's address data 
          
         UsersAddress::where(['user_id'=>$id])->delete();
         
        Toastr::success('Patient deleted succesfully');
        return back();
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

    if (!$customer) {
        Toastr::error(__('messages.customer_not_found'));
        return back();
    }

    // ✅ Ensure the authenticated vendor can only access their own orders
    $vendorId = Auth::guard('vendor')->id();

    if (!$vendorId) {
        Toastr::error(__('messages.unauthorized_access'));
        return back();
    }

    $status = 'all';
    $orderstatus = [];
    $from_date = null;
    $to_date = null;

    // ✅ Check if session filter exists
    if (session()->has('customer_order_filter')) {
        $request = json_decode(session('customer_order_filter'));
        $status = 'all';

        $orders = Order::latest()
            ->where('vendor_id', $vendorId)  // ✅ Filter orders by vendor_id
            ->where('user_id', $id)          // ✅ Filter by customer ID
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('vendor', function ($q) use ($request) {
                    return $q->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'ongoing', fn($query) => $query->ServiceOngoing())
            ->when($status == 'completed', fn($query) => $query->Completed())
            ->when($status == 'cancelled', fn($query) => $query->Cancelled())
            ->when($status == 'failed', fn($query) => $query->Failed())
            ->when($status == 'refunded', fn($query) => $query->Refunded())
            ->when($status == 'all', fn($query) => $query->All())
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('vendor', function ($q) use ($request) {
                    return $q->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                return $query->whereIn('status', $request->orderStatus);
            })
            ->when(isset($request->scheduled) && $status == 'all', fn($query) => $query->scheduled())
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->orderBy('id', 'desc')
            ->paginate(config('default_pagination'));

        $orderstatus = $request->orderStatus ?? [];
        $from_date = $request->from_date ?? null;
        $to_date = $request->to_date ?? null;

        return view('vendor-views.customer.customer-view', compact('customer', 'orders', 'status', 'orderstatus', 'from_date', 'to_date'));
    }

    // ✅ Default case when no filter is applied
    $orders = Order::latest()
        ->where('vendor_id', $vendorId)
        ->where('user_id', $id)
        ->paginate(config('default_pagination'));

    return view('vendor-views.customer.customer-view', compact('customer', 'orders', 'status', 'orderstatus', 'from_date', 'to_date'));
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
