@extends('layouts.vendor.app')
@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp
@section('title','Add new product')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset($assetPrefixPath . '/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} {{__('messages.product')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
               


                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                           <a class="nav-link lang_link active" href="#" id="en-link">English (EN)</a>
                        </li>
                       {{-- <li class="nav-item">
                           <a class="nav-link lang_link" href="#" id="fr-link">French (FR)</a>
                        </li> --}}
                     </ul>
      
                      <input type="hidden" id="lang_name" value="en">


                    <div class="card mb-1 p-4 lang_form" id="en-form">
                        <form 
                        action="javascript:"
                         {{-- action="{{route('vendor.medicion.store')}}" --}}
                         method="post" 
                         id="food_form_en"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="lang_name" id="lang_code_en" value="en">



                        <input type="hidden" value="{{asset($assetPrefixPath.'/assets/admin/img/400x400/img2.jpg')}}" id="placeholder_image_path">


                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.name')}} <small style="color: red">* </small></label>
                                    <input type="text" name="name_en" class="form-control" placeholder="{{__('messages.product')}} {{__('messages.name')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.price')}} <small style="color: red">* </small></label>
                                    <input type="number" min="0" max="9999999999999999999999" step="0.01"  name="price_en" class="form-control"
                                           placeholder="Ex : 100" required>
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                           
                            <div class="col-md-6">
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
                            </div>
    
                            <div class="col-md-6">
    <div class="form-group">
        <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.type')}} <small style="color: red">* </small></label>
        <select name="product_type_en" id="product_type_en" class="form-control js-select2-custom" required>
            <option value="">Select Product Type</option>
            <?php
            if(isset($product_types) && !empty($product_types)){
                foreach($product_types as $key => $value){
            ?>
                <option value="<?=$value->id?>" data-name="<?=$value->name?>" data-unit="<?=$value->unit?>"><?=$value->name?></option>
            <?php }} ?>
        </select>
    </div>
</div>

<div class="col-md-6 unit-weight-section_en">
    <div class="form-group">
        <label class="input-label" for="unit_weight">Unit Weight <small style="color: red">* </small></label>
        <input type="text" name="unit_weight_en" id="unit_weight_en" class="form-control" placeholder="Unit Weight">
    </div>
</div>

<div class="col-md-6 piece-section_en d-none">
    <div class="form-group">
        <label class="input-label" for="total_pieces_en">Total Pieces <small style="color: red">* </small></label>
        <input type="number" name="total_pieces_en" id="total_pieces_en" class="form-control" placeholder="Total Pieces" min="0">
    </div>
</div>

                            
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="unit">Unit <small style="color: red">* </small></label>
                                    <select name="unit_en" class="form-control js-select2-custom">
                                        <option value="">Select Unit</option>
                                        <option value="ml">ml</option>
                                        <option value="piece">piece</option>
                                        <option value="gm">gm</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Total Stock <small style="color: red">* </small></label>
                                    <input type="text" name="total_stock_en" class="form-control" placeholder="Total Stock" required>
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufactured_by">Manufactured By <small style="color: red">* </small></label>
                                    <input type="text" name="manufactured_by_en" class="form-control" placeholder="Manufactured By" required>
                                </div>
                            </div>
    
                        </div>
    
                        <div class="row d-none">
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.discount')}} {{__('messages.type')}} <small style="color: red">* </small></label>
                                    <select name="discount_type_en" class="form-control js-select2-custom">
                                        <option value="percent">{{__('messages.percent')}}</option>
                                        <option value="amount">{{__('messages.amount')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.discount')}}</label>
                                    <input type="number" min="0" max="9999999999999999999999" value="0" name="discount_en" class="form-control"
                                           placeholder="Ex : 100" >
                                </div>
                            </div>
    
                        </div>
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{__('messages.description')}} <small style="color: red">* </small></label>
                            <textarea type="text" name="description_en" class="form-control ckeditor" required></textarea>
                        </div>
    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="text-dark">{{__('messages.product')}} Main Image</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                    </div>
            
                                    <center style="display: none" id="image-viewer-section" class="pt-2">
                                        <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                            src="{{asset($assetPrefixPath.'/assets/admin/img/400x400/img2.jpg')}}" alt="banner image"/>
                                    </center>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.images')}}</label>
                                    <div>
                                        <div class="row" id="coba1"></div>
                                    </div>
                                </div>
                            </div>
    
                           
                            
                            
    
                          
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufacturing_date">Manufacturing Date <small style="color: red">* </small></label>
                                    <input type="date" name="manufacturing_date_en" class="form-control" placeholder="Total Stock" required>
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="expiry_date">Expiry Date <small style="color: red">* </small></label>
                                    <input type="date" name="expiry_date_en" class="form-control" placeholder="Expiry Date" required>
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Batch Number <small style="color: red">* </small></label>
                                    <input type="text" name="batch_number_en" class="form-control" placeholder="Batch Number" required>
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="gst">GST (In %) <small style="color: red">* </small></label>
                                    <input type="number" name="gst_en" class="form-control" placeholder="GST (In %) " min="0" required>
                                </div>
                            </div>
    
                           
                        </div>

                        <br>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                    </form>

                    </div>
                  
                    <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
                        <form 
                        action="javascript:"
                         {{-- action="{{route('admin.product.store')}}" --}}
                         method="post" 
                         id="food_form_fr"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="lang_name" id="lang_code_fr" value="fr">



                        <input type="hidden" value="{{asset($assetPrefixPath.'/assets/admin/img/400x400/img2.jpg')}}" id="placeholder_image_path">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.name')}} <small style="color: red">* </small></label>
                                    <input type="text" name="name_fr" class="form-control" placeholder="{{__('messages.product')}} {{__('messages.name')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.price')}} <small style="color: red">* </small></label>
                                    <input type="number" min="0" max="9999999999999999999999" step="0.01"  name="price_fr" class="form-control"
                                            placeholder="Ex : 100" required>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row">
                            
                            <div class="col-md-6">
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
                            </div>
                    
                           <div class="col-md-6">
    <div class="form-group">
        <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.type')}} <small style="color: red">* </small></label>
        <select name="product_type_fr" id="product_type_fr" class="form-control js-select2-custom" required>
            <option value="">Select Product Type</option>
            <?php
            if(isset($french_product_types) && !empty($french_product_types)){
                foreach($french_product_types as $key => $value){
            ?>
                <option value="<?=$value->id?>" data-name="<?=$value->name?>" data-unit="<?=$value->unit?>"><?=$value->name?></option>
            <?php }} ?>
        </select>
    </div>
</div>

<div class="col-md-6 unit-weight-section_fr">
    <div class="form-group">
        <label class="input-label" for="unit_weight_fr">Unit Weight <small style="color: red">* </small></label>
        <input type="text" name="unit_weight_fr" id="unit_weight_fr" class="form-control" placeholder="Unit Weight">
    </div>
</div>

<div class="col-md-6 piece-section_fr d-none">
    <div class="form-group">
        <label class="input-label" for="total_pieces_fr">Total Pieces <small style="color: red">* </small></label>
        <input type="number" name="total_pieces_fr" id="total_pieces_fr" class="form-control" placeholder="Total Pieces" min="0">
    </div>
</div>

                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="unit">Unit <small style="color: red">* </small></label>
                                    <select name="unit_fr" class="form-control js-select2-custom">
                                        <option value="">Select Unit</option>
                                        <option value="ml">ml</option>
                                        <option value="piece">piece</option>
                                        <option value="gm">gm</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Total Stock <small style="color: red">* </small></label>
                                    <input type="text" name="total_stock_fr" class="form-control" placeholder="Total Stock" required>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufactured_by">Manufactured By <small style="color: red">* </small></label>
                                    <input type="text" name="manufactured_by_fr" class="form-control" placeholder="Manufactured By" required>
                                </div>
                            </div>
                    
                        </div>
                    
                        <div class="row d-none">
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.discount')}} {{__('messages.type')}} <small style="color: red">* </small></label>
                                    <select name="discount_type_fr" class="form-control js-select2-custom">
                                        <option value="percent">{{__('messages.percent')}}</option>
                                        <option value="amount">{{__('messages.amount')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.discount')}}</label>
                                    <input type="number" min="0" max="9999999999999999999999" value="0" name="discount_fr" class="form-control"
                                            placeholder="Ex : 100" >
                                </div>
                            </div>
                    
                        </div>
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{__('messages.description')}} <small style="color: red">* </small></label>
                            <textarea type="text" name="description_fr" class="form-control ckeditor" required></textarea>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="text-dark">{{__('messages.product')}} Main Image</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                    </div>
                    
                                    <center style="display: none" id="image-viewer-section" class="pt-2">
                                        <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                            src="{{asset($assetPrefixPath.'/assets/admin/img/400x400/img2.jpg')}}" alt="banner image"/>
                                    </center>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.images')}}</label>
                                    <div>
                                        <div class="row" id="coba"></div>
                                    </div>
                                </div>
                            </div>
                    
                            
                            
                            
                    
                            
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufacturing_date">Manufacturing Date <small style="color: red">* </small></label>
                                    <input type="date" name="manufacturing_date_fr" class="form-control" placeholder="Total Stock" required>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="expiry_date">Expiry Date <small style="color: red">* </small></label>
                                    <input type="date" name="expiry_date_fr" class="form-control" placeholder="Expiry Date" required>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Batch Number <small style="color: red">* </small></label>
                                    <input type="text" name="batch_number_fr" class="form-control" placeholder="Batch Number" required>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="gst">GST (In %) <small style="color: red">* </small></label>
                                    <input type="number" name="gst_fr" class="form-control" placeholder="GST (In %) " min="0" required>
                                </div>
                            </div>
                    
                            
                        </div>

                        <br>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                    </form>
                    
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
</script>
<script>
  $(document).ready(function() {
    // Listen for changes in product type dropdown
    $('#product_type_en').on('change', function() {
        var productType = $(this).find('option:selected');
        var unit = productType.data('unit');
        var name = productType.data('name');

        // Show or hide the inputs based on product type
        if (unit === 'pieces') {
            // If unit is pieces, show the Total Pieces field and hide Unit Weight
            $('.unit-weight-section_en').addClass('d-none');
            $('.piece-section_en').removeClass('d-none');
            
            // Remove 'required' attribute from unit_weight_en field if it's hidden
            $('#unit_weight_en').removeAttr('required');
            
            // Add 'required' attribute to total_pieces_en field if it's visible
            $('#total_pieces_en').attr('required', 'required');

            // Update label based on product name
            $('#total_pieces_en').attr('placeholder', 'Enter ' + name + ' pieces');
        } else if (unit === 'weight') {
            // If unit is weight, show the Unit Weight field and hide Total Pieces
            $('.unit-weight-section_en').removeClass('d-none');
            $('.piece-section_en').addClass('d-none');

            // Remove 'required' attribute from total_pieces_en field if it's hidden
            $('#total_pieces_en').removeAttr('required');
            
            // Add 'required' attribute to unit_weight_en field if it's visible
            $('#unit_weight_en').attr('required', 'required');

            // Update label based on product name
            $('#unit_weight_en').attr('placeholder', 'Enter ' + name + ' weight');
        } else {
            // Default case, hide both fields and remove 'required' from both
            $('.unit-weight-section_en').addClass('d-none');
            $('.piece-section_en').addClass('d-none');

            // Remove 'required' attribute from both fields
            $('#unit_weight_en').removeAttr('required');
            $('#total_pieces_en').removeAttr('required');
        }
    });

    // Before submitting, make sure the visible fields are validated
    $('form').on('submit', function(e) {
        var isValid = true;

        // Check if the visible input fields have values
        if ($('.unit-weight-section_en').is(':visible') && $('#unit_weight_en').val() === '') {
            isValid = false;
        }

        if ($('.piece-section_en').is(':visible') && $('#total_pieces_en').val() === '') {
            isValid = false;
        }

        // If validation fails, prevent form submission
        if (!isValid) {
            e.preventDefault();
            alert("Please fill in the required fields.");
        }
    });
});

$(document).ready(function() {
    // Listen for changes in product type dropdown for French product types
    $('#product_type_fr').on('change', function() {
        var productType = $(this).find('option:selected');
        var unit = productType.data('unit');
        var name = productType.data('name');

        // Show or hide the inputs based on product type
        if(unit === 'pieces') {
            // If unit is pieces, show the Total Pieces field and hide Unit Weight
            $('.unit-weight-section_fr').addClass('d-none');
            $('.piece-section_fr').removeClass('d-none');
            // Update label based on product name
            $('#total_pieces_fr').attr('placeholder', 'Enter ' + name + ' pieces');
        } else if(unit === 'weight') {
            // If unit is weight, show the Unit Weight field and hide Total Pieces
            $('.unit-weight-section_fr').removeClass('d-none');
            $('.piece-section_fr').addClass('d-none');
            // Update label based on product name
            $('#unit_weight_fr').attr('placeholder', 'Enter ' + name + ' weight');
        } else {
            // Default case, hide both fields
            $('.unit-weight-section_fr').addClass('d-none');
            $('.piece-section_fr').addClass('d-none');
        }
    });
});


</script>
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
            console.log("this",this)
            readURL(this);
            $('#image-viewer-section').show(1000);
        });

        function readURL2(input, id) {
                if (input.files && input.files[0]) {
                    console.log("ev",input)
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        // Set the source of the image
                        console.log("result",e.target.result)
                     
                        $('#viewer'+id).attr('src', e.target.result);
                        $('#color-image-viewer-section'+id).show();
                    };

                    // Read the selected file as a data URL
                    reader.readAsDataURL(input.files[0]);
                }
        }

        $(document).on("change",".customFileEg",function(ev){
    
            var id = $(this).attr("data-id");  
               
     
            readURL2(this,id);
            
        });
    </script>

    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
               
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });


            $('.js-select2-custom').prop("required",true)
        });
  
    </script>


    <script src="{{asset($assetPrefixPath . '/assets/admin')}}/js/tags-input.min.js"></script>


    <script>
        CKEDITOR.replace('description_en');
        CKEDITOR.replace('description_fr');
             var lang =  $("#lang_name").val();
       if(lang == 'en')
       {
        $('#food_form_en').on('submit', function (e) {
           
           e.preventDefault();
           var formData = new FormData(this);
            // Get CKEditor data
            var lang = $("#lang_name").val();
      

           var editorData = CKEDITOR.instances.description_en.getData();
           

           // Append CKEditor data to FormData
           formData.append('description_en', editorData);
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
           $.post({
               url: '{{route('vendor.medicion.store')}}',
               data: formData,
               cache: false,
               contentType: false,
               processData: false,
               beforeSend: function () {
                   $('#loading').show();
               },
               success: function (data) {
                   $('#loading').hide();
                   console.log("data",data.status);
                   if (data.status == 'error') {
                       toastr.error(data.message, {
                           CloseButton: true,
                           ProgressBar: true
                       });

                      
                   } else {
                       toastr.success(data.message, {
                           CloseButton: true,
                           ProgressBar: true
                       });
                       setTimeout(function () {
                           location.href = '{{\Request::server('HTTP_REFERER')??route('admin.product.list')}}';
                       }, 2000);
                   }
               }
           });
       });
       } else {
        $('#food_form_fr').on('submit', function (e) {
           
           e.preventDefault();
           var formData = new FormData(this);
            // Get CKEditor data
            var lang = $("#lang_name").val();
      

      
             var editorData = CKEDITOR.instances.description_fr.getData();
           

           // Append CKEditor data to FormData
           formData.append('description_fr', editorData);
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
           $.post({
               url: '{{route('vendor.medicion.store')}}',
               data: formData,
               cache: false,
               contentType: false,
               processData: false,
               beforeSend: function () {
                   $('#loading').show();
               },
               success: function (data) {
                   $('#loading').hide();
                   console.log("data",data.status);
                   if (data.status == 'error') {
                       toastr.error(data.message, {
                           CloseButton: true,
                           ProgressBar: true
                       });

                      
                   } else {
                       toastr.success(data.message, {
                           CloseButton: true,
                           ProgressBar: true
                       });
                       setTimeout(function () {
                           location.href = '{{\Request::server('HTTP_REFERER')??route('admin.product.list')}}';
                       }, 2000);
                   }
               }
           });
       });
       }
        
    </script>
      <script src="{{asset($assetPrefixPath.'/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
      <script type="text/javascript">
          
        $(function () {



     var lang = $("#lang_name").val();
       

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

            //  $("#product_type_en").on("change",function(){
            //     var product_type = $(this).val();
            //     if(product_type == ''){
            //         $("#unit_weight_en").prop("required",false);
            //         $("#total_pieces_en").prop("required",false);
            //         $(".unit-weight-section_en").removeClass('d-none');
            //         $(".piece-section_en").addClass('d-none');
            //     }
            //     if(product_type == '1' || product_type == '2'){
            //         $(".unit-weight-section_en").addClass('d-none');
            //         $(".piece-section_en").removeClass('d-none');
            //         $("#total_pieces_en").prop("required",true);
            //         $("#unit_weight_en").prop("required",false);
                    
            //     }

            //     if(product_type == '3' || product_type == '4'){
            //         $(".unit-weight-section_en").removeClass('d-none');
            //         $(".piece-section_en").addClass('d-none');
            //         $("#total_pieces_en").prop("required",false);
            //         $("#unit_weight_en").prop("required",true);
                 
            //     }

            //  });


            //  $("#product_type_fr").on("change",function(){
            //     var product_type = $(this).val();
            //     if(product_type == ''){
            //         $("#unit_weight_fr").prop("required",false);
            //         $("#total_pieces_fr").prop("required",false);
            //         $(".unit-weight-section_fr").removeClass('d-none');
            //         $(".piece-section_fr").addClass('d-none');
            //     }
            //     if(product_type == '1' || product_type == '2'){
            //         $(".unit-weight-section_fr").addClass('d-none');
            //         $(".piece-section_fr").removeClass('d-none');
            //         $("#total_pieces_fr").prop("required",true);
            //         $("#unit_weight_fr").prop("required",false);
                    
            //     }

            //     if(product_type == '3' || product_type == '4'){
            //         $(".unit-weight-section_fr").removeClass('d-none');
            //         $(".piece-section_fr").addClass('d-none');
            //         $("#total_pieces_fr").prop("required",false);
            //         $("#unit_weight_fr").prop("required",true);
                 
            //     }

            //  });

              $("#coba1").spartanMultiImagePicker({
                  fieldName: 'product_images[]',
                  maxCount: 6,
                  rowHeight: '120px',
                  groupClassName: 'col-lg-2 col-md-4 col-sm-4 col-6',
                  maxFileSize: '',
                  placeholderImage: {
                    image: $("#placeholder_image_path").val(),
                      width: '100%'
                  },
                  dropFileLabel: "Drop Here",
                  onAddRow: function (index, file) {
  
                  },
                  onRenderedPreview: function (index) {
  
                  },
                  onRemoveRow: function (index) {
  
                  },
                  onExtensionErr: function (index, file) {
                      toastr.error("{{__('messages.please_only_input_png_or_jpg_type_file')}}", {
                          CloseButton: true,
                          ProgressBar: true
                      });
                  },
                  onSizeErr: function (index, file) {
                      toastr.error("{{__('messages.file_size_too_big')}}", {
                          CloseButton: true,
                          ProgressBar: true
                      });
                  }
              });


              $("#coba").spartanMultiImagePicker({
                  fieldName: 'product_images[]',
                  maxCount: 6,
                  rowHeight: '120px',
                  groupClassName: 'col-lg-2 col-md-4 col-sm-4 col-6',
                  maxFileSize: '',
                  placeholderImage: {
                    image: $("#placeholder_image_path").val(),
                      width: '100%'
                  },
                  dropFileLabel: "Drop Here",
                  onAddRow: function (index, file) {
  
                  },
                  onRenderedPreview: function (index) {
  
                  },
                  onRemoveRow: function (index) {
  
                  },
                  onExtensionErr: function (index, file) {
                      toastr.error("{{__('messages.please_only_input_png_or_jpg_type_file')}}", {
                          CloseButton: true,
                          ProgressBar: true
                      });
                  },
                  onSizeErr: function (index, file) {
                      toastr.error("{{__('messages.file_size_too_big')}}", {
                          CloseButton: true,
                          ProgressBar: true
                      });
                  }
              });
        });


  
    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');
        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        $("#"+lang+"-form").removeClass('d-none');
        $("#lang_name").val(lang);
        

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


