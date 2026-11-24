@extends('layouts.lab.app')

@section('title','Update lab test')

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
                    <h1 class="page-header-title"><i class="tio-edit"></i>  {{__('messages.update')}} Lab Test
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?php
                    $labTest_en = \App\Models\labTest::where(['ltid' => "lt".$labTest->id,'lang_code' => 'en'])->first();
                    $labTest_fr =  \App\Models\labTest::where(['ltid' => "lt".$labTest->id,'lang_code' => 'fr'])->first();
                    ?>
                    <div class="card-header">
                        <h5>{{isset($labTest_en)?__('messages.update'):__('messages.add').' '.__('messages.new')}} Lab Test</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('lab.lab-test.update',[$labTest_en['id']])}}" method="post" enctype="multipart/form-data">
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
                                        <label class="input-label" for="category_id_en"> Category <small style="color: red">* </small></label>
                                        <select name="category_id_en" class="form-control js-select2-custom">
                                            <option value="">Select Category</option>
                                            <?php
                                                if(isset($categories) && !empty($categories)){
                                                    foreach($categories as $key => $value){
                                                        if($value->id == $labTest_en['category_id']){
                                                             $selected = "selected=selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                            ?>
                                            <option value="<?=$value->id?>" {{ $selected }}><?=$value->name?></option>
        
                                            <?php }} ?>
                                        </select>
                                    </div>
                               
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">*</small> </label>
                                    <input type="text" name="name_en" value="{{isset($labTest_en)?$labTest_en['name']:''}}"  class="form-control" placeholder=" {{__('messages.name')}}">
                                </div>

                                
                                <div class="form-group">
                                    <label class="input-label" for="image_en">{{__('messages.image')}} <small style="color: red">*</small></label>
                                    <input type="file" name="image_en" class="form-control" id="image_en_input" accept="image/*">
                                    <img id="image_en_preview" src="{{ isset($labTest_en) ? asset('storage/app/public/'.$labTest_en->image) : asset('default-image.png') }}" class="mt-2 img-thumbnail" width="150">
                                </div>
                                
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        function previewImage(inputId, previewId, defaultSrc) {
                                            let inputElement = document.getElementById(inputId);
                                            let previewElement = document.getElementById(previewId);
                                    
                                            if (inputElement && previewElement) {
                                                inputElement.addEventListener('change', function(event) {
                                                    let file = event.target.files[0];
                                    
                                                    if (file) {
                                                        let reader = new FileReader();
                                                        reader.onload = function() {
                                                            previewElement.src = reader.result;
                                                            previewElement.style.display = 'block';
                                                        };
                                                        reader.readAsDataURL(file);
                                                    } else {
                                                        previewElement.src = defaultSrc; // Restore default image if no file is selected
                                                    }
                                                });
                                            } else {
                                                console.error(`Element missing: inputId=${inputId}, previewId=${previewId}`);
                                            }
                                        }
                                    
                                        // Store existing images to revert if needed
                                        let defaultImageEn = document.getElementById('image_en_preview').src;
                                        let defaultImageFr = document.getElementById('image_fr_preview').src;
                                    
                                        previewImage('image_en_input', 'image_en_preview', defaultImageEn);  
                                        previewImage('image_fr_input', 'image_fr_preview', defaultImageFr); 
                                    });
                            </script>



                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.description')}} <small style="color: red">*</small> </label>
                                    <textarea type="text" name="description_en" class="form-control ckeditor" required>{{isset($labTest_en)?$labTest_en['description']:''}}</textarea>
                                </div>

                               
                             </div>
                             
                             <div class="mb-1 p-4 lang_form d-none" id="fr-form">
                                <div class="form-group">
                                    <label class="input-label" for="category_id_fr"> Category <small style="color: red">* </small></label>
                                    <select name="category_id_fr" class="form-control js-select2-custom">
                                        <option value="">Select Category</option>
                                        <?php
                                            if(isset($french_categories) && !empty($french_categories)){
                                                foreach($french_categories as $key => $value){
                                                    if(isset($labTest_fr) && !empty($labTest_fr)){
                                                        if($value->id == $labTest_fr['category_id']){
                                                             $selected = "selected=selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                    } else {
                                                            $selected = "";
                                                        }
                                                    
                                            ?>
                                            <option value="<?=$value->id?>" {{ $selected }}><?=$value->name?></option>
    
                                        <?php }} ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">*</small> </label>
                                    <input type="text" name="name_fr" value="{{isset($labTest_fr)?$labTest_fr['name']:''}}"  class="form-control" placeholder=" {{__('messages.name')}}"
                                        >
                                </div>
                                
                                <div class="form-group">
                                    <label class="input-label" for="image_fr">{{__('messages.image')}} <small style="color: red">*</small></label>
                                    <input type="file" name="image_fr" class="form-control" id="image_fr_input" accept="image/*">
                                    <img id="image_fr_preview" src="{{ isset($labTest_fr) ? asset('storage/app/public/'.$labTest_fr->image) : asset('default-image.png') }}" class="mt-2 img-thumbnail" width="150">
                                </div>


                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.description')}} <small style="color: red">*</small> </label>
                                    <textarea type="text" name="description_fr" class="form-control ckeditor" required>{{isset($labTest_fr)?$labTest_fr['description']:''}}</textarea>
                                </div>

                               
                             </div>


                             <div class="mb-1 p-4 form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.price')}} <small style="color: red">*</small> </label>
                                <input type="number" name="price" value="{{isset($labTest)?$labTest['price']:''}}"  class="form-control" min="0" placeholder=" {{__('messages.price')}}"
                                    required>
                            </div>
                           
                           
                           
                                
                           
                            <input name="position" value="1" style="display: none">
                            <button type="submit" class="btn btn-primary">{{isset($labTest_en)?__('messages.update'):__('messages.save')}}</button>
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

        var lang = "<?=$labTest->lang_code?>";
       

       if(lang == 'en')
       {
        //   $("#en-form").find('input, select').attr('required', 'required');
        //   $("#fr-form").find('input, select').removeAttr('required');
       }
       else
       {
        //   $("#fr-form").find('input, select').attr('required', 'required');
        //   $("#en-form").find('input, select').removeAttr('required');
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
