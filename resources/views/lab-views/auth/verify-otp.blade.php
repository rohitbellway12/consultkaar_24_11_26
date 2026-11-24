@php
$appEnv = env('APP_ENV');
$assetPrefixPath = ($appEnv == 'local') ? '/' : 'public/';
$favIconData = \App\Models\BusinessSetting::where(['key'=>'fav_icon'])->first();
$favIcon  = $favIconData->value ?? ''; 
$favIconPath = (env('APP_ENV') == 'local') ?  asset('storage/business/' . $favIcon) : asset('storage/app/public/business/' . $favIcon); 
$favIconPath =  $favIconData->value ? $favIconPath : ''; 
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{__('messages.doctor')}} | Verify OTP</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $favIconPath }}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/doctor')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset($assetPrefixPath . 'assets/doctor')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset($assetPrefixPath . 'assets/doctor')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset($assetPrefixPath . 'assets/doctor')}}/css/toastr.css">
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
        {{--  style="height: 100%; background-image: url({{asset($assetPrefixPath . 'assets/doctor')}}/svg/components/login-background.png);"> --}}
         style="height: 100%; background: #01853A;">
    </div>
    @php($systemLogo)
    <!-- Content -->
    <div class="container py-5 py-sm-7">
        <label class="badge badge-soft-success float-right" style="z-index: 9;position: absolute;right: 0.5rem;top: 0.5rem;">
            {{__('messages.software_version')}} : {{env('SOFTWARE_VERSION')}}
        </label>
        <a class="d-flex justify-content-center mb-5" href="javascript:">
            <?php
                $logoPath = (env('APP_ENV') == 'local') ? asset('storage/business/' . $systemLogo) : asset('storage/app/public/business/' . $systemLogo);     
            ?>
            <img class="z-index-2" src="{{$logoPath}}"
                  alt="Image Description" style="max-height: 100px; max-width: 300px">
        </a>

        <div class="container py-5 py-sm-7">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="card card-lg mb-5">
                        <div class="card-body">
                            <form method="post" action="{{ url('doctor/auth/verify-otp') }}">
                                @csrf
                                <div class="text-center">
                                    <div class="mb-5">
                                        <h2 class="text-capitalize">Verify OTP</h2>
                                    </div>
                                </div>
                                <div class="js-form-message form-group">
                                    <label class="input-label" for="otp">OTP  <span style="color:red;"><strong>*</strong></span></label>
                                    <input type="number" class="form-control form-control-lg" name="otp" placeholder="Enter OTP" oninput="this.value = this.value.slice(0, 4)" required>

                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-lg btn-block btn-primary">Verify</button>
                                </div>
                                <div class="form-group">
                                    <a href="{{ url('doctor/auth/resend-otp') }}">Resend OTP</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
</main>
<!-- ========== END MAIN CONTENT ========== -->


<!-- JS Implementing Plugins -->
<script src="{{asset($assetPrefixPath . 'assets/doctor')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset($assetPrefixPath . 'assets/doctor')}}/js/theme.min.js"></script>
<script src="{{asset($assetPrefixPath . 'assets/doctor')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<!-- JS Plugins Init. -->
<script>
    $(document).on('ready', function () {
    

        // INITIALIZATION OF FORM VALIDATION
        // =======================================================
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>



<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset($assetPrefixPath . '/assets/doctor')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
