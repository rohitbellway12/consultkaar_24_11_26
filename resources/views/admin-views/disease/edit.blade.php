@extends('layouts.admin.app')

@section('title','Update disease')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@php
    $appEnv = env('APP_ENV');
    $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';

    $disease_en = \App\Models\Disease::where(['did' => "d" . ($disease->id ?? 0), 'lang_code' => 'en'])->first();
    $disease_fr = \App\Models\Disease::where(['did' => "d" . ($disease->id ?? 0), 'lang_code' => 'fr'])->first();
@endphp

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.update')}} Disease</h1>
            </div>
        </div>
    </div>

    @if (!$disease)
        <div class="alert alert-danger">Disease data not found.</div>
        @php return; @endphp
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.disease.update', [$disease->id]) }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="lang_code" id="lang_code" value="en">

                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link lang_link active" href="#" id="en-link">English<span class="text-uppercase">(EN)</span></a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link lang_link" href="#" id="fr-link">French<span class="text-uppercase">(FR)</span></a>
                            </li> --}}
                        </ul>

                        <div class="mb-1 p-4 lang_form" id="en-form">
                            <div class="form-group">
                                <label>Speciality <small style="color: red">*</small></label>
                                <select name="speciality_id_en" class="form-control speciality_id" required>
                                    <option value="">Select Speciality</option>
                                    @if(count($specialities) > 0)
                                        @foreach($specialities as $sp)
                                            <option value="{{ $sp->id }}"
                                                {{ isset($disease_en) && $sp->id == $disease_en->speciality_id ? 'selected' : '' }}>
                                                {{ $sp->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="input-label">Name</label>
                                <input type="text" name="name_en"
                                       value="{{ $disease_en->name ?? '' }}"
                                       class="form-control" placeholder="Disease Name" required>
                            </div>
                        </div>

                        <div class="mb-1 p-4 lang_form d-none" id="fr-form">
                            <div class="form-group">
                                <label>Speciality <small style="color: red">*</small></label>
                                <select name="speciality_id_fr" class="form-control js-select2-custom">
                                    <option value="">Select Speciality</option>
                                    @if(isset($french_specialities))
                                        @foreach($french_specialities as $sp)
                                            <option value="{{ $sp->id }}"
                                                {{ isset($disease_fr) && $sp->id == $disease_fr->speciality_id ? 'selected' : '' }}>
                                                {{ $sp->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="input-label">Name <small style="color: red">*</small></label>
                                <input type="text" name="name_fr"
                                       value="{{ $disease_fr->name ?? '' }}"
                                       class="form-control" placeholder="Disease Name">
                            </div>
                        </div>

                        <div class="mb-1 p-4 form-group">
                            <label>Image</label> (Ratio 1:1)
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">Choose file</label>
                            </div>
                            <hr>
                        </div>

                        <div class="col-12">
                            <center>
                                @php
                                    $imagePath = isset($disease->image) && $disease->image
                                        ? ((env('APP_ENV') == 'local')
                                            ? asset('storage/disease/' . $disease->image)
                                            : asset('storage/app/public/disease/' . $disease->image))
                                        : asset('public/assets/admin/img/placeholder.png');
                                @endphp
                                <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                                     src="{{ $imagePath }}" alt=""/>
                            </center>
                        </div>

                        <input name="position" value="1" type="hidden">
                        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    $('.speciality_id').select2();

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

    var lang = "{{ $disease->lang_code ?? 'en' }}";

    if (lang === 'en') {
        $("#en-form").find('input, select').attr('required', 'required');
        $("#fr-form").find('input, select').removeAttr('required');
    } else {
        $("#fr-form").find('input, select').attr('required', 'required');
        $("#en-form").find('input, select').removeAttr('required');
    }

    $(".lang_link").click(function (e) {
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');
        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        $("#" + lang + "-form").removeClass('d-none');
        $("#lang_code").val(lang);

        if (lang === 'en') {
            $("#en-form").find('input, select').attr('required', 'required');
            $("#fr-form").find('input, select').removeAttr('required');
        } else {
            $("#fr-form").find('input, select').attr('required', 'required');
            $("#en-form").find('input, select').removeAttr('required');
        }
    });
</script>
@endpush
