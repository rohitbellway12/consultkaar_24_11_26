@extends('layouts.admin.app')

@section('title','Update Banner')

@push('css_or_js')
<style>
    .select2 .select2-container .select2-container--default .select2-container--above .select2-container--focus{
        width:100% !important;
    }
</style>
@endpush
@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
  $storagePath = ($appEnv == 'local') ? asset('/storage/banner') : asset('/storage/app/public/banner');
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update')}} {{__('messages.banner')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.banner.update', [$banner->id])}}" method="post" id="banner_form" enctype="multipart/form-data">
                            @csrf
                            <?php
                            $banner_en = \App\Models\Banner::where(['bid' => "b".$banner->id,'lang_code' => 'en'])->first();
                            $banner_fr =  \App\Models\Banner::where(['bid' => "b".$banner->id,'lang_code' => 'fr'])->first();
    
                          
                            ?>
                            <input type="hidden" name="lang_code" id="lang_code" value="en">    
                   
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                   <a class="nav-link lang_link active" href="#" id="en-link">English<span class="text-uppercase">(EN)</span></a>
                                </li>
                             {{--   <li class="nav-item">
                                   <a class="nav-link lang_link" href="#" id="fr-link">French<span class="text-uppercase">(FR)</span></a>
                                </li> --}}
                             </ul>

                             <div class="mb-1 p-4 lang_form" id="en-form">
                                <div class="form-group">
                                    <label class="input-label qcont" for="speciality_id">Speciality <small style="color: red">* </small> </label> 
                                    <select name="speciality_id_en"  class="form-control speciality_id" required>
                                        <option value="">Select Speciality</option>
                                        <?php
                                            if(count($specialities) > 0){
                                            foreach ($specialities as $key=>$sp){
                                                $selected = ($sp->id == $banner['speciality_id']) ? "selected=selected" : '';
        
                                        ?>
                                            <option value="{{ $sp->id }}" {{ $selected }}>{{ $sp->name }}</option>
        
                                        <?php }} ?>
                                    </select>
                                </div>
                           
                        </div>


                        <div class="mb-1 p-4 lang_form d-none" id="fr-form">
                            <div class="form-group">
                                <label class="input-label qcont" for="speciality_id">Speciality <small style="color: red">* </small> </label> 
                                <select name="speciality_id_fr"  class="form-control speciality_id" required>
                                    <option value="">Select Speciality</option>
                                    <?php
                                        if(count($french_specialities) > 0){
                                        foreach ($french_specialities as $key=>$sp){
                                            if(isset($banner_fr) && !empty($banner_fr)){
                                                    $selected = ($sp->id == $banner_fr['speciality_id']) ? "selected=selected" : '';
                                                } else {
                                                    $selected = '';
                                                }
    
                                    ?>
                                        <option value="{{ $sp->id }}" {{ $selected}}>{{ $sp->name }}</option>
    
                                    <?php }} ?>
                                </select>
                            </div>
                       
                    </div>

                            <!--<div class="row">-->
                            <!--    <div class="form-group mb-1 p-4 col-md-4">-->
                            <!--        <label>{{__('messages.banner')}} {{__('messages.image')}}</label>-->
                            <!--        <small style="color: red">* ( {{__('messages.ratio')}} 3:1 )</small>-->
                            <!--        <div class="custom-file">-->
                            <!--            <input type="file" name="image" id="customFileEg1" class="custom-file-input"-->
                            <!--                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">-->
                            <!--            <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>-->
                            <!--        </div>-->
                            <!--        <div class="form-group" style="margin-top:20px;">-->
                            <!--            <center>-->
                            <!--                <img style="border: 1px solid; border-radius: 10px;width:30%" id="viewer"  src="{{$storagePath}}/{{$banner['image']}}" alt="campaign image"/>-->
                            <!--            </center>-->
                            <!--        </div>-->
                            <!--    </div>-->


                            <!--    <div class="form-group mb-1 p-4 col-md-4">-->
                            <!--        <label>{{__('messages.banner')}} for</label>-->
                            <!--        <small style="color: red">*</small>-->
                            <!--        <select name="banner_for" class="form-control" required>-->
                            <!--            <option value="">Select</option>-->
                            <!--            <option value="home"  {{ ( $banner['banner_for'] == 'home') ? "selected=selected" : '' }} >Home</option>-->
                            <!--            <option value="lab_test"  {{ ( $banner['banner_for'] == 'lab_test') ? "selected=selected" : '' }}>Lab Test</option>-->
                            <!--        </select>-->
                                       
                                   
                            <!--    </div>-->


                            <!--</div>-->
                            <!--------------------------------------------Rifaul change------------------------------------------->
                            <div class="row">
                            <!-- Banner Image Upload -->
                            <div class="form-group mb-1 p-4 col-md-4">
                                <label>{{__('messages.banner')}} {{__('messages.image')}}</label>
                                <small style="color: red">* ( {{__('messages.ratio')}} 3:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                </div>
                                <div class="form-group" style="margin-top:20px;">
                                    <center>
                                        <img style="border: 1px solid; border-radius: 10px;width:30%" id="viewer"  
                                            src="{{$storagePath}}/{{$banner['image']}}" alt="campaign image"/>
                                    </center>
                                </div>
                            </div>
                        
                            <!-- Banner For Dropdown -->
                            <div class="form-group mb-1 p-4 col-md-4">
                                <label>{{__('messages.banner')}} for</label>
                                <small style="color: red">*</small>
                                <select name="banner_for" class="form-control" id="bannerForDropdown" required>
                                    <option value="">Select</option>
                                    <option value="home" {{ ( $banner['banner_for'] == 'home') ? "selected=selected" : '' }}>Home</option>
                                    <option value="lab_test" {{ ( $banner['banner_for'] == 'lab_test') ? "selected=selected" : '' }}>Lab Test</option>
                                </select>
                            </div>
                        
                            <!-- Lab Test Dropdown (Initially Hidden) -->
                            <div class="form-group mb-1 p-4 col-md-4" id="labTestContainer" style="display: none;">
                                <label>Select Lab Test</label>
                                <small style="color: red">*</small>
                                <select name="lab_test_id" class="form-control">
                                    <option value="">Select Lab Test</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- jQuery to Show/Hide Lab Test Dropdown -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                        $(document).ready(function () {
                            function fetchLabTests(selectedLabTestId = null) {
                                $.ajax({
                                    url: "{{ route('admin.banner.get-lab-tests') }}",
                                    type: "GET",
                                    success: function (data) {
                                        let options = '<option value="">Select Lab Test</option>';
                                        $.each(data, function (key, value) {
                                            options += `<option value="${value.id}" ${selectedLabTestId == value.id ? "selected" : ""}>${value.name}</option>`;
                                        });
                                        $('select[name="lab_test_id"]').html(options);
                                    }
                                });
                            }
                        
                            // On Page Load, check if 'lab_test' is selected and fetch Lab Tests
                            if ($("#bannerForDropdown").val() === 'lab_test') {
                                $('#labTestContainer').show();
                                fetchLabTests("{{ $banner['lab_test'] ?? '' }}");
                            }
                        
                            $('#bannerForDropdown').on('change', function () {
                                if ($(this).val() === 'lab_test') {
                                    $('#labTestContainer').show();
                                    fetchLabTests();
                                } else {
                                    $('#labTestContainer').hide();
                                }
                            });
                        });
                        </script>






                          
                            
                            <button type="submit" class="btn btn-primary mt-5">{{__('messages.submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
<script>
    function getRequest(route, id) {
        $.get({
            url: route,
            dataType: 'json',
            success: function (data) {
                $('#' + id).empty().append(data.options);
            },
        });
    }
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
    $(document).on('ready', function () {
        $(".speciality_id").select2();
        $('#banner_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.banner.update', [$banner['id']])}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('Banner updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.banner.add-new')}}';
                        }, 2000);
                    }
                }
            });
        });
        

        var lang = "<?=$banner->lang_code?>";
       
       if(lang == 'en')
       {
           $("#en-form").find('input, select').attr('required', 'required');
           $("#fr-form").find('input, select').removeAttr('required');
       }
       else
       {
           $("#fr-form").find('input, select').attr('required', 'required');
          $("#en-form").find('input, select').removeAttr('required');
       }
    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');
        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        $("#"+lang+"-form").removeClass('d-none');
        $("#lang_code").val(lang);
        

        if(lang == 'en')
        {
            $("#en-form").find('input, select').attr('required', 'required');
            $("#fr-form").find('input, select').removeAttr('required');
        }
        else
        {
            $("#fr-form").find('input, select').attr('required', 'required');
            $("#en-form").find('input, select').removeAttr('required');
        }
    });
         
    });

    
       
        
    </script>
@endpush
