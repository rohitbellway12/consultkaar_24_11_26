<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class HdfcPaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $order = Order::with(['details'])->where(['id' => session('order_id'), 'user_id'=>session('customer_id')])->first();
        $user = session('data');
        $hdfc = Helpers::get_business_settings('hdfc');
        if (!($hdfc && ($hdfc['status']??0)==1)) {
            abort(403,'HDFC disabled');
        }

        $merchantId = $hdfc['merchant_id'];
        $accessCode = $hdfc['access_code'];
        $workingKey = $hdfc['working_key'];
        $environment = $hdfc['environment'] ?? 'test';

        $txnRef = 'hdfc_'.Str::random(16);
        $order->transaction_reference = $txnRef;
        $order->save();

        $redirectUrl = route('hdfc-callback');
        $amount = number_format($order->order_amount, 2, '.', '');

        $payload = http_build_query([
            'merchant_id' => $merchantId,
            'order_id' => $txnRef,
            'amount' => $amount,
            'currency' => Helpers::currency_code(),
            'redirect_url' => $redirectUrl,
            'cancel_url' => $redirectUrl,
            'language' => 'EN',
            'billing_name' => $user['name'] ?? '',
            'billing_tel' => $user['phone'] ?? '',
            'billing_email' => $user['email'] ?? '',
        ]);

        // Simple encryption wrapper (replace with CCAvenue/HDFC library if required)
        $encRequest = base64_encode(openssl_encrypt($payload, 'AES-128-CBC', pack('H*', md5($workingKey)), OPENSSL_ZERO_PADDING, substr(pack('H*', md5($workingKey)), 0, 16)));

        $gateway = $environment==='live' ? 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction' : 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

        return view('payments.hdfc-redirect', compact('gateway','encRequest','accessCode'));
    }

    public function callback(Request $request)
    {
        $hdfc = Helpers::get_business_settings('hdfc');
        $workingKey = $hdfc['working_key'];
        $encResp = $request->input('encResp');

        $decrypted = openssl_decrypt(base64_decode($encResp), 'AES-128-CBC', pack('H*', md5($workingKey)), OPENSSL_ZERO_PADDING, substr(pack('H*', md5($workingKey)), 0, 16));
        parse_str($decrypted, $resp);

        $order = Order::where('transaction_reference', $resp['order_id'] ?? '')->first();
        if (!$order) {
            return \redirect()->route('payment-fail');
        }

        $status = $resp['order_status'] ?? '';
        if (strtolower($status) === 'success') {
            $order->payment_method = 'hdfc';
            $order->payment_status = 'paid';
            $order->order_status = 'confirmed';
            $order->confirmed = now();
            $order->save();
            if ($order->callback != null) {
                return redirect($order->callback . '&status=success');
            }
            return \redirect()->route('payment-success');
        }

        $order->order_status = 'failed';
        $order->failed = now();
        $order->save();
        if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }
        return \redirect()->route('payment-fail');
    }
}


