@php
$appEnv = env('APP_ENV');
$assetPrefixPath = ($appEnv == 'local') ? '/' : 'public/';
$favIconData = \App\Models\BusinessSetting::where(['key' => 'fav_icon'])->first();
$favIcon  = $favIconData->value ?? ''; 
$favIconPath = (env('APP_ENV') == 'local') ? asset('storage/business/' . $favIcon) : asset('storage/app/public/business/' . $favIcon); 
$favIconPath =  $favIconData->value ? $favIconPath : ''; 
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{ __('messages.vendor') }} | {{ __('messages.login') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $favIconPath }}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ asset($assetPrefixPath . 'assets/admin/vendor/icon-set/style.css') }}">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{ asset($assetPrefixPath . 'assets/admin/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ asset($assetPrefixPath . 'assets/admin/css/toastr.css') }}">
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
         style="height: 100%; background: #01853A;">
    </div>

    @php
        $vendorLogo = \App\Models\BusinessSetting::where(['key' => 'vendor_logo'])->first();
        $vendorLogoPath = (env('APP_ENV') == 'local') 
            ? asset('storage/business/' . ($vendorLogo->value ?? 'default-vendor-logo.png')) 
            : asset('storage/app/public/business/' . ($vendorLogo->value ?? 'default-vendor-logo.png'));
    @endphp

    <!-- Content -->
    <div class="container py-5 py-sm-7">
        <label class="badge badge-soft-success float-right" style="z-index: 9; position: absolute; right: 0.5rem; top: 0.5rem;">
            {{ __('messages.software_version') }} : {{ env('SOFTWARE_VERSION') }}
        </label>

        <a class="d-flex justify-content-center mb-5" href="javascript:">
            <img class="z-index-2" src="{{ asset('storage/app/public/business/2024-02-19-65d306c457fe7.png') }}" 
            alt="Vendor Logo" style="max-height: 100px; max-width: 300px">
        </a>

        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <!-- Card -->
                <div class="card card-lg mb-5">
                    <div class="card-body">
                        <!-- Form -->
                        <form action="{{ route('auth.login') }}" method="post" id="vendor-login-form">
                            @csrf
                            <div class="text-center">
                                <div class="mb-5">
                                    <h2 class="text-capitalize">{{ __('messages.signin') }}</h2>
                                    <span class="badge badge-soft-info">( {{ __('Vendor SignIn') }} )</span>
                                </div>
                            </div>
                        
                            <!-- Form Group -->
                          <div class="form-group">
                                <label for="email">Email <span style="color:red;">*</span></label>
                                <input type="email" class="form-control" name="email" id="email"
                                       placeholder="Enter Email" required>
                            </div>

                            <!-- Password Field with Toggle -->
                            <div class="form-group password-container">
                                <label for="password">Password <span style="color:red;">*</span></label>
                                <input type="password" class="form-control" name="password" id="password"
                                       placeholder="Enter Password" required>
                                <span class="toggle-password" onclick="togglePassword('password')"> Show password 👁️</span>
                            </div>
                            <!-- End Form Group -->
                        
                            <!-- Checkbox -->
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="rememberMe"
                                           name="remember">
                                    <label class="custom-control-label text-muted" for="rememberMe">
                                        {{ __('messages.remember_me') }}
                                    </label>
                                </div>
                            </div>
                            <!-- End Checkbox -->
                        
                            <button type="submit" class="btn btn-lg btn-block btn-primary">
                                {{ __('messages.sign_in') }}
                            </button>
                        </form>

                        <!-- End Form -->
                    </div>
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
    <!-- End Content -->
</main>
<!-- ========== END MAIN CONTENT ========== -->

<!-- JS Implementing Plugins -->
<script src="{{ asset($assetPrefixPath . 'assets/admin/js/vendor.min.js') }}"></script>
<!-- JS Front -->
<script src="{{ asset($assetPrefixPath . 'assets/admin/js/theme.min.js') }}"></script>
<script src="{{ asset($assetPrefixPath . 'assets/admin/js/toastr.js') }}"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{ $error }}', 'Error', {
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
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>
<script>
    // Toggle password visibility
    function togglePassword(id) {
        const passwordField = document.getElementById(id);
        const icon = passwordField.nextElementSibling;

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.textContent = "🔒";
        } else {
            passwordField.type = "password";
            icon.textContent = "👁️";
        }
    }
</script>

@if(env('APP_MODE')=='demo')
    <script>
        function copy_cred() {
            $('#email').val('vendor@vendor.com');
            $('#password').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
@endif

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) 
        document.write('<script src="{{ asset($assetPrefixPath . '/assets/admin/vendor/babel-polyfill/polyfill.min.js') }}"><\/script>');
</script>

</body>
</html>
