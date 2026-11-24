<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\BusinessSetting;
use App\Models\User;
use App\Models\Payment;
use PHPUnit\Exception;


class StripePaymentController extends Controller
{
    public function payment_process_3d()
    {
        $tran = Str::random(6) . '-' . rand(1, 1000);
        $order_id = session('order_id');
        session()->put('transaction_ref', $tran);
        $order = Order::with(['details'])->where(['id' => $order_id])->first();
        $config = Helpers::get_business_settings('stripe');
        Stripe::setApiKey($config['api_key']);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = url('/');

        

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => Helpers::currency_code(),
                    'unit_amount' => $order->order_amount * 100,
                    'product_data' => [
                        'name' => BusinessSetting::where(['key' => 'business_name'])->first()->value,
                        'images' => [asset('storage/app/public/business') . '/' . BusinessSetting::where(['key' => 'logo'])->first()->value],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/pay-stripe/success',
            'cancel_url' => url()->previous(),
        ]);

        return response()->json(['id' => $checkout_session->id]);
    }

    public function success()
    {
        $order = Order::find(session('order_id'));
        $order->order_status='confirmed';
        $order->payment_method='stripe';
        $order->transaction_reference=session('transaction_ref');
        $order->payment_status='paid';
        $order->confirmed=now();
        $order->save();
        try {
            Helpers::send_order_notification($order);
        } catch (\Exception $e) {

        }

        if ($order->callback != null) {
            return redirect($order->callback . '&status=success');
        }

        return \redirect()->route('payment-success');
    }

    public function fail()
    {
        DB::table('orders')
        ->where('id', session('order_id'))
        ->update(['order_status' => 'failed',  'payment_status' => 'unpaid', 'failed'=>now()]);
        $order = Order::find(session('order_id'));
        if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }
        return \redirect()->route('payment-fail');
    }

    public function payment_process_3d_for_amount()
    {
        $tran = Str::random(6) . '-' . rand(1, 1000);
        $order_id = session('order_id');
        session()->put('transaction_ref', $tran);
        
        $config = Helpers::get_business_settings('stripe');
        Stripe::setApiKey($config['api_key']);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = url('/');

        $pay_for = session('pay_for');
        
        if($pay_for == 'medicine'){
            $order = Order::where(['id' => $order_id])->first();
            $order_amount = $order->amount;
        } 
        
        
        
        if($pay_for == 'lab_test'){
            $order = DB::table('users_lab_tests')
            ->where('id', $order_id)
            ->first();
            $order_amount = $order->amount;
        }

        if($pay_for == 'appointment')
        {
            $order = DB::table('appointments')
            ->where('id', $order_id)
            ->first();
            $order_amount = $order->consultation_fee;

        }
        

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'INR',
                    'unit_amount' => $order_amount * 100,
                    'product_data' => [
                        'name' => BusinessSetting::where(['key' => 'business_name'])->first()->value,
                        'images' => [asset('storage/app/public/business') . '/' . BusinessSetting::where(['key' => 'logo'])->first()->value],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/pay-amount/success',
            'cancel_url' => url()->previous(),
        ]);

        return response()->json(['id' => $checkout_session->id]);
    }
    
    
    public function paymentSuccess()
    {
        
        
        
        $pay_for = session('pay_for');
        $transaction_id = session('transaction_ref');

        
        
        if($pay_for == 'medicine'){

                DB::table('orders')->where('id', session('order_id'))->update(['payment_status' => 'paid','transaction_id' => $transaction_id]);
        } 
        
        
        
        if($pay_for == 'lab_test'){
                            DB::table('users_lab_tests')->where('id', session('order_id'))->update(['payment_status' => 'paid','transaction_id' => $transaction_id]);

        }

        if($pay_for == 'appointment')
        {
            DB::table('appointments')->where('id', session('order_id'))->update(['payment_status' => 'paid','transaction_id' => $transaction_id]);


        }
        
        
        
        

      

        return \redirect()->route('payment-success');
    }
    
    

    public function paymentFail()
    {
    

        
        $pay_for = session('pay_for');
        
        if($pay_for == 'medicine'){

                DB::table('orders')->where('id', session('order_id'))->update(['payment_status' => 'failed']);
        } 
        
        
        
        if($pay_for == 'lab_test'){
                            DB::table('users_lab_tests')->where('id', session('order_id'))->update(['payment_status' => 'failed']);

        }

        if($pay_for == 'appointment')
        {
            DB::table('appointments')->where('id', session('order_id'))->update(['payment_status' => 'failed']);


        }
        
        
        
        return \redirect()->route('payment-fail');
    }


    public function payment_process_3d_for_wallet()
    {
        $tran = Str::random(6) . '-' . rand(1, 1000);
        $customer_id = session('customer_id');
        $payment_id = session('payment_id');
        $paymentData = Payment::find($payment_id);
        $amount = $paymentData->amount;
        session()->put('transaction_ref', $tran);
        $config = Helpers::get_business_settings('stripe');
        Stripe::setApiKey($config['api_key']);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = url('/');

    
        

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'INR',
                    'unit_amount' => $amount * 100,
                    'product_data' => [
                        'name' => BusinessSetting::where(['key' => 'business_name'])->first()->value,
                        'images' => [asset('storage/app/public/business') . '/' . BusinessSetting::where(['key' => 'logo'])->first()->value],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/pay-wallet/success',
            'cancel_url' => url()->previous(),
        ]);

        return response()->json(['id' => $checkout_session->id]);
    }
    
    
    public function paymentWalletSuccess()
    {
        $order = Payment::find(session('payment_id'));
        $order->transaction_id = session('transaction_ref');
     
        try {
            $order->status = 'paid';
            //Helpers::send_order_notification($order);
        } catch (\Exception $e) {
            $order->status = 'failed';
        }

      
        $order->save();


        return \redirect()->route('wallet-payment-success');
    }
}
