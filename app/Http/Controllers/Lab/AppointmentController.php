<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Food;
use App\Models\BusinessSetting;
use App\Models\Appointment;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\UserLabTest;
use PDF;
use DB;

class AppointmentController extends Controller
{
   


    public function search(Request $request){
        $doctor_id = auth()->user()->id;
        $key = explode(' ', $request['search']);
        $status = $request->status;
        $search = $request->search;

        $orders = Appointment::query()->where('doctor_id',$doctor_id)
       
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->Where('id', 'like', "%{$value}%");
                // Add the condition to search by customer name
                $q->orWhereHas('customer', function ($subQuery) use ($value) {
                    $subQuery->where('name', 'like', "%{$value}%");
                });
            }
        })
     
        ->limit(50)->get();

        return response()->json([
            'view'=>view('doctor-views.appointment.partials._table',compact('orders'))->render()
        ]);
    }



    public function restaurnt_filter($id)
    {
        session()->put('restaurnt_filter', $id);
        return back();
    }

    public function filter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);
        session()->put('order_filter', json_encode($request->AllAppointments()));
        return back();
    }
    public function filter_reset(Request $request)
    {
        session()->forget('order_filter');
        return back();
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
           $pdf = PDF::setOptions(['debugKeepTemp' => true,'defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true, 'isPhpEnabled' => true])->loadView('doctor-views.appointment.lab-test-order-invoice',compact('order','logoPath'));
          return $pdf->download('Invoice'.'# '.$orderId.'.pdf');
         }
    } 




    public function list(Request $request,$status)
    {
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if(session()->has('order_filter'))
        {
            $request = json_decode(session('order_filter'));
        }
        $doctor_id = auth()->user()->id;

        $orders = Appointment::with(['customer'])->where('doctor_id',$doctor_id)

               
        ->when($status == 'pending', function($query){
            return $query->Pending();
        })
        ->when($status == 'completed', function($query){
            return $query->Completed();
        })
        ->when($status == 'cancelled', function($query){
            return $query->Cancelled();
        })
        ->when($status == 'confirmed', function($query){
            return $query->Confirmed();
        })
        ->when($status == 'rejected', function($query){
            return $query->Rejected();
        })

        ->when($status == 'all', function($query){
            return $query->AllAppointments();
        })


         ->when(isset($request->from_date)&&isset($request->to_date)&&$request->from_date!=null&&$request->to_date!=null, function($query)use($request){
            return $query->whereBetween('created_at', [$request->from_date." 00:00:00",$request->to_date." 23:59:59"]);
        })
        ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
           
            if($request->orderStatus[0] == 'refunded'){
                return $query->where('refunded','yes');
               
            } else {
                return $query->whereIn('status', $request->orderStatus);
            }
            
        })
        
        ->orderBy('id', 'desc')
        ->paginate(config('default_pagination'));
        

        $orderstatus = isset($request->orderStatus)?$request->orderStatus:[];
        $scheduled =isset($request->scheduled)?$request->scheduled:0;
        $vendor_ids =isset($request->vendor)?$request->vendor:[];
        $zone_ids =isset($request->zone)?$request->zone:[];
        $from_date =isset($request->from_date)?$request->from_date:null;
        $to_date =isset($request->to_date)?$request->to_date:null;
        
        
        

        if($status == 'all'){
            $total = Appointment::AllAppointments()->count();  
          }
          
          if($status == 'pending'){
            $total = Appointment::Pending()->count();  
          }
          
          if($status == 'completed'){
            $total = Appointment::Completed()->count();  
          }
          
          if($status == 'confirmed'){
            $total = Appointment::Confirmed()->count();  
          }

          if($status == 'cancelled'){
            $total = Appointment::Cancelled()->count();  
          }
          if($status == 'rejected'){
            $total = Appointment::Rejected()->count();  
          }
         
          
         
  


        return view('doctor-views.appointment.appointment-list', compact('orders', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total'));
    }


    public function addSlot(Request $request){
        return view('doctor-views.appointment.add-slot');
    }

    public function editSlot($id)
    {
        $slot_record = DB::table('doctor_time_slots')->where('id', $id)->first();
        return view('doctor-views.appointment.edit-slot', compact('slot_record'));
        
        
    }


    public function addSlots(Request $request){
       
        $doctorId = auth()->user()->id;
        $dayName = $request->day_name;
        $startTime = date("h:i A",strtotime($request->start_time));
        $endTime = date("h:i A",strtotime($request->end_time));
        $patientLimit = $request->patient_limit;

         // Define the validation rules
         $validationRules = [
            'day_name' => 'required',
        ];
    
        
     

   

        // Validate the input data
      

        $request->validate([
            'day_name' => 'required'

        ]);


     



          // Check if the record already exists
            $existingSlot = DB::table('doctor_time_slots')
            ->where('doctor_id', $doctorId)
            ->where('day_name', $dayName)
            ->where('start_time', $startTime)
            ->where('end_time', $endTime)
            ->first();

        if ($existingSlot) {
            // Update existing record
            Toastr::error('Slot already exists');
            return redirect()->back();
        } else {
            // Insert new record
            DB::table('doctor_time_slots')->insert([
                'doctor_id' => $doctorId,
                'day_name' => $dayName,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'patient_limit' => $patientLimit
                // Add other fields here
            ]);
        }

        Toastr::success('Slot added succesfully');
        return redirect()->back();



   
    }

    public function slotList(Request $request){
        $doctorId = auth()->user()->id;
    
        $slots = DB::table('doctor_time_slots')->where('doctor_id', $doctorId)->orderBy('day_name')->paginate();
        return view('doctor-views.appointment.slot-list', compact('slots'));
    }


    public function updateSlots(Request $request){
       
        $doctorId = auth()->user()->id;
        $dayName = $request->day_name;
        $startTime = date("h:i A",strtotime($request->start_time));
        $endTime = date("h:i A",strtotime($request->end_time));
        $patientLimit = $request->patient_limit;

         // Define the validation rules
         $validationRules = [
            'day_name' => 'required',
        ];
    
        
     

   

        // Validate the input data
      

        $request->validate([
            'day_name' => 'required'

        ]);


     

            DB::table('doctor_time_slots')
            ->where('doctor_id', $doctorId)
            ->where('day_name', $dayName)
            ->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
                'patient_limit' => $patientLimit
            ]);

        Toastr::success('Slot updated succesfully');
        return redirect()->back();



   
    }


    public function manageAppointmentStatus(Request $request){
   
        $appointmentId = $request->segment(3);
   
        $status = $request->segment(4);
        
        $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();

        if(isset($appointmentData) && !empty($appointmentData)){
            DB::table('appointments')->where('id', $appointmentId)->update(['status' => $status]);
            if($status == "confirmed"){
                $message = "Appointment is already confirmed";

            } else {
                $message = "Appointment is already rejected";
            }

            if($appointmentData->status == $status) {
                Toastr::error($message);
                return redirect()->back();

          
            }
        
            if($status == "confirmed"){
                $message = "Appointment confirmed successfully";

            } else {
                $message = "Appointment rejected successfully";
            }
            // send notification to  patient

            $appointmentData = DB::table('appointments')->where('id', $appointmentId)->first();
            Toastr::success($message);
            return redirect()->back();
          
        } else {
            Toastr::error('Invalid appointment id');
            return redirect()->back();
          
        }
    }


    public function deleteSlot($id)
    {
        
        DB::table('doctor_time_slots')
        ->where('id', $id)
        ->delete();

       
         
        Toastr::success('Slot deleted succesfully');
        return back();
    }

}
