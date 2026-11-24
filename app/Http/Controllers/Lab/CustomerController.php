<?php

namespace App\Http\Controllers\Doctor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\UserPassword;
use App\Models\UsersAddress;

class CustomerController extends Controller
{

    function list()
    {
        $doctorId = auth()->user()->id;

        $user_list = Appointment::where('doctor_id', $doctorId)
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('appointments as a2')
                    ->whereRaw('a2.patient_mobile_number = appointments.patient_mobile_number')
                    ->whereRaw('a2.id > appointments.id');
            })
            ->latest()
            ->paginate(config('default_pagination'));
            return view('doctor-views.customer.list', compact('user_list'));

    }

   
  
    public function search(Request $request){

        $key = explode(' ', $request['search']);

        $doctorId = auth()->user()->id;

        $customers = Appointment::where('doctor_id', $doctorId)
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('patent_name', 'like', "%{$value}%");
                    $q->orWhere('patient_mobile_number', 'like', "%{$value}%");
                }
            })
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('appointments as a2')
                    ->whereRaw('a2.patient_mobile_number = appointments.patient_mobile_number')
                    ->whereRaw('a2.id > appointments.id');
            })
            ->latest()
            ->paginate(config('default_pagination'));

return response()->json([
    'view' => view('doctor-views.customer.partials._table', compact('customers'))->render(),
    'count' => $customers->count()
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
                   return view('doctor-views.customer.customer-view', compact('customer', 'orders','status','orderstatus','from_date', 'to_date'));
            } else {
                  $orders = Order::latest()->where(['user_id' => $id])->paginate(config('default_pagination'));
                  $status = 'all';
                  $orderstatus = [];
                  $from_date = null;
                  $to_date = null;
                return view('doctor-views.customer.customer-view', compact('customer', 'orders','status','orderstatus','from_date', 'to_date'));
            } 

          

         
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
