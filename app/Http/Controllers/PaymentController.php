<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);

            if($order->order_amount == 0){

                $user = User::find($order->user_id);
                if($user->wallet_balance < $order->wallet_amount){
                    return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Insufficient wallet balance']], 403);
                } 

                $order->transaction_reference = 'wallet_'.Str::random(12);
                $order->payment_method = 'wallet';
                $order->payment_status = 'paid';
                $order->order_status = 'pending';
                // $order->confirmed = now();
                $order->save();      
                
                if($user) $user->decrement('wallet_balance', $order->wallet_amount);

                return redirect('payment-success' . '&status=success');

            }

            if ($request->has('payment_type')) { 
                if($request->payment_type == 'part') return view('payment-view', ['payment_type' => 'part']);
            }

            // Decide gateway: prefer Razorpay if active and keys present, else HDFC if active
            $razor = \App\CentralLogics\Helpers::get_business_settings('razor_pay');
            $hdfc  = \App\CentralLogics\Helpers::get_business_settings('hdfc');

            $razorActive = $razor && ($razor['status']??0)==1 && !empty($razor['razor_key']) && !empty($razor['razor_secret']);
            $hdfcActive  = $hdfc && ($hdfc['status']??0)==1 && !empty($hdfc['merchant_id']) && !empty($hdfc['access_code']) && !empty($hdfc['working_key']);

            if ($razorActive) {
                return \redirect()->route('paywithrazorpay');
            }
            if ($hdfcActive) {
                return \redirect()->route('hdfc-initiate');
            }

            return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'No payment gateway configured']], 403);
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }


    public function payAmount(Request $request)
    {

        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);
        session()->put('pay_for', $request->pay_for);

        

        $customer = User::find($request['customer_id']);

        if($request->pay_for == 'medicine'){
            $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();
        }

        if($request->pay_for == 'lab_test'){
            $order = DB::table('users_lab_tests')
            ->where('id', $request->order_id)
            ->where('user_id', $request->customer_id)
            ->first();
        }

        if($request->pay_for == 'appointment')
        {
            $order = DB::table('appointments')
            ->where('id', $request->order_id)
            ->where('user_id', $request->customer_id)
            ->first();

        }

       

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
            return view('payment-amount-view', ['payment_type' => 'full']);
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }

    public function payAmountForWallet(Request $request)
    {

        if ($request->has('callback')) {
            Payment::where(['id' => $request->payment_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('payment_id', $request->payment_id);
        $order = DB::table('payment_logs')
            ->where('id', $request->payment_id)
            ->where('user_id', $request->customer_id)
            ->first();
        

        $customer = User::find($request['customer_id']);
         if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer->name,
                'email' =>  $customer->email,
                'mobile_number' => $customer->mobile_number,
            ];
            session()->put('data', $data);
            return view('wallet-amount-view', ['payment_type' => 'full']);
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }

    public function success()
    {
        // return response()->json(['message' => 'Payment succeeded'], 200);
        return view('payment-success');
    }

    public function fail()
    {
        $pay_for = session('pay_for');
        return response()->json(['message' => 'Payment failed'], 403);
    }
    public function walletPaymentSuccess()
    {
        return response()->json(['message' => 'Payment succeeded'], 200);
    }
}
