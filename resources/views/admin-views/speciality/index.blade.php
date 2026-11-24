@extends('layouts.admin.app')

@section('title', 'Add new speciality')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@php
    $appEnv = env('APP_ENV');
    $assetPrefixPath = $appEnv == 'local' ? '' : 'public';
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{ __('messages.add') }}
                        {{ __('messages.new') }} {{ __('messages.speciality') }}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ isset($speciality) ? __('messages.update') : __('messages.add') . ' ' . __('messages.new') }}
                            {{ __('messages.speciality') }}</h5>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ isset($speciality) ? route('admin.speciality.update', [$speciality['id']]) : route('admin.speciality.store') }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="lang_code" id="lang_code" value="en">
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="en-link">English<span
                                            class="text-uppercase">(EN)</span></a>
                                </li>
                                {{--    <li class="nav-item">
                               <a class="nav-link lang_link" href="#" id="fr-link">French<span class="text-uppercase">(FR)</span></a>
                            </li> --}}
                            </ul>
                            <div class="mb-1 p-4">
                                <div class="form-group">
                                    <label class="input-label" for="domain_id">Select Domain <small
                                            style="color: red">*</small></label>
                                    <select name="domain_id" id="domain_id" class="form-control" required>
                                        <option value="">Select Domain</option>
                                        @foreach ($domains as $domain)
                                            <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-1 p-4 lang_form" id="en-form">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{ __('messages.name') }}
                                        <small style="color: red">* </small>
                                    </label>
                                    <input type="text" name="name_en"
                                        value="{{ isset($speciality) ? $speciality['name'] : '' }}" class="form-control"
                                        placeholder="{{ __('Expert') }} {{ __('messages.name') }}" required>
                                </div>



                            </div>

                            <div class="mb-1 p-4 lang_form d-none" id="fr-form">

                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{ __('messages.name') }}
                                        <small style="color: red">* </small>
                                    </label>
                                    <input type="text" name="name_fr"
                                        value="{{ isset($speciality) ? $speciality['name'] : '' }}" class="form-control"
                                        placeholder="{{ __('messages.speciality') }} {{ __('messages.name') }}" required>
                                </div>


                            </div>


                            <div class="mb-1 p-4 form-group">
                                <label>{{ __('messages.image') }}</label>


                                <small style="color: red">* ( {{ __('messages.ratio') }} 1:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <label class="custom-file-label" for="customFileEg1">{{ __('messages.choose') }}
                                        {{ __('messages.file') }}</label>
                                </div>
                                <hr>
                            </div>
                            <div class="col-12">
                                <center>
                                    <img style="width: 10%;border: 1px solid; border-radius: 10px;" id="viewer"
                                        src="" alt="" />
                                </center>
                            </div>





                            <input name="position" value="1" style="display: none">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($speciality) ? __('messages.update') : __('messages.save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
        });

        $("#en-form").find('input, select,textarea').attr('required', 'required');
        $("#fr-form").find('input, select,textarea').removeAttr('required');

        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');
            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            $("#" + lang + "-form").removeClass('d-none');
            $("#lang_code").val(lang);


            if (lang == 'en') {
                $("#en-form").find('input, select').attr('required', 'required');
                $("#fr-form").find('input, select').removeAttr('required');
            } else {
                $("#fr-form").find('input, select').attr('required', 'required');
                $("#en-form").find('input, select').removeAttr('required');
            }
        });
    </script>
@endpush
