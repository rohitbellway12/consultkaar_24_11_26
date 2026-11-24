@extends('layouts.admin.app')

@section('title','Update category')

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
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.update')}}  {{__('messages.category')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">                     
                  
                    <div class="card-body">

                        <?php
                        $category_en = \App\Models\LabTestCategory::where(['cid' => "c".$category->id,'lang_code' => 'en'])->first();
                        $category_fr =  \App\Models\LabTestCategory::where(['cid' => "c".$category->id,'lang_code' => 'fr'])->first();
                        $category = \App\Models\LabTestCategory::where(['id' => $category->id])->first();
                        ?>



                        <form action="{{route('admin.lab-test-category.update',[$category['id']])}}" method="post" enctype="multipart/form-data">
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
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">*</small></label>
                                <input type="text" name="name_en" value="{{isset($category_en)?$category_en['name']:''}}"  class="form-control" placeholder="{{__('messages.category')}} {{__('messages.name')}}"
                                    required>
                            </div>
                               
                         </div>
                         <div class="mb-1 p-4 lang_form d-none" id="fr-form">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">*</small></label>
                                <input type="text" name="name_fr" value="{{isset($category_fr)?$category_fr['name']:''}}"  class="form-control" placeholder="{{__('messages.category')}} {{__('messages.name')}}"
                                    required>
                            </div>
                               
                         </div>


                         <div class="mb-1 p-4 form-group">
                            <label>{{__('messages.image')}}</label> <small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                            </div>
                            <hr>
                        </div>
                        <div class="col-12">
                            <center>
                                @php
                                   $imagePath = (env('APP_ENV') == 'local') ? asset('storage/category/' . $category['image']) : asset('storage/app/public/category/' . $category['image']);

                                   @endphp
                                <img style="width: 10%;border: 1px solid; border-radius: 10px;" id="viewer"
                                     src="{{$imagePath}}" alt=""/>
                            </center>
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

        var lang = "<?=$category->lang_code?>";
       

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
