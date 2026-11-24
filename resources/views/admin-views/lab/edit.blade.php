@extends('layouts.admin.app')

@section('title','Update lab')

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
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.update')}} Lab</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    @php
                        $lab_en = \App\Models\Lab::where(['lid' => "l".$lab->id, 'lang_code' => 'en'])->first();
                        $lab_fr = \App\Models\Lab::where(['lid' => "l".$lab->id, 'lang_code' => 'fr'])->first();
                    @endphp
                    <div class="card-header">
                        <h5>{{isset($lab_en) ? __('messages.update') : __('messages.add').' '.__('messages.new')}} Lab</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.lab.update', [$lab['id']]) }}" method="post" enctype="multipart/form-data">
                        @csrf       
                        <input type="hidden" name="lang_code" id="lang_code" value="en">    
                   
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                   <a class="nav-link lang_link active" href="#" id="en-link">English <span class="text-uppercase">(EN)</span></a>
                                </li>
                                <li class="nav-item">
                                   <a class="nav-link lang_link" href="#" id="fr-link">French <span class="text-uppercase">(FR)</span></a>
                                </li>
                             </ul>
                             
                             <div class="mb-1 p-4 lang_form" id="en-form">
                                <div class="form-group">
                                    <label class="input-label">{{__('messages.name')}} <small style="color: red">*</small> </label>
                                    <input type="text" name="name_en" value="{{isset($lab_en) ? $lab_en['name'] : ''}}" class="form-control" placeholder="{{__('messages.name')}}" required>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.description')}} <small style="color: red">*</small> </label>
                                    <textarea name="description_en" class="form-control ckeditor" required>{{ isset($lab_en) ? $lab_en['description'] : '' }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.email')}} <small style="color: red">*</small> </label>
                                    <input type="email" name="email" value="{{ isset($lab_en) ? $lab_en['email'] : '' }}" class="form-control" placeholder="{{__('messages.email')}}" required>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.password')}} </label>
                                    <input type="password" name="password" class="form-control" placeholder="{{__('messages.password')}}">
                                  
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.address')}} <small style="color: red">*</small> </label>
                                    <textarea name="address" class="form-control" placeholder="{{__('messages.address')}}" required>{{ isset($lab_en) ? $lab_en['address'] : '' }}</textarea>
                                </div>
                             </div>
                             
                             <div class="mb-1 p-4 lang_form d-none" id="fr-form">
                                <div class="form-group">
                                    <label class="input-label">{{__('messages.name')}} <small style="color: red">*</small> </label>
                                    <input type="text" name="name_fr" value="{{isset($lab_fr) ? $lab_fr['name'] : ''}}" class="form-control" placeholder="{{__('messages.name')}}" required>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.description')}} <small style="color: red">*</small> </label>
                                    <textarea name="description_fr" class="form-control ckeditor" required>{{ isset($lab_fr) ? $lab_fr['description'] : '' }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.email')}} <small style="color: red">*</small> </label>
                                    <input type="email" name="email" value="{{ isset($lab_fr) ? $lab_fr['email'] : '' }}" class="form-control" placeholder="{{__('messages.email')}}" required>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.password')}} </label>
                                    <input type="password" name="password" class="form-control" placeholder="{{__('messages.password')}}">
                                
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{__('messages.address')}} <small style="color: red">*</small> </label>
                                    <textarea name="address" class="form-control" placeholder="{{__('messages.address')}}" required>{{ isset($lab_fr) ? $lab_fr['address'] : '' }}</textarea>
                                </div>
                             </div>

                            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

<script type="text/javascript">
   $(document).ready(function () {
        $('.ckeditor').ckeditor();
   });

    $("#en-form").find('input, select,textarea').attr('required', 'required');
    $("#fr-form").find('input, select,textarea').removeAttr('required');

    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');
        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        $("#"+lang+"-form").removeClass('d-none');
        $("#lang_code").val(lang);

        if(lang == 'en') {
            $("#en-form").find('input, select, textarea').attr('required', 'required');
            $("#fr-form").find('input, select, textarea').removeAttr('required');
        } else {
            $("#fr-form").find('input, select, textarea').attr('required', 'required');
            $("#en-form").find('input, select, textarea').removeAttr('required');
        }
    });
</script>
@endpush
