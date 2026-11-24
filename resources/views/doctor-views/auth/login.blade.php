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
    <title>Advisor | {{__('messages.login')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $favIconPath }}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset($assetPrefixPath . 'assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset($assetPrefixPath . 'assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset($assetPrefixPath . 'assets/admin')}}/css/toastr.css">
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
         style="height: 100%; background-image: url('https://media.istockphoto.com/id/2163983591/vector/abstract-multicolored-gradient-vector-background-design-wallpaper-template-with-geometric.jpg?s=612x612&w=0&k=20&c=wb1CT6XfnlzyhA63kMaN6LW0EM-ltGJSPSPlSW9qHos='); background-size: cover; background-position: center;"
         <!--style="height: 100%; background-image: url({{asset($assetPrefixPath . 'assets/admin')}}/svg/components/login-background.png);"> -->
         <!--style="height: 100%; background: #01853A;">-->
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

        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <!-- Card -->
                <div class="card card-lg mb-5">
                    <div class="card-body">
                        <!-- Form -->
                        <form class="" action="{{route('doctor.auth.login')}}" method="post" id="form-id">
                            @csrf
                            <div class="text-center">
                                <div class="mb-5">
                                    <h2 class="text-capitalize">{{__('messages.signin')}}</h2>
                                    <span class="badge badge-soft-info">( {{__('messages.doctor_signin')}} )</span>
                                </div>
                             
                            </div>

                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label qcont" for="mobile_number">Mobile number  <span style="color:red;"><strong>*</strong></span></label>
                                <input type="text" class="form-control form-control-lg" name="mobile_number" 
                                   placeholder="Enter mobile number"  required>
                            </div>
                            <!-- End Form Group -->

          
                        

                            <!-- Checkbox -->
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="termsCheckbox"
                                           name="remember">
                                    <label class="custom-control-label text-muted" for="termsCheckbox">
                                        {{__('messages.remember_me')}}
                                    </label>
                                </div>
                            </div>
                            <!-- End Checkbox -->

                            {{-- recaptcha --}}
                           {{--  @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                            @if(isset($recaptcha) && $recaptcha['status'] == 1)
                                <div id="recaptcha_element" style="width: 100%;" data-type="image"></div>
                                <br/>
                            @else
                                <div class="row p-2">
                                    <div class="col-6 pr-0">
                                        <input type="text" class="form-control form-control-lg" name="custome_recaptcha"
                                               id="custome_recaptcha" required placeholder="{{\__('Enter recaptcha value')}}" style="border: none" autocomplete="off">
                                    </div>
                                    <div class="col-6" style="background-color: #FFFFFF; border-radius: 5px;">
                                        <img src="<?php echo $custome_recaptcha->inline(); ?>" style="width: 100%; border-radius: 4px;"/>
                                    </div>
                                </div>
                            @endif --}}

                            <button type="submit" class="btn btn-lg btn-block btn-primary">{{__('messages.sign_in')}}</button>
                        </form>
                        <!-- End Form -->
                    </div>
                    @if(env('APP_MODE')=='demo')
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-10">
                                    <span>Email : admin@admin.com</span><br>
                                    <span>Password : 12345678</span>
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-primary" onclick="copy_cred()"><i class="tio-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
    <!-- End Content -->
</main>
<!-- ========== END MAIN CONTENT ========== -->


<!-- JS Implementing Plugins -->
<script src="{{asset($assetPrefixPath . 'assets/admin')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset($assetPrefixPath . 'assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset($assetPrefixPath . 'assets/admin')}}/js/toastr.js"></script>
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
        // INITIALIZATION OF SHOW PASSWORD
        // =======================================================
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        // INITIALIZATION OF FORM VALIDATION
        // =======================================================
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

{{-- recaptcha scripts start --}}
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script type="text/javascript">
        var onloadCallback = function () {
            grecaptcha.render('recaptcha_element', {
                'sitekey': '{{ \App\CentralLogics\Helpers::get_business_settings('recaptcha')['site_key'] }}'
            });
        };
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
    <script>
        $("#form-id").on('submit',function(e) {
            var response = grecaptcha.getResponse();

            if (response.length === 0) {
                e.preventDefault();
                toastr.error("{{__('messages.Please check the recaptcha')}}");
            }
        });
    </script>
@endif
{{-- recaptcha scripts end --}}



@if(env('APP_MODE')=='demo')
    <script>
        function copy_cred() {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
@endif

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset($assetPrefixPath . '/assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>