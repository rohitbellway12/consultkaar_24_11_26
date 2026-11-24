<?php

namespace App\CentralLogics;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Nexmo\Laravel\Facade\Nexmo;
use Twilio\Rest\Client;

class SMS_module
{
    public static function send($receiver, $otp, $templateId = null)
    {
        // $config = self::get_settings('twilio_sms');
        // if (isset($config) && $config['status'] == 1) {
        //     $response = self::twilio($receiver, $otp);
        //     return $response;
        // }

        // $config = self::get_settings('nexmo_sms');
        // if (isset($config) && $config['status'] == 1) {
        //     $response = self::nexmo($receiver, $otp);
        //     return $response;
        // }

        // $config = self::get_settings('2factor_sms');
        // if (isset($config) && $config['status'] == 1) {
        //     $response = self::two_factor($receiver, $otp);
        //     return $response;
        // }

        // $config = self::get_settings('msg91_sms');
        // if (isset($config) && $config['status'] == 1) {
            $response = self::clicksend($receiver, $otp, $templateId);
            return $response;
        // }

        // return 'not_found';
    }

    public static function twilio($receiver, $otp)
    {
        $config = self::get_settings('twilio_sms');
        $response = 'error';

        if (isset($config) && $config['status'] == 1) {
            $message = str_replace("#OTP#", $otp, $config['otp_template']);
            $sid = $config['sid'];
            $token = $config['token'];
            try {
                $twilio = new Client($sid, $token);
                $twilio->messages
                    ->create($receiver, // to
                        array(
                           "from" => $config['from'],
                            "body" => $message
                        )
                    );
                $response = 'success';
            } catch (\Exception $exception) {
                $response = 'error';
            }
        } elseif (empty($config)) {
            DB::table('business_settings')->updateOrInsert(['key' => 'twilio_sms'], [
                'key' => 'twilio_sms',
                'value' => json_encode([
                    'status' => 0,
                    'sid' => '',
                    'token' => '',
                    'from' => '',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $response;
    }

    public static function nexmo($receiver, $otp)
    {
        $sms_nexmo = self::get_settings('nexmo_sms');
        $response = 'error';
        if (isset($sms_nexmo) && $sms_nexmo['status'] == 1) {
            $message = str_replace("#OTP#", $otp, $sms_nexmo['otp_template']);
            try {
                $config = [
                    'api_key' => $sms_nexmo['api_key'],
                    'api_secret' => $sms_nexmo['api_secret'],
                    'signature_secret' => '',
                    'private_key' => '',
                    'application_id' => '',
                    'app' => ['name' => '', 'version' => ''],
                    'http_client' => ''
                ];
                Config::set('nexmo', $config);
                Nexmo::message()->send([
                    'to' => $receiver,
                    'from' => $sms_nexmo['from'],
                    'text' => $message
                ]);
                $response = 'success';
            } catch (\Exception $exception) {
                $response = 'error';
            }
        } elseif (empty($config)) {
            DB::table('business_settings')->updateOrInsert(['key' => 'nexmo_sms'], [
                'key' => 'nexmo_sms',
                'value' => json_encode([
                    'status' => 0,
                    'api_key' => '',
                    'api_secret' => '',
                    'signature_secret' => '',
                    'private_key' => '',
                    'application_id' => '',
                    'from' => '',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $response;
    }

    public static function two_factor($receiver, $otp)
    {
        $config = self::get_settings('2factor_sms');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $api_key = $config['api_key'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://2factor.in/API/V1/" . $api_key . "/SMS/" . $receiver . "/" . $otp . "",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if (!$err) {
                $response = 'success';
            } else {
                $response = 'error';
            }
        } elseif (empty($config)) {
            DB::table('business_settings')->updateOrInsert(['key' => '2factor_sms'], [
                'key' => '2factor_sms',
                'value' => json_encode([
                    'status' => 0,
                    'api_key' => 'aabf4e9c-f55f-11eb-85d5-0200cd936042',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $response;
    }

    public static function msg_91($receiver, $otp, $templateId = null)
    {
        $config = self::get_settings('msg91_sms');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            
            $receiver = str_replace("+", "", $receiver);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=" . $templateId . "&mobile=" . $receiver . "&authkey=" . $config['authkey'] . "",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{\"OTP\":\"$otp\"}",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) {
                $response = 'success';
            } else {
                $response = 'error';
            }
        } elseif (empty($config)) {
            DB::table('business_settings')->updateOrInsert(['key' => 'msg91_sms'], [
                'key' => 'msg91_sms',
                'value' => json_encode([
                    'status' => 0,
                    'template_id' => '',
                    'authkey' => '',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $response;
    }


    public static function clicksend($receiver, $otp, $templateId = null){
        $config = self::get_settings('clicksend_sms');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            
            $userName = $config['username'];
            $key = $config['apikey'];
            

          //  $templateData = self::getAllSMSTemplates($userName,$key);
       
            $templateData = self::getSMSTemplateData($userName,$key, $templateId);
        //    echo "<pre>";
            
        //    print_r($templateData);

            if($templateData['response_code'] == 'SUCCESS'){
                    $message_template = $templateData['data']['body'];

                    $message_template = str_replace('#OTP',$otp,$message_template);


            // Message data
            $message_data = array(
                'messages' => array(
                    array(
                        'source' => 'php',
                        'from' => 'YourSenderID',
                        'body' => $message_template,
                        'to' => $receiver
                    )
                )
            );

                // Convert message data to JSON format
                $json_message = json_encode($message_data);

                // Initialize cURL session
                $ch = curl_init();

                // Set cURL options
                curl_setopt($ch, CURLOPT_URL, 'https://rest.clicksend.com/v3/sms/send');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_message);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Basic ' . base64_encode("$userName:$key")
                ));

                // Execute cURL session
                $response = curl_exec($ch);

                // Close cURL session
                curl_close($ch);

                // Decode the JSON response
                $response_data = json_decode($response, true);

                // Check if the request was successful
                if ($response_data['response_code'] == 'SUCCESS') {
                    return 1;
                } else {
                    return 0;
                }
                    
            } else {
                return 0;
            }
        



        }
        return $response;
    }


    public static function getAllSMSTemplates($userName,$key)
    {
        // Initialize cURL session
            $ch = curl_init();

            // Set cURL options to list templates
            curl_setopt($ch, CURLOPT_URL, 'https://rest.clicksend.com/v3/sms/templates');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic ' . base64_encode("$userName:$key")
            ));

            // Execute cURL session
            $response = curl_exec($ch);

            // Close cURL session
            curl_close($ch);

            // Decode the JSON response
            $response_data = json_decode($response, true);

            echo "<pre>";
            print_r($response_data);
            die;


    }


    public static function getSMSTemplateData($userName,$key,$templateId)
    {
        // Initialize cURL session
            $ch = curl_init();

            // Set cURL options to list templates
            curl_setopt($ch, CURLOPT_URL, 'https://rest.clicksend.com/v3/sms/templates/' . $templateId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic ' . base64_encode("$userName:$key")
            ));

            // Execute cURL session
            $response = curl_exec($ch);

            // Close cURL session
            curl_close($ch);

            // Decode the JSON response
            $response_data = json_decode($response, true);

           return $response_data;


    }


   

    public static function get_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }
}
