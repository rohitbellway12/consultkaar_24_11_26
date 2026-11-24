<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class SMSModuleController extends Controller
{
    public function sms_index()
    {
        return view('admin-views.business-settings.sms-index');
    }

    public function sms_update(Request $request, $module)
    {
      

           
            if ($module == 'clicksend_sms') {
                $config = Helpers::get_business_settings('clicksend_sms');
                DB::table('business_settings')->updateOrInsert(['key' => 'clicksend_sms'], [
                    'key' => 'clicksend_sms',
                    'value' => json_encode([
                        'status' => $request['status'],
                        'template_id_for_verify_new_account' => $request['template_id_for_verify_new_account'],
                        'username' => $request['username'],
                        'template_id_for_verify_account' => $request['template_id_for_verify_account'],
                        'apikey' => $request['apikey'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($module == 'msg91_whatsapp') {
                $request->validate([
                    'authkey' => 'required|string',
                    'flow_id' => 'required|string',
                ]);

                DB::table('business_settings')->updateOrInsert(['key' => 'msg91_whatsapp'], [
                    'key' => 'msg91_whatsapp',
                    'value' => json_encode([
                        'status' => $request['status'] ?? 0,
                        'authkey' => $request['authkey'],
                        'flow_id' => $request['flow_id'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        Toastr::success(trans('messages.settings_updated'));
        return back();
    }
}