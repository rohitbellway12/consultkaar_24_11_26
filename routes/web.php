<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
// use App\Http\Controllers\Vendor\DashboardController;
// use App\Http\Controllers\Vendor\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('pppp', function(){
//             session()->flush();
//         });
Route::get('clear-all-cache', function () {

    Artisan::call("optimize:clear");
        echo "all cache cleared";
    
});
Route::get('/', 'HomeController@index')->name('home');


Route::get('blog', function () {
    if (request()->has('id') && preg_match('/android/i', request()->userAgent() ?? '')) {
        return redirect()->away('https://play.google.com/store/apps/details?id=com.consultkaar.userapp');
    }

    abort(404);
})->name('blog.deep-link');

// Route::get('/reel/{id}', function ($id) {
//     $userAgent = request()->userAgent() ?? '';
 
//     $playStoreUrl = 'https://play.google.com/store/apps/details?id=com.consultkaar.userapp';
//     $appStoreUrl = 'https://apps.apple.com/app/id6761044369';
 
//     if (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
//         return redirect()->away($appStoreUrl);
//     }
 
//     return redirect()->away($playStoreUrl);
// })->name('reel.deep-link');


Route::get('/reel/{id}', function ($id) {
    $userAgent = request()->userAgent() ?? '';
 
    $playStoreUrl = 'https://play.google.com/store/apps/details?id=com.consultkaar.userapp';
    $appStoreUrl = 'https://apps.apple.com/app/id6761044369';
 
    // 1. Android: Direct 302 Header Redirect to Intent URL
    // Chrome handles intent natively: launches app if installed, opens Play Store if not. (Zero UI / No Button)
    if (preg_match('/android/i', $userAgent)) {
        $androidIntentUrl = "intent://reel/{$id}#Intent;scheme=consultkaar;package=com.consultkaar.userapp;S.browser_fallback_url=" . urlencode($playStoreUrl) . ";end";
        return redirect()->away($androidIntentUrl);
    }
 
    // 2. iOS (iPhone/iPad): Instant Script Redirect to custom scheme (No visible page/button)
    if (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
        $appSchemeUrl = "consultkaar://reel/{$id}";
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Redirecting...</title>
    <script>
        var now = Date.now();
        window.location.href = "{$appSchemeUrl}";
        setTimeout(function() {
            if (Date.now() - now < 2000) {
                window.location.href = "{$appStoreUrl}";
            }
        }, 500);
    </script>
</head>
<body style="background:#fff;"></body>
</html>
HTML;
        return response($html)->header('Content-Type', 'text/html');
    }
 
    // 3. Desktop / Others: Direct 302 Header Redirect to Play Store
    return redirect()->away($playStoreUrl);
})->name('reel.deep-link');
 







Route::get('terms-and-conditions', 'HomeController@terms_and_conditions')->name('terms-and-conditions');
Route::get('child-policy', 'HomeController@child_policy');
Route::get('about-us', 'HomeController@about_us')->name('about-us');
Route::get('contact-us', 'HomeController@contact_us')->name('contact-us');
Route::get('privacy-policy', 'HomeController@privacy_policy')->name('privacy-policy');

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthenticated.']);
    return response()->json([
        'errors' => $errors,
    ], 401);
})->name('authentication-failed');

Route::group(['prefix' => 'payment-mobile'], function () {
    Route::get('/', 'PaymentController@payment')->name('payment-mobile');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});

// SSLCOMMERZ Start
/*Route::get('/example1', 'SslCommerzPaymentController@exampleEasyCheckout');
Route::get('/example2', 'SslCommerzPaymentController@exampleHostedCheckout');*/
Route::post('pay-ssl', 'SslCommerzPaymentController@index');
Route::post('/success', 'SslCommerzPaymentController@success');
Route::post('/fail', 'SslCommerzPaymentController@fail');
Route::post('/cancel', 'SslCommerzPaymentController@cancel');
Route::post('/ipn', 'SslCommerzPaymentController@ipn');
//SSLCOMMERZ END

/*paypal*/
/*Route::get('/paypal', function (){return view('paypal-test');})->name('paypal');*/
Route::post('pay-paypal', 'PaypalPaymentController@payWithpaypal')->name('pay-paypal');
Route::get('paypal-status', 'PaypalPaymentController@getPaymentStatus')->name('paypal-status');
/*paypal*/

