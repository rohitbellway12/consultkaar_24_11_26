<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Zone;
use App\Models\Product;
use App\Models\DeliveryManWallet;
use App\Models\DeliveryMan;
use App\Models\Category;
use App\Models\Food;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use App\Models\UserLabTest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PDF;

class OrderController extends Controller
{
//   public function list(Request $request, $status)
// {
//     if (!session()->has('zone_filter')) {
//         session()->put('zone_filter', 0);
//     }

//     if (session()->has('order_filter')) {
//         $request = json_decode(session('order_filter'));
//     }

//     // Get authenticated vendor ID
//     $authVendorId = Auth::guard('vendor')->id();


//     $orders = Order::with(['customer'])
//         ->where('vendor_id', $authVendorId) // Filter orders by authenticated vendor
//         ->when(isset($request->zone), function ($query) use ($request) {
//             return $query->whereHas('vendor', function ($q) use ($request) {
//                 return $q->whereIn('zone_id', $request->zone);
//             });
//         })
//         ->when($status == 'ongoing', function ($query) {
//             return $query->ServiceOngoing();
//         })
//         ->when($status == 'delivered', function ($query) {
//             return $query->Completed();
//         })
//         ->when($status == 'cancelled', function ($query) {
//             return $query->Cancelled();
//         })
//         ->when($status == 'failed', function ($query) {
//             return $query->failed();
//         })
//         ->when($status == 'refunded', function ($query) {
//             return $query->Refunded();
//         })
//         ->when($status == 'all', function ($query) {
//             return $query->All();
//         })
//         ->when(isset($request->vendor), function ($query) use ($request) {
//             return $query->whereHas('vendor', function ($query) use ($request) {
//                 return $query->whereIn('id', $request->vendor);
//             });
//         })
//         ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
//             return $query->whereIn('status', $request->orderStatus);
//         })
//         ->when(isset($request->scheduled) && $status == 'all', function ($query) {
//             return $query->scheduled();
//         })
//         ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
//             return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
//         })
//         ->orderBy('id', 'desc')
//         ->paginate(config('default_pagination'));

//     $orderstatus = $request->orderStatus ?? [];
//     $scheduled = $request->scheduled ?? 0;
//     $vendor_ids = $request->vendor ?? [];
//     $zone_ids = $request->zone ?? [];
//     $from_date = $request->from_date ?? null;
//     $to_date = $request->to_date ?? null;

//     // Get total count for different statuses
//     if ($status == 'all') {
//         $total = Order::where('vendor_id', $authVendorId)->count();  
//     } elseif ($status == 'ongoing') {
//         $total = Order::where('vendor_id', $authVendorId)->ServiceOngoing()->count();
//     } elseif ($status == 'delivered') {
//         $total = Order::where('vendor_id', $authVendorId)->Completed()->count();
//     } elseif ($status == 'cancelled') {
//         $total = Order::where('vendor_id', $authVendorId)->Cancelled()->count();
//     } elseif ($status == 'refunded') {
//         $total = Order::where('vendor_id', $authVendorId)->Refunded()->count();
//     } elseif ($status == 'failed') {
//         $total = Order::where('vendor_id', $authVendorId)->failed()->count();
//     } else {
//         $total = $orders->total();
//     }

//     return view('vendor-views.order.list', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total'));
// }
public function list(Request $request, $status)
{
    if (!session()->has('zone_filter')) {
        session()->put('zone_filter', 0);
    }

    // Get filters from session if available
    $filterRequest = $request;
    if (session()->has('order_filter')) {
        $filterRequest = new \Illuminate\Http\Request((array) json_decode(session('order_filter')));
    }

    $authVendorId = Auth::guard('vendor')->id();
    $search = $request->search ?? null; // search comes from URL, keep original $request

    $orders = Order::with(['customer'])
        ->where('vendor_id', $authVendorId)
        ->when(isset($filterRequest->zone), function ($query) use ($filterRequest) {
            return $query->whereHas('vendor', function ($q) use ($filterRequest) {
                return $q->whereIn('zone_id', $filterRequest->zone);
            });
        })
        ->when($status == 'ongoing', fn($query) => $query->ServiceOngoing())
        ->when($status == 'delivered', fn($query) => $query->Completed())
        ->when($status == 'cancelled', fn($query) => $query->Cancelled())
        ->when($status == 'failed', fn($query) => $query->failed())
        ->when($status == 'refunded', fn($query) => $query->Refunded())
        ->when($status == 'all', fn($query) => $query->All())
        ->when(isset($filterRequest->vendor), function ($query) use ($filterRequest) {
            return $query->whereHas('vendor', fn($q) => $q->whereIn('id', $filterRequest->vendor));
        })
        ->when(isset($filterRequest->orderStatus) && $status == 'all', function ($query) use ($filterRequest) {
            return $query->whereIn('status', $filterRequest->orderStatus);
        })
        ->when(isset($filterRequest->scheduled) && $status == 'all', fn($query) => $query->scheduled())
        ->when(isset($filterRequest->from_date) && isset($filterRequest->to_date), function ($query) use ($filterRequest) {
            return $query->whereBetween('created_at', [
                $filterRequest->from_date . " 00:00:00",
                $filterRequest->to_date . " 23:59:59"
            ]);
        })
        ->when(!empty($search), function ($query) use ($search) {
            $keywords = explode(' ', $search);
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('order_id', 'like', "%{$keyword}%")
                      ->orWhereHas('customer', fn($sub) => $sub->where('name', 'like', "%{$keyword}%"));
                }
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(config('default_pagination'))
        ->appends(array_merge($filterRequest->all(), ['search' => $search]));

    // Filters for the view
    $orderstatus = $filterRequest->orderStatus ?? [];
    $scheduled = $filterRequest->scheduled ?? 0;
    $vendor_ids = $filterRequest->vendor ?? [];
    $zone_ids = $filterRequest->zone ?? [];
    $from_date = $filterRequest->from_date ?? null;
    $to_date = $filterRequest->to_date ?? null;

    // Get total count for summary
    $total = match ($status) {
        'all' => Order::where('vendor_id', $authVendorId)->count(),
        'ongoing' => Order::where('vendor_id', $authVendorId)->ServiceOngoing()->count(),
        'delivered' => Order::where('vendor_id', $authVendorId)->Completed()->count(),
        'cancelled' => Order::where('vendor_id', $authVendorId)->Cancelled()->count(),
        'refunded' => Order::where('vendor_id', $authVendorId)->Refunded()->count(),
        'failed' => Order::where('vendor_id', $authVendorId)->failed()->count(),
        default => $orders->total(),
    };

    return view('vendor-views.order.list', compact(
        'orders', 'status', 'orderstatus', 'scheduled',
        'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total'
    ));
}

    
    public function dispatch_list($status, Request $request)
    {

        if(session()->has('order_filter'))
        {
            $request = json_decode(session('order_filter'));
            $zone_ids = isset($request->zone)?$request->zone:0;
        }
        
        Order::where(['checked' => 0])->update(['checked' => 1]);
        
        $orders = Order::with(['customer', 'restaurant'])
        ->when(isset($request->zone), function($query)use($request){
            return $query->whereHas('restaurant', function($query)use($request){
                return $query->whereIn('zone_id',$request->zone);
            });
        })
        ->when($status == 'searching_for_deliverymen', function($query){
            return $query->SearchingForDeliveryman();
        })
        ->when($status == 'ongoing', function($query){
            return $query->Ongoing();
        })
        ->when(isset($request->vendor), function($query)use($request){
            return $query->whereHas('restaurant', function($query)use($request){
                return $query->whereIn('id',$request->vendor);
            });
        })
        ->when(isset($request->from_date)&&isset($request->to_date)&&$request->from_date!=null&&$request->to_date!=null, function($query)use($request){
            return $query->whereBetween('created_at', [$request->from_date." 00:00:00",$request->to_date." 23:59:59"]);
        })
      
        ->OrderScheduledIn(30)
        ->orderBy('schedule_at', 'desc')
        ->paginate(config('default_pagination'));

        $orderstatus = isset($request->orderStatus)?$request->orderStatus:[];
        $scheduled =isset($request->scheduled)?$request->scheduled:0;
        $vendor_ids =isset($request->vendor)?$request->vendor:[];
        $zone_ids =isset($request->zone)?$request->zone:[];
        $from_date =isset($request->from_date)?$request->from_date:null;
        $to_date =isset($request->to_date)?$request->to_date:null;
        $total = $orders->total();

        return view('admin-views.order.distaptch_list', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total'));
    }

    public function details(Request $request, $id)
    {
        $order = Order::where(['id' => $id])->first();

        if (isset($order)) {


            $category = $request->query('category_id', 0);
            // $sub_category = $request->query('sub_category', 0);
            $categories = Category::active()->get();
            $keyword = $request->query('keyword', false);
            $key = explode(' ', $keyword);
            $products = Product::
            when($category, function($query)use($category){
                $query->whereHas('category',function($q)use($category){
                    return $q->whereId($category)->orWhere('parent_id', $category);
                });
            })
            ->when($keyword, function($query)use($key){
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate(10);
            $editing=false;
            if($request->session()->has('order_cart'))
            {
                $cart = session()->get('order_cart');
                if(count($cart)>0 && $cart[0]->order_id == $order->id)
                {
                    $editing=true;
                }
                else
                {
                    session()->forget('order_cart');
                }
                
            }
            
            // $deliveryMen=Helpers::deliverymen_list_formatting($deliveryMen);
            return view('vendor-views.order.order-view', compact('order','categories', 'products','category', 'keyword', 'editing'));
        } else {
            Toastr::info(trans('messages.no_more_orders'));
            return back();
        }
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $status = $request->status;
        $search = $request->search;

        $orders = Order::query()
        ->when($status == 'pending', function($query){
            return $query->Pending();
        })
        ->when($status == 'accepted', function($query){
            return $query->Accepted();
        })
        ->when($status == 'processing', function($query){
            return $query->Preparing();
        })
        ->when($status == 'services_ongoing', function($query){
            return $query->ServiceOngoing();
        })
        ->when($status == 'completed', function($query){
            return $query->Delivered();
        })
        ->when($status == 'canceled', function($query){
            return $query->Canceled();
        })
        ->when($status == 'failed', function($query){
            return $query->failed();
        })
        ->when($status == 'refunded', function($query){
            return $query->Refunded();
        })
        ->when($status == 'scheduled', function($query){
            return $query->Scheduled();
        })
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->Where('order_id', 'like', "%{$value}%");
                // Add the condition to search by customer name
                $q->orWhereHas('customer', function ($subQuery) use ($value) {
                    $subQuery->where('name', 'like', "%{$value}%");
                });
            }
        })
     
        ->paginate(config('default_pagination'));


        return response()->json([
            'view'=>view('admin-views.order.partials._table',compact('orders'))->render()
        ]);
    }



    



    public function status(Request $request)
    {
        $order = Order::find($request->id);
        

        
        if($request->status == 'completed'){
            if($order->pending_amount > 0){
                Toastr::warning('You cannot change status of order as completed until customer have not paid pending amount');
                return back(); 
            }
            
            $order_description = "Your order has been completed";
            
        }
        
        if($request->status == 'cancelled'){
            $order_description = "Your order has been cancelled";
        }

        
        $order->status = $request->status;
        // $order->description = $order_description;
        $order->save();        
      
        Toastr::success(trans('messages.order').trans('messages.status_updated'));
        return back();
    }

 



    public function generate_invoice($id)
    {
        $order = Order::where('id', $id)->first();
        return view('vendor-views.order.invoice', compact('order'));
    }


    public function generateInvoiceForLabTest($id)
    {
        $order = UserLabTest::where('id', $id)->first();
        return view('admin-views.order.lab-test-invoice', compact('order'));

    }

    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success(trans('messages.payment_reference_code_is_added'));
        return back();
    }

