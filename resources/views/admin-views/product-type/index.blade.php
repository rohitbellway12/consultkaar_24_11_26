@extends('layouts.admin.app')

@section('title','Add new product type')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} Product Type
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{isset($category)?__('messages.update'):__('messages.add').' '.__('messages.new')}} Product Type</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{isset($category)?route('admin.product-type.update',[$category['id']]):route('admin.product-type.store')}}" method="post">
                        @csrf      
                        
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
        <label class="input-label" for="name_en">{{__('messages.name')}} <small style="color: red">*</small> </label>
        <input type="text" name="name_en" value="{{ isset($category) ? $category['name'] : '' }}" class="form-control" placeholder="{{__('messages.name')}}" required>
    </div>

    <!-- Weight/Pieces Dropdown for English -->
    <div class="form-group">
        <label class="input-label" for="unit_en">Unit Type <small style="color: red">*</small></label>
        <select name="unit_en" id="unit_en" class="form-control" required>
            <option value="">Select Unit</option>
            <option value="weight" {{ isset($category) && $category['unit'] == 'weight' ? 'selected' : '' }}>Weight</option>
            <option value="pieces" {{ isset($category) && $category['unit'] == 'pieces' ? 'selected' : '' }}>Pieces</option>
        </select>
    </div>
</div>

<div class="mb-1 p-4 lang_form d-none" id="fr-form">
    <div class="form-group">
        <label class="input-label" for="name_fr">{{__('messages.name')}} <small style="color: red">*</small> </label>
        <input type="text" name="name_fr" value="{{ isset($category) ? $category['name'] : '' }}" class="form-control" placeholder="{{__('messages.name')}}" required>
    </div>

    <!-- Weight/Pieces Dropdown for French -->
    <div class="form-group">
        <label class="input-label" for="unit_fr">Unit Type <small style="color: red">*</small></label>
        <select name="unit_fr" id="unit_fr" class="form-control" required>
            <option value="">Sélectionner l'unité</option>
            <option value="weight" {{ isset($category) && $category['unit'] == 'weight' ? 'selected' : '' }}>Weight</option>
            <option value="pieces" {{ isset($category) && $category['unit'] == 'pieces' ? 'selected' : '' }}>Pieces</option>
        </select>
    </div>
</div>

                           
                            <input name="position" value="1" style="display: none">
                            <button type="submit" class="btn btn-primary">{{isset($category)?__('messages.update'):__('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
   $("#en-form").find('input, select, textarea').attr('required', 'required');
   $("#fr-form").find('input, select, textarea').removeAttr('required');

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
