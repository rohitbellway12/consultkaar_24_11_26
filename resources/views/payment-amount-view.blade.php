
@php($currency=\App\Models\BusinessSetting::where(['key'=>'currency'])->first()->value)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    <meta name="_token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font -->
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('/public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('/public/assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('/public/assets/admin')}}/css/custom.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('/public/assets/admin')}}/css/theme.minc619.css?v=1.0">

    <style>
        .stripe-button-el {
            display: none !important;
        }

        .razorpay-payment-button {
            display: none !important;
        }
    </style>
    <script
        src="{{asset('/public/assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('/public/assets/admin')}}/css/toastr.css">
    {{--stripe--}}
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    {{--stripe--}}
</head>
<!-- Body-->
<body class="toolbar-enabled">
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <center class="">
                <h1>Payment method</h1>
            </center>
        </div>
        @php($order=\App\Models\Order::find(session('order_id')))
        <section class="col-lg-12">
            <div class="checkout_details mt-3">
                <div class="row">

   
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body py-0" style="height: 70px">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                                    <button class="btn btn-block click-if-alone" type="button" id="checkout-button">
                                        <img width="100" src="{{asset('/public/assets/admin/img/stripe.png')}}"/>
                                    </button>

                                    <script type="text/javascript">
                                        // Create an instance of the Stripe object with your publishable API key
                                        var stripe = Stripe('{{$config['published_key']}}');
                                        var checkoutButton = document.getElementById("checkout-button");
                                        checkoutButton.addEventListener("click", function () {
                                            fetch("{{route('pay-amount')}}", {
                                                method: "GET",
                                            }).then(function (response) {
                                                console.log(response)
                                                return response.text();
                                            }).then(function (session) {
                                                console.log(JSON.parse(session).id)
                                                return stripe.redirectToCheckout({sessionId: JSON.parse(session).id});
                                            }).then(function (result) {
                                                if (result.error) {
                                                    alert(result.error.message);
                                                }
                                            }).catch(function (error) {
                                                console.error("Error:", error);
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    @endif


                    
                </div>
            </div>
        </section>
    </div>
</div>

<!-- JS Front -->
<script src="{{asset('/public/assets/admin')}}/js/custom.js"></script>
<script src="{{asset('/public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('/public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('/public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('/public/assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('/public/assets/admin')}}/js/bootstrap.min.js"></script>

{!! Toastr::message() !!}



<script>
    $('#checkout-button').click();

    setTimeout(function () {
        $('.stripe-button-el').hide();
        $('.razorpay-payment-button').hide();
    }, 10)
</script>




<script type="text/javascript">
    
</script>

</body>
</html>