    public function restaurnt_filter($id)
    {
        session()->put('restaurnt_filter', $id);
        return back();
    }

    public function medicionfilter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);
        session()->put('order_filter', json_encode($request->all()));
        return back();
    }
    public function medicionfilter_reset(Request $request)
    {
        session()->forget('order_filter');
        return back();
    }








    public function quick_view(Request $request)
    {

        $product = $product = Food::findOrFail($request->product_id);
        $item_type = 'food';
        $order_id = $request->order_id;
        
        return response()->json([
            'success' => 1,
            'view' => view('admin-views.order.partials._quick-view', compact('product', 'order_id','item_type'))->render(),
        ]);
    }



    function downloadInvoice(Request $request,$orderId){
         
        $order = Order::where('id',$orderId)->first();
        if (isset($order)) { 
           $businessSettingData = BusinessSetting::where('key','logo')->first(); 
           $businessSettingLogoPath = (isset($businessSettingData) && !empty($businessSettingData)) ? "/storage/app/public/business/".$businessSettingData->value : '';
           
          
           $path = base_path($businessSettingLogoPath);;
           $type = pathinfo($path,PATHINFO_EXTENSION);
          
           $data = file_get_contents($path);
           $logoPath = 'data:image/'. $type .';base64,'. base64_encode($data);
           $pdf = PDF::setOptions(['debugKeepTemp' => true,'defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true, 'isPhpEnabled' => true])->loadView('vendor-views.order.order-invoice',compact('order','logoPath'));
          return $pdf->download('Invoice'.'# '.$orderId.'.pdf');
         }
    } 


    function downloadInvoiceForLabTest(Request $request,$orderId){
         
        $order = UserLabTest::where('id',$orderId)->first();
        if (isset($order)) { 
           $businessSettingData = BusinessSetting::where('key','logo')->first(); 
           $businessSettingLogoPath = (isset($businessSettingData) && !empty($businessSettingData)) ? "/storage/app/public/business/".$businessSettingData->value : '';
           
          
           $path = base_path($businessSettingLogoPath);;
           $type = pathinfo($path,PATHINFO_EXTENSION);
           $data = file_get_contents($path);
           $logoPath = 'data:image/'. $type .';base64,'. base64_encode($data);
           $pdf = PDF::setOptions(['debugKeepTemp' => true,'defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true, 'isPhpEnabled' => true])->loadView('admin-views.order.lab-test-order-invoice',compact('order','logoPath'));

          return $pdf->download('Invoice'.'# '.$orderId.'.pdf');
         }
    } 




    public function labTestBookinglist(Request $request)
    {
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if(session()->has('order_filter'))
        {
            $request = json_decode(session('order_filter'));
        }
        

        $orders = UserLabTest::with(['customer'])
        ->when(isset($request->zone), function($query)use($request){
            return $query->whereHas('vendor', function($q)use($request){
                return $q->whereIn('zone_id',$request->zone);
            });
        })

        
    
    
    
        ->when(isset($request->from_date)&&isset($request->to_date)&&$request->from_date!=null&&$request->to_date!=null, function($query)use($request){
            return $query->whereBetween('created_at', [$request->from_date." 00:00:00",$request->to_date." 23:59:59"]);
        })
        
        ->orderBy('id', 'desc')
        ->paginate(config('default_pagination'));


        $orderstatus = isset($request->orderStatus)?$request->orderStatus:[];
        $scheduled =isset($request->scheduled)?$request->scheduled:0;
        $vendor_ids =isset($request->vendor)?$request->vendor:[];
        $zone_ids =isset($request->zone)?$request->zone:[];
        $from_date =isset($request->from_date)?$request->from_date:null;
        $to_date =isset($request->to_date)?$request->to_date:null;
    
        
        

        $total = $orders->total();

        $labs =  DB::table('labs')->where('lang_code','en')->orderBy('id','desc')->get();



        return view('admin-views.order.lab-test-booking-list', compact('orders', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total','labs'));
    }

    public function labTestBookingDetail(Request $request)
    {
        $id = $request->get('id');
        $order = UserLabTest::where(['id' => $id])->first();

        if (isset($order)) {


            $category = $request->query('category_id', 0);
            // $sub_category = $request->query('sub_category', 0);
            $categories = Category::active()->get();
            $keyword = $request->query('keyword', false);
            $key = explode(' ', $keyword);
            $products = Product::
            when($category, function($query)use($category){
                $query->whereHas('category',function($q)use($category){
                    return $q->whereId($category)->orWhere('parent_id', $category);
                });
            })
            ->when($keyword, function($query)use($key){
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate(10);
            $editing=false;
            if($request->session()->has('order_cart'))
            {
                $cart = session()->get('order_cart');
                if(count($cart)>0 && $cart[0]->order_id == $order->id)
                {
                    $editing=true;
                }
                else
                {
                    session()->forget('order_cart');
                }
                
            }
            
            // $deliveryMen=Helpers::deliverymen_list_formatting($deliveryMen);
            return view('admin-views.order.lab-test-booking-view', compact('order','categories', 'products','category', 'keyword', 'editing'));
        } else {
            Toastr::info(trans('messages.no_more_orders'));
            return back();
        }
    }


    public function searchLabTest(Request $request){
        $key = explode(' ', $request['search']);
        $status = $request->status;
        $search = $request->search;

        $orders = UserLabTest::with(['customer'])
        
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->Where('booking_id', 'like', "%{$value}%");
                // Add the condition to search by customer name
                $q->orWhereHas('customer', function ($subQuery) use ($value) {
                    $subQuery->where('name', 'like', "%{$value}%");
                });
            }
        })
     
        ->limit(50)->get();

        return response()->json([
            'view'=>view('admin-views.order.partials._lab_test_table',compact('orders'))->render()
        ]);
    }


    public function assignLab(Request $request){

        $bookingId = $request->booking_id;
        $labId     = $request->lab_id;  
        $bookingData = DB::table('users_lab_tests')->where('id',$bookingId)->first();
        // Retrieve the booking data

        // Check if the booking data exists
        if ($bookingData) {
            $userId = $bookingData->user_id;

            $isLabAssignedToUser = DB::table('users_lab_tests')->where(['id' => $bookingId,'user_id' => $userId,'lab_id' => $labId])->count();
            if($isLabAssignedToUser > 0){
                return response()->json(['status' => 'error', 'code' => 200,'message' => 'Lab is already assigned to user']);

            }
            // Perform the update operation and get the number of affected rows
            $affectedRows = DB::table('users_lab_tests')->where('id', $bookingId)->update(['lab_id' => $labId]);

            // Check if the update was successful
            if ($affectedRows > 0) {
                // Retrieve the necessary user and admin data
                
                $customerData = User::find($userId);
                $adminData = User::where('role_id', 1)->first();

                // Insert a new notification
                DB::table('notifications')->insert([
                    'title' => "Lab assigned",
                    'from_user_id' => $adminData->id,
                    'to_user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json(['status' => 'success', 'code' => 200,'message' => 'Lab assigned successfully']);


                // Success message or further processing
                echo "Lab assigned and notification sent successfully.";
            } else {
                // Handle the failure case
                return response()->json(['status' => 'error', 'code' => 200,'message' => 'Failed to update']);

            }
        }


    }

}