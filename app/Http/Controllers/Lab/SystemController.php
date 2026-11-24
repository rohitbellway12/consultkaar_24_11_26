<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\BusinessSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Session;
use App\Models\Lab;


class SystemController extends Controller
{

    public function restaurant_data()
    {
        $new_order = DB::table('orders')->where(['checked' => 0])->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_order' => $new_order]
        ]);
    }

    public function settings()
    {
        
        $labData = Session::get('lab_data');    

        return view('lab-views.settings',array('lab_data'=> $labData));
    }

    public function settings_update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,4}$/',
        ], [
            'name.required' => 'Name is required!',
            'name.string' => 'Name must contain only string!',
            'name.min' => 'Name must contain minimum 3 letters!',
            'name.regex' => 'Name must contain only letters',
        ]);

        $labId = Session::get('lab_id');
        $user = Lab::find($labId);

        if ($request->has('image')) {
            $image_name =Helpers::update('admin/', $user->image, 'png', $request->file('image'));
        } else {
            $image_name = $user['image'];
        }
       

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile_number = $request->phone;
        $user->image = $image_name;
        $user->save();
        Toastr::success(trans('Profile updated successfully'));
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password',
            'confirm_password' => 'required',
        ]);
        $labId = Session::get('lab_id');
        $user =  Lab::find($labId);


        $user->password = bcrypt($request['password']);
        $user->save();
        Toastr::success(trans('Password updated successfully'));
        return back();
    }

    public function maintenance_mode()
    {
        $maintenance_mode = BusinessSetting::where('key', 'maintenance_mode')->first();
        if (isset($maintenance_mode) == false) {
            DB::table('business_settings')->insert([
                'key' => 'maintenance_mode',
                'value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('business_settings')->where(['key' => 'maintenance_mode'])->update([
                'key' => 'maintenance_mode',
                'value' => $maintenance_mode->value == 1 ? 0 : 1,
                'updated_at' => now(),
            ]);
        }

        if (isset($maintenance_mode) && $maintenance_mode->value){
            return response()->json(['message'=>'Maintenance is off.']);
        }
        return response()->json(['message'=>'Maintenance is on.']);
    }
}
