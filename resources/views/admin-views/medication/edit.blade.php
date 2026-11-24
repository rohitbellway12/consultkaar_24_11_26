@extends('layouts.admin.app')

@section('title','Update medication')

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
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update')}} Medication</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.medication.update', [$medication->id])}}" method="post" id="medication_form">
                            @csrf
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                   <a class="nav-link lang_link active" href="#" id="en-link">English<span class="text-uppercase">(EN)</span></a>
                                </li>
                             {{--   <li class="nav-item">
                                   <a class="nav-link lang_link" href="#" id="fr-link">French<span class="text-uppercase">(FR)</span></a>
                                </li> --}}
                             </ul>
                             <?php
                             $medication_en =  \App\Models\Medication::where(['mid' => "m".$medication->id,'lang_code' => 'en'])->first();
                              $medication_fr =  \App\Models\Medication::where(['mid' => "m".$medication->id,'lang_code' => 'fr'])->first();
                              $medication = \App\Models\Medication::where(['id' => $medication->id])->first();

                             ?>
                        <input type="hidden" name="lang_code" id="lang_code" value="en">
                        <div class="card mb-1 p-4 lang_form" id="en-form">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{__('messages.name')}}</label>
                                        <small style="color: red">* </small>
                                        <input type="text" name="name_en" class="form-control" value="{{ $medication_en['name'] ?? '' }}" required> 
                                        <br>                                    
                                    </div>                             
                                </div>
                             </div>
                             <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{__('messages.name')}}</label>
                                        <small style="color: red">* </small>
                                        <input type="text" name="name_fr"  class="form-control" value="{{ $medication_fr['name'] ?? '' }}" required> 
                                        <br>                                    
                                    </div>                             
                                </div>
                             </div>
                            <br>  
                            <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
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

     
        $('#medication_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.medication.update', [$medication['id']])}}',
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
                        toastr.success('Medication updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.medication.add-new')}}';
                        }, 2000);
                    }
                }
            });
        });
        
        var lang = "<?=$medication->lang_code?>";

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
    </script>
@endpush
