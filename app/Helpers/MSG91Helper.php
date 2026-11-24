<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use App\CentralLogics\SMS_module;

class MSG91Helper
{
    public static function sendWhatsAppOTP($mobile, $otp)
    {
        // Prefer credentials from Business Settings (admin panel), fallback to .env
        $settings = SMS_module::get_settings('msg91_whatsapp');
        $authKey = is_array($settings) && !empty($settings['authkey']) ? $settings['authkey'] : env('MSG91_AUTH_KEY');
        $flowId  = is_array($settings) && !empty($settings['flow_id'])  ? $settings['flow_id']  : env('MSG91_WHATSAPP_FLOW_ID');

        if (empty($authKey) || empty($flowId)) {
            return false;
        }

        $formattedMobile = preg_replace('/[^0-9]/', '', $mobile);

        $payload = [
            "flow_id" => $flowId,
            "recipients" => [
                [
                    "mobiles" => $formattedMobile,
                    "VAR1" => $otp // This matches your template variable
                ]
            ]
        ];

        $response = Http::withHeaders([
            "authkey" => $authKey,
            "Content-Type" => "application/json"
        ])->post("https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/", $payload);

        return $response->successful();
    }
}
