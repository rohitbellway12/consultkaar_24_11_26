@extends('layouts.doctor.app')

@section('title',__('messages.profile_settings'))
@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp
@push('css_or_js')

@endpush

@section('content')
    <!-- Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{__('messages.settings')}}</h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-primary" href="{{route('doctor.dashboard')}}">
                        <i class="tio-home mr-1"></i> {{__('messages.dashboard')}}
                    </a>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <div class="row">
           

            <div class="col-lg-12">
                <form action="{{env('APP_MODE')!='demo'?route('doctor.settings'):'javascript:'}}" method="post" enctype="multipart/form-data" id="admin-settings-form">
                @csrf
                <!-- Card -->
                    <div class="card mb-3 mb-lg-5" id="generalDiv">
                        <!-- Profile Cover -->
                        <div class="profile-cover">
                            <div class="profile-cover-img-wrapper"></div>
                        </div>
                        <!-- End Profile Cover -->

                        <!-- Avatar -->
                        <label
                            class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar"
                            for="avatarUploader">
                            <img id="viewer"
                               
                                 class="avatar-img"
                                 src="{{asset('storage/app/public/user/' . $admin->image)}}"
                                 alt="Image">

                            <input type="file" name="image" class="js-file-attach avatar-uploader-input"
                                   id="customFileEg1"
                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="avatar-uploader-trigger" for="customFileEg1">
                                <i class="tio-edit avatar-uploader-icon shadow-soft"></i>
                            </label>
                        </label>
                        <!-- End Avatar -->
                    </div>
                    <!-- End Card -->

                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h2 class="card-title h4"><i class="tio-info"></i> {{__('messages.basic_information')}}</h2>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form -->
                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">{{__('messages.full_name')}} <i
                                        class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Display name"></i></label>
                                    @php
                                    $name = $admin->name;

                                    if (!empty($name)) {
                                        $nameParts = explode(" ", $name);
                                        $f_name = $nameParts[0];
                                        $l_name = isset($nameParts[1]) ? $nameParts[1] : '';
                                    } else {
                                        $f_name = '';
                                        $l_name = '';
                                    }
                                    @endphp


                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm-down-break">
                                        <input type="text" class="form-control" name="f_name" id="firstNameLabel"
                                               placeholder="{{__('messages.your_first_name')}}" aria-label="{{__('messages.your_first_name')}}"
                                               value="{{$f_name}}">
                                        <input type="text" class="form-control" name="l_name" id="lastNameLabel"
                                               placeholder="{{__('messages.your_last_name')}}" aria-label="{{__('messages.your_last_name')}}"
                                               value="{{$l_name}}">
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="phoneLabel" class="col-sm-3 col-form-label input-label">{{__('messages.phone')}} <span
                                        class="input-label-secondary"></span></label>

                                <div class="col-sm-9">
                                    <input type="text" class="js-masked-input form-control" name="phone" id="phoneLabel"
                                           placeholder="+x(xxx)xxx-xx-xx" 
                                           value="{{$admin->mobile_number}}"
                                           required>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <div class="row form-group">
                                <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{__('messages.email')}}</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                           value="{{$admin->email}}"
                                           placeholder="{{__('messages.enter_new_email_address')}}" aria-label="{{__('messages.enter_new_email_address')}}">
                                           <p id="error-message" style="color:red;"></p>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" onclick="{{env('APP_MODE')!='demo'?"form_alert('admin-settings-form','Want to update doctor info ?')":"call_demo()"}}" class="btn btn-primary">{{__('messages.save')}}</button>
                            </div>

                            <!-- End Form -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </form>

                <!-- Card -->
              
                <!-- End Card -->

                <!-- Sticky Block End Point -->
                <div id="stickyBlockEndPoint"></div>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Content -->
@endsection

@push('script_2')
    <script>
   $('#admin-settings-form').submit(function(event) {
   
var email = $("#newEmailLabel").val();    

        var emailRegex = /^[^\d\s@][^\s@]*@[^\s@]+\.[^\s@]+$/;

        if (emailRegex.test(email)) {
            $('#error-message').text("");
            return true;
        } else {
            $('#error-message').text("Please enter a valid email address.");
            $('#newEmailLabel').focus(); // Focus the email field if invalid
             event.preventDefault();
        }
 
});
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>

    <script>
        $("#generalSection").click(function() {
            $("#generalSection").addClass("active");
            $('html, body').animate({
                scrollTop: $("#generalDiv").offset().top
            }, 2000);
        });

    </script>
@endpush