/*Route::get('stripe', function (){
return view('stripe-test');
});*/

Route::get('pay-stripe', 'StripePaymentController@payment_process_3d')->name('pay-stripe');
Route::get('pay-stripe/success', 'StripePaymentController@success')->name('pay-stripe.success');
Route::get('pay-stripe/fail', 'StripePaymentController@fail')->name('pay-stripe.fail');



Route::get('pay-stripe', 'PaymentController@payAmount')->name('pay-stripe');
Route::get('pay-amount', 'StripePaymentController@payment_process_3d_for_amount')->name('pay-amount');
Route::get('pay-amount/success', 'StripePaymentController@paymentSuccess')->name('pay-amount.success');
Route::get('pay-amount/fail', 'StripePaymentController@paymentFail')->name('pay-amount.fail');

Route::get('pay-wallet', 'PaymentController@payAmountForWallet')->name('pay-wallet');
Route::get('add-money-in-wallet', 'StripePaymentController@payment_process_3d_for_wallet')->name('add-money-in-wallet');
Route::get('pay-wallet/success', 'StripePaymentController@paymentWalletSuccess')->name('pay-wallet.success');
Route::get('pay-wallet/fail', 'StripePaymentController@paymentpaymentWalletFail')->name('pay-wallet.fail');



// Get Route For Show Payment Form
Route::get('paywithrazorpay', 'RazorPayController@payWithRazorpay')->name('paywithrazorpay');
Route::post('payment-razor', 'RazorPayController@payment')->name('payment-razor');

/*Route::fallback(function () {
return redirect('/admin/auth/login');
});*/

Route::get('payment-success', 'PaymentController@success')->name('payment-success');
Route::get('wallet-payment-success', 'PaymentController@walletPaymentSuccess')->name('wallet-payment-success');
Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');

//senang pay
Route::match(['get', 'post'], '/return-senang-pay', 'SenangPayController@return_senang_pay')->name('return-senang-pay');

// paymob
Route::post('/paymob-credit', 'PaymobController@credit')->name('paymob-credit');
Route::get('/paymob-callback', 'PaymobController@callback')->name('paymob-callback');

// HDFC
Route::match(['get','post'],'/hdfc/initiate', 'HdfcPaymentController@initiate')->name('hdfc-initiate');
Route::post('/hdfc/callback', 'HdfcPaymentController@callback')->name('hdfc-callback');

//paystack
Route::post('/paystack-pay', 'PaystackController@redirectToGateway')->name('paystack-pay');
Route::get('/paystack-callback', 'PaystackController@handleGatewayCallback')->name('paystack-callback');
Route::get('/paystack',function (){
    return view('paystack');
});


// The route that the button calls to initialize payment
Route::post('/flutterwave-pay','FlutterwaveController@initialize')->name('flutterwave_pay');
// The callback url after a payment
Route::get('/rave/callback', 'FlutterwaveController@callback')->name('flutterwave_callback');


// The callback url after a payment
Route::get('mercadopago/home', 'MercadoPagoController@index')->name('mercadopago.index');
Route::post('mercadopago/make-payment', 'MercadoPagoController@make_payment')->name('mercadopago.make_payment');
Route::get('mercadopago/get-user', 'MercadoPagoController@get_test_user')->name('mercadopago.get-user');



Route::get('/test',function (){
    // dd(\App\CentralLogics\Helpers::get_view_keys());
    dd('Hello tester');
});

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
    return response()->json([
        'errors' => $errors
    ], 401);
})->name('authentication-failed');

Route::get('module-test',function (){

});


//Restaurant Registration
Route::group(['prefix' => 'restaurant', 'as' => 'restaurant.'], function () {
    Route::get('apply', 'VendorController@create')->name('create');
    Route::post('apply', 'VendorController@store')->name('store');
});

//Deliveryman Registration
Route::group(['prefix' => 'deliveryman', 'as' => 'deliveryman.'], function () {
    Route::get('apply', 'DeliveryManController@create')->name('create');
    Route::post('apply', 'DeliveryManController@store')->name('store');
});


// Route::prefix('vendor')->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
// });


