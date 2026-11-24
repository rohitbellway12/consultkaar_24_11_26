<?php

namespace App\Http\Controllers\Doctor;

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
use Illuminate\Support\Facades\Auth;


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

        $doctorData = Auth::user();
        $doctorId   = $doctorData->id;

        $user = User::find($doctorId);
        return view('doctor-views.settings',array('admin'=> $user));
    }

    public function settings_update(Request $request)
    {
       
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => [
        'required',
        'string',
        'email',
        'max:255',
        'unique:users',
        'regex:/^\w+[-\.\w]*@(?!(?:outlook|myemail|yahoo)\.com$)\w+[-\.\w]*?\.\w{2,4}$/'
    ],
            'phone' => 'required',
        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
            'email.regex' => 'We do not accept mails with this domain.'
        ]);

        $doctorId = auth()->user()->id;
        $user = User::find($doctorId);

        if ($request->has('image')) {
            $image_name =Helpers::update('user/', $user->image, 'png', $request->file('image'));
        } else {
            $image_name = $user['image'];
        }


        $user->name = $request->f_name . " " . $request->l_name;
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
        $doctorId = auth()->user()->id;
        $user = User::find($doctorId);


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
