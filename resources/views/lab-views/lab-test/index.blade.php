@extends('layouts.lab.app')

@section('title','Add new lab test')

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
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} Lab Test
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{isset($category)?__('messages.update'):__('messages.add').' '.__('messages.new')}} Lab Test</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{isset($category)?route('lab.lab-test.update',[$category['id']]):route('lab.lab-test.store')}}" method="post" enctype="multipart/form-data">
                        @csrf       
                        <input type="hidden" name="lang_code" id="lang_code" value="en">    
                   
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                   <a class="nav-link lang_link active" href="#" id="en-link">English<span class="text-uppercase">(EN)</span></a>
                                </li>
                              {{--  <li class="nav-item">
                                   <a class="nav-link lang_link" href="#" id="fr-link">French<span class="text-uppercase">(FR)</span></a>
                                </li> --}}
                             </ul>
                             
                             <div class="mb-1 p-4 lang_form" id="en-form">

                                    <div class="form-group">
                                        <label class="input-label" for="category_id"> Category <small style="color: red">* </small></label>
                                        <select name="category_id_en" class="form-control js-select2-custom">
                                            <option value="">Select Category</option>
                                            <?php
                                                if(isset($categories) && !empty($categories)){
                                                    foreach($categories as $key => $value){
                                                  
        
                                            ?>
                                            <option value="<?=$value->id?>"><?=$value->name?></option>
        
                                            <?php }} ?>
                                        </select>
                                    </div>
                               
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">*</small> </label>
                                    <input type="text" name="name_en" value="{{isset($category)?$category['name']:''}}"  class="form-control" placeholder=" {{__('messages.name')}}"
                                        required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="input-label">{{ __('messages.image') }} <small style="color: red">*</small></label>
                                    <input type="file" name="image" class="form-control" id="imageInput" accept="image/*" required>
                                    <img id="imagePreview" src="" alt="Image Preview" style="display:none; max-width: 300px; margin-top: 10px;">
                                </div>


                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        function previewImage(inputId, previewId) {
                                            let inputElement = document.getElementById(inputId);
                                            let previewElement = document.getElementById(previewId);
                                
                                            if (inputElement && previewElement) { // Ensure both elements exist
                                                inputElement.addEventListener('change', function(event) {
                                                    let file = event.target.files[0];
                                
                                                    if (file) {
                                                        let reader = new FileReader();
                                                        reader.onload = function(){
                                                            previewElement.src = reader.result;
                                                            previewElement.style.display = 'block';
                                                        };
                                                        reader.readAsDataURL(file);
                                                    } else {
                                                        previewElement.src = "";
                                                        previewElement.style.display = 'none';
                                                    }
                                                });
                                            } else {
                                                console.error(`Element missing: inputId=${inputId}, previewId=${previewId}`);
                                            }
                                        }
                                
                                        // Ensure both inputs work
                                        previewImage('imageInput', 'imagePreview');  
                                        previewImage('image_fr', 'imagePreview_fr'); 
                                    });
                                </script>




                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.description')}} <small style="color: red">*</small> </label>
                                    <textarea type="text" name="description_en" class="form-control ckeditor" required></textarea>
                                </div>

                               
                             </div>
                             
                             <div class="mb-1 p-4 lang_form d-none" id="fr-form">
                                <div class="form-group">
                                    <label class="input-label" for="category_id"> Category <small style="color: red">* </small></label>
                                    <select name="category_id_fr" class="form-control js-select2-custom">
                                        <option value="">Select Category</option>
                                        <?php
                                            if(isset($french_categories) && !empty($french_categories)){
                                                foreach($french_categories as $key => $value){
                                              
    
                                        ?>
                                        <option value="<?=$value->id?>"><?=$value->name?></option>
    
                                        <?php }} ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">*</small> </label>
                                    <input type="text" name="name_fr" value="{{isset($category)?$category['name']:''}}"  class="form-control" placeholder="{{__('messages.category')}} {{__('messages.name')}}"
                                        required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="input-label">{{ __('messages.image') }} <small style="color: red">*</small></label>
                                    <input type="file" name="image_fr" class="form-control" id="image_fr" accept="image/*" required>
                                    <img id="imagePreview_fr" src="" alt="Image Preview" style="display:none; max-width: 300px; margin-top: 10px;">
                                </div>

                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.description')}} <small style="color: red">*</small> </label>
                                    <textarea type="text" name="description_fr" class="form-control ckeditor" required></textarea>
                                </div>

                               
                             </div>


                             <div class="mb-1 p-4 form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.price')}} <small style="color: red">*</small> </label>
                                <input type="number" name="price" value="{{isset($category)?$category['price']:''}}"  class="form-control" min="0" placeholder=" {{__('messages.price')}}"
                                    required>
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
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
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
