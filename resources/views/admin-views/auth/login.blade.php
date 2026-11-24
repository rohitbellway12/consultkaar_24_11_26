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
    <title>{{__('messages.admin')}} | {{__('messages.login')}}</title>

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
   {{-- <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
         style="height: 100%; background-image: url({{asset($assetPrefixPath . 'assets/admin')}}/svg/components/login-background.png);"> --}}
          
          
          {{--  <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
         style="height: 100%; background-image: url({{asset($assetPrefixPath . 'assets/admin')}}/svg/components/login2.jpg);">
         </div> --}}
         
         <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
     style="height: 100%; background-image: url('https://media.istockphoto.com/id/2163983591/vector/abstract-multicolored-gradient-vector-background-design-wallpaper-template-with-geometric.jpg?s=612x612&w=0&k=20&c=wb1CT6XfnlzyhA63kMaN6LW0EM-ltGJSPSPlSW9qHos='); background-size: cover; background-position: center;">
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
        <div class="card card-lg mb-5" style="background-color: transparent; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border-radius: 12px;">
            <div class="card-body" style="border: 1px solid #ccc; border-radius: 11px;">
                <!-- Form -->
                <form class="" action="{{route('admin.auth.login')}}" method="post" id="form-id">
                    @csrf
                    <div class="text-center">
                        <div class="mb-5">
                            <h2 class="text-capitalize">{{__('messages.signin')}}</h2>
                            <span class="badge badge-soft-info">( {{__('messages.admin_or_employee_signin')}} )</span>
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div class="js-form-message form-group">
                        <label class="input-label text-capitalize" for="signinSrEmail">{{__('messages.your')}} {{__('messages.email')}}</label>
                        <input type="email" class="form-control form-control-lg"
                            name="email" id="signinSrEmail" tabindex="1"
                            placeholder="email@address.com" aria-label="email@address.com"
                            value="{{ old('email') }}"
                            required data-msg="Please enter a valid email address."
                            style="background-color: transparent; border: 1px solid #ccc; color: #000;">
                    </div>

                    <!-- Password Input -->
                    <div class="js-form-message form-group">
                        <label class="input-label" for="signupSrPassword" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                            {{__('messages.password')}}
                            </span>
                        </label>

                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                name="password" id="signupSrPassword"
                                placeholder="{{__('messages.password_length_placeholder',['length'=>'6+'])}}"
                                aria-label="{{__('messages.password_length_placeholder',['length'=>'6+'])}}"
                                required data-msg="{{__('messages.invalid_password_warning')}}"
                                data-hs-toggle-password-options='{
                                    "target": "#changePassTarget",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon"
                                }'
                                style="background-color: transparent; border: 1px solid #ccc; color: #000;">
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="remember">
                            <label class="custom-control-label text-muted" for="termsCheckbox">
                                {{__('messages.remember_me')}}
                            </label>
                        </div>
                    </div>

                    <!-- Transparent Submit Button with Shadow -->
                    <button type="submit" class="btn btn-lg btn-block"
                        style="background-color: transparent; color: #007bff; border: 1px solid #007bff; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);">
                        {{__('messages.sign_in')}}
                    </button>
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
                        <button class="btn btn-primary" onclick="copy_cred()"><i class="tio-copy"></i></button>
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
<style>
    .badge-soft-info {
    color: #000000 !important;
}
</style>

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
function lsRememberMe() {
          alert("here")
          if (rmCheck.checked && emailInput.value !== "") {
            localStorage.username = emailInput.value;
            localStorage.checkbox = rmCheck.value;
          } else {
            localStorage.username = "";
            localStorage.checkbox = "";
          }
        }
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
            e.preventDefault();
            var response = grecaptcha.getResponse();
            alert("here");
            lsRememberMe();
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
