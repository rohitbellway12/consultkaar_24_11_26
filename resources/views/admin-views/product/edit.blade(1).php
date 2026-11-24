@extends('layouts.admin.app')
@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp
@section('title','Update Product')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset($assetPrefixPath . '/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    @php($opening_time='')
    @php($closing_time='')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.product')}} {{__('messages.update')}}</h1>
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
                    <li class="nav-item">
                       <a class="nav-link lang_link" href="#" id="fr-link">French (FR)</a>
                    </li>
                 </ul>
        
                 <div class="card mb-1 p-4 lang_form" id="en-form">
                    <form action="javascript:" method="post" id="product_form_en"
                            enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="lang_code" id="lang_code_en" value="<?=$product->lang_code?>"> 

                        <input type="hidden" value="{{asset($assetPrefixPath.'/assets/admin/img/400x400/img2.jpg')}}" id="placeholder_image_path">
                        <div class="row">
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">* </small></label>
                                    <input type="text" name="name_en" value="{{$product['name']}}" class="form-control" placeholder="Product Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.price')}} <small style="color: red">* </small></label>
                                    <input type="number" value="{{$product['price']}}" min="0" max="100000" name="price_en"
                                            class="form-control" step="0.01"
                                            placeholder="Ex : 100" required>
                                </div>
                            </div>
                        </div>

                        <div class="row d-none">
                            

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.discount')}} {{__('messages.type')}} <small style="color: red">* </small></label>
                                    <select name="discount_type_en" class="form-control js-select2-custom">
                                        <option value="percent" {{$product['discount_type']=='percent'?'selected':''}}>
                                            {{__('messages.percent')}}
                                        </option>
                                        <option value="amount" {{$product['discount_type']=='amount'?'selected':''}}>
                                            {{__('messages.amount')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            

                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlSelect1">{{__('messages.category')}} <small style="color: red"> * </small></label>
                                  
                                 <?php


                                    ?>
                                    <select name="category_id_en"  class="form-control js-select2-custom">
                                            <option value="">---{{__('messages.select')}}---</option>
                                        @foreach($categories as $category)
                                      
                                      
                                        
                                        <option value="{{$category->id}}" {{ ($category->id == $product->category_id) ? 'selected' : ''}} >{{$category->name}}</option>
                                        @endforeach
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
                                            <option value="<?=$value->id?>" {{ ($value->id == $product['product_type_id']) ? 'selected' : ''}}><?=$value->name?></option>
                                    <?php }} ?>
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 unit-weight-section_en">
                                <div class="form-group">
                                    <label class="input-label" for="unit_weight">Unit Weight <small style="color: red">* </small></label>
                                    <input type="number"  name="unit_weight_en" id="unit_weight" class="form-control" placeholder="Unit Weight" min="0" value="{{$product['unit_weight']}}">
                                </div>
                            </div>

                            <div class="col-md-6 piece-section_en d-none">
                                <div class="form-group">
                                    <label class="input-label" for="unit_weight">Total Pieces <small style="color: red">* </small></label>
                                    <input type="number"  name="total_pieces_en" id="total_pieces_en" class="form-control" placeholder="Total Pieces" min="0" value="{{$product['total_pieces']}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="unit">Unit <small style="color: red">* </small></label>
                                    <select name="unit_en" class="form-control js-select2-custom">
                                        <option value="">Select Unit</option>
                                        <option value="ml" {{ ($product['unit'] == 'ml') ? 'selected=selected' : ''}}>ml</option>
                                        <option value="piece" {{ ($product['unit'] == 'piece') ? 'selected' : ''}}>piece</option>
                                        <option value="gm" {{ ($product['unit'] == 'gm') ? 'selected' : ''}}>gm</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Total Stock <small style="color: red">* </small></label>
                                    <input type="text" name="total_stock_en" class="form-control" placeholder="Total Stock" value="{{$product['total_stock']}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufactured_by">Manufactured By <small style="color: red">* </small></label>
                                    <input type="text" name="manufactured_by_en" class="form-control" placeholder="Manufactured By" value="{{$product['manufactured_by']}}" required>
                                </div>
                            </div>
                            

                        </div>

            

                        

                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1"> {{__('messages.description')}} <small style="color: red">* </small></label>
                            <textarea type="text" name="description_en" class="form-control ckeditor">{{$product['description']}}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('messages.product')}} {{__('messages.image')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                    </div>
                                        <?php

                                        $productImagePath = (env('APP_ENV') == 'local') ? asset('storage/product/' . $product['image']) : asset('storage/app/public/product/' . $product['image']);    
                                        ?>
            
                                    <center style="display: block" id="image-viewer-section" class="pt-2">
                                        <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                            src="{{$productImagePath}}"
                                            alt="product image"/>
                                    </center>
                                </div>
        
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.images')}} </label>
                                    <div>
                                        <div class="row" id="coba">
                                            @if(count($product->images) > 0)
                                            @foreach ($product->images as $key => $photo)
                                                @if(!str_contains($photo, 'video-'))
                                                <?php

                                                $productImagePath = (env('APP_ENV') == 'local') ? asset('storage/product/' . $photo) : asset('storage/app/public/product/' . $photo);    
                                                ?>
                                                    <div class="col-lg-2 col-md-4 col-sm-4 col-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <img style="width: 100%" height="auto"
                                                                    
                                                                        src="{{$productImagePath}}"
                                                                        alt="Product image">
                                                                <a href="{{route('admin.product.remove-image',['id'=>$product['id'],'name'=>$photo])}}"
                                                                    class="btn btn-danger btn-block">Remove</a>
            
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endIf
                                            @endforeach
                                            @endIf
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row">

                            

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufacturing_date">Manufacturing Date <small style="color: red">* </small></label>
                                    <input type="date" name="manufacturing_date_en" class="form-control"  value="{{$product['manufacturing_date']}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="expiry_date">Expiry Date <small style="color: red">* </small></label>
                                    <input type="date" name="expiry_date_en" value="{{$product['expiry_date']}}" class="form-control" placeholder="Expiry Date" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Batch Number <small style="color: red">* </small></label>
                                    <input type="text" name="batch_number_en" class="form-control" placeholder="Batch Number" value="{{$product['batch_number']}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="gst">GST (In %) <small style="color: red">* </small></label>
                                    <input type="number" name="gst_en" value="{{$product['gst']}}"  class="form-control" placeholder="GST (In %) " min="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <br>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
                    </form>

                 </div>


                 <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
            
                    <form action="javascript:" method="post" id="product_form_fr" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="lang_code" id="lang_code_fr" value="<?=$product->lang_code?>"> 
                       
                        <input type="hidden" value="{{!empty($assetPrefixPath) ? asset($assetPrefixPath.'/assets/admin/img/400x400/img2.jpg') : ''}}" id="placeholder_image_path">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">* </small></label>
                                    <input type="text" name="name_fr" value="{{!empty($product_fr['name']) ? $product_fr['name'] : ''}}" class="form-control" placeholder="Product Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.price')}} <small style="color: red">* </small></label>
                                    <input type="number" value="{{!empty($product_fr['price']) ? $product_fr['price'] : ''}}" min="0" max="100000" name="price_fr"
                                           class="form-control" step="0.01"
                                           placeholder="Ex : 100" required>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row d-none">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.discount')}} {{__('messages.type')}} <small style="color: red">* </small></label>
                                    <select name="discount_type_fr" class="form-control js-select2-custom">
                                        <option value="percent" {{!empty($product_fr['discount_type']) && $product_fr['discount_type'] == 'percent' ? 'selected' : ''}}>
                                            {{__('messages.percent')}}
                                        </option>
                                        <option value="amount" {{!empty($product_fr['discount_type']) && $product_fr['discount_type'] == 'amount' ? 'selected' : ''}}>
                                            {{__('messages.amount')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.discount')}}</label>
                                    <input type="number" min="0" value="{{!empty($product_fr['discount']) ? $product_fr['discount'] : ''}}" max="100000"
                                           name="discount_fr" class="form-control"
                                           placeholder="Ex : 100">
                                </div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlSelect1">{{__('messages.category')}} <small style="color: red"> * </small></label>
                                    <select name="category_id_fr"  class="form-control js-select2-custom">
                                            <option value="">---{{__('messages.select')}}---</option>
                                       
                                       <?php
                                            foreach($french_categories as $category){
                                                
                                                if(isset($product_fr) && !empty($product_fr)){
                                                    
                                                    $selected = ($category->id == $product_fr['category_id']) ? "selected=selected" : '';
                                                } else {
                                                    $selected = '';
                                                }


                                                ?>

                                        <option value="{{$category->id}}" {{ $selected  }} >{{$category->name}}</option>
                                                <?php } ?>
                                      
                                    </select>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.type')}} <small style="color: red">* </small></label>
                                    
                                    <select name="product_type_fr" id="product_type_fr" class="form-control js-select2-custom" required>
                                        <option value="">Select Product Type</option>
                                        <?php
                                        if(!empty($french_product_types) && !empty($french_product_types)){
                                        foreach($french_product_types as $key => $value){
                                        
                                    ?>
                                            <option value="<?=$value->id?>" {{ !empty($value->id) && !empty($product_fr['product_type_id']) && $value->id == $product_fr['product_type_id'] ? 'selected' : ''}}><?=$value->name?></option>
                                    <?php }} ?>
                                        
                                    </select>
                                </div>
                            </div>
                    
                            <div class="col-md-6 unit-weight-section_fr">
                                <div class="form-group">
                                    <label class="input-label" for="unit_weight">Unit Weight <small style="color: red">* </small></label>
                                    <input type="number"  name="unit_weight_fr" id="unit_weight" class="form-control" placeholder="Unit Weight" min="0" value="{{!empty($product_fr['unit_weight']) ? $product_fr['unit_weight'] : ''}}">
                                </div>
                            </div>
                    
                            <div class="col-md-6 piece-section_fr d-none">
                                <div class="form-group">
                                    <label class="input-label" for="unit_weight">Total Pieces <small style="color: red">* </small></label>
                                    <input type="number"  name="total_pieces_fr" id="total_pieces_fr" class="form-control" placeholder="Total Pieces" min="0" value="{{!empty($product_fr['total_pieces']) ? $product_fr['total_pieces'] : ''}}">
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="unit">Unit <small style="color: red">* </small></label>
                                    <select name="unit_fr" class="form-control js-select2-custom">
                                        <option value="">Select Unit</option>
                                        <option value="ml" {{ !empty($product_fr['unit']) && $product_fr['unit'] == 'ml' ? 'selected=selected' : ''}}>ml</option>
                                        <option value="piece" {{ !empty($product_fr['unit']) && $product_fr['unit'] == 'piece' ? 'selected' : ''}}>piece</option>
                                        <option value="gm" {{ !empty($product_fr['unit']) && $product_fr['unit'] == 'gm' ? 'selected' : ''}}>gm</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Total Stock <small style="color: red">* </small></label>
                                    <input type="text" name="total_stock_fr" class="form-control" placeholder="Total Stock" value="{{!empty($product_fr['total_stock']) ? $product_fr['total_stock'] : ''}}" required>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufactured_by">Manufactured By <small style="color: red">* </small></label>
                                    <input type="text" name="manufactured_by_fr" class="form-control" placeholder="Manufactured By" value="{{!empty($product_fr['manufactured_by']) ? $product_fr['manufactured_by'] : ''}}" required>
                                </div>
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1"> {{__('messages.description')}} <small style="color: red">* </small></label>
                            <textarea type="text" name="description_fr" class="form-control ckeditor">{{!empty($product_fr['description']) ? $product_fr['description'] : ''}}</textarea>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{__('messages.product')}} {{__('messages.image')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                    </div>
                                        <?php
                                        $product_frImagePath = (!empty(env('APP_ENV')) && env('APP_ENV') == 'local') ? asset('storage/product/' . (!empty($product_fr['image']) ? $product_fr['image'] : '')) : asset('storage/app/public/product/' . (!empty($product_fr['image']) ? $product_fr['image'] : ''));    
                                        ?>
                                        <center style="display: block" id="image-viewer-section" class="pt-2">
                                            <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                                src="{{$product_frImagePath}}"
                                                alt="product image"/>
                                        </center>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{__('messages.product')}} {{__('messages.images')}} </label>
                                    <div>
                                        <div class="row" id="coba">
                                            @if(!empty($product_fr->images) && count($product_fr->images) > 0)
                                            @foreach ($product_fr->images as $key => $photo)
                                                @if(!str_contains($photo, 'video-'))
                                                <?php
                                                $product_frImagePath = (!empty(env('APP_ENV')) && env('APP_ENV') == 'local') ? asset('storage/product/' . $photo) : asset('storage/app/public/product/' . $photo);    
                                                ?>
                                                    <div class="col-lg-2 col-md-4 col-sm-4 col-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <img style="width: 100%" height="auto"
                                                                        src="{{$product_frImagePath}}"
                                                                        alt="Product image">
                                                                <a href="{{route('admin.product.remove-image',['id'=>(!empty($product_fr['id']) ? $product_fr['id'] : ''),'name'=>$photo])}}"
                                                                    class="btn btn-danger btn-block">Remove</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="manufacturing_date">Manufacturing Date <small style="color: red">* </small></label>
                                    <input type="date" name="manufacturing_date_fr" class="form-control"  value="{{!empty($product_fr['manufacturing_date']) ? $product_fr['manufacturing_date'] : ''}}" required>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="expiry_date">Expiry Date <small style="color: red">* </small></label>
                                    <input type="date" name="expiry_date_fr" value="{{!empty($product_fr['expiry_date']) ? $product_fr['expiry_date'] : ''}}" class="form-control" placeholder="Expiry Date" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">Batch Number <small style="color: red">* </small></label>
                                    <input type="text" name="batch_number_fr" class="form-control" placeholder="Batch Number" value="{{!empty($product_fr['batch_number']) ? $product_fr['batch_number'] : ''}}" required>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="gst">GST (In %) <small style="color: red">* </small></label>
                                    <input type="number" name="gst_fr" value="{{!empty($product_fr['gst']) ? $product_fr['gst'] : ''}}"  class="form-control" placeholder="GST (In %) " min="0" required>
                                </div>
                            </div>
                        </div>
                    
                        <br>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
                    </form>
                    
                 </div>



              
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush

@push('script_2')

<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
       // $('.ckeditor').ckeditor();
    });
</script>
    <script>
        function getRestaurantData(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
            $.get({
                url:'{{url('/')}}/api/v1/restaurants/details/'+vendor_id,
                dataType: 'json',
                success: function(data) {
                    if(data.available_time_starts != null && data.available_time_ends != null){
                        var opening_time = data.available_time_starts;
                        var closeing_time = data.available_time_ends;
                        $('#available_time_ends').attr('min', opening_time);
                        $('#available_time_starts').attr('min', opening_time);
                        $('#available_time_ends').attr('max', closeing_time);
                        $('#available_time_starts').attr('max', closeing_time);
                        $('#available_time_starts').val(opening_time);
                        $('#available_time_ends').val(closeing_time);
                    }                    
                },
            });
        }

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
            console.log("ev",ev)
            $('#image-viewer-section').show(1000)
        });

    

 // Function to read the selected image file and set the source
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
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            $('.js-select2-custom').prop("required",true)
        });
        
        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{url('/')}}/admin/vendor/get-restaurants',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });
        
    </script>

    <script src="{{asset($assetPrefixPath . '/assets/admin')}}/js/tags-input.min.js"></script>

    <script>
        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            combination_update();
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name;
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="Choice Title" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="Enter choice values" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        setTimeout(function () {
            $('.call-update-sku').on('change', function () {
                combination_update();
            });
        }, 2000)

      


     
    </script>

    <!-- submit form -->
    <script>
     
  

       CKEDITOR.replace('description_en');

        CKEDITOR.replace('description_fr');


        var lang =  $('input[name="lang_code"]').val();
       if(lang == 'en')
       {
        $('#product_form_en').on('submit', function () {
            var formData = new FormData(this);
             // Get CKEditor data
             var editorData = CKEDITOR.instances.description_en.getData();

            // Append CKEditor data to FormData
            formData.append('description_en', editorData);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',[$product['id']])}}',
                data: $('#product_form_en').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    if (data.status == 'error') {
                        toastr.error(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });

                       
                    }else {
                        toastr.success('Product updated successfully!', {
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
        $('#product_form_fr').on('submit', function () {
            var formData = new FormData(this);
             // Get CKEditor data
             var editorData = CKEDITOR.instances.description_fr.getData();

            // Append CKEditor data to FormData
            formData.append('description_fr', editorData);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',[$product['id']])}}',
                data: $('#product_form_fr').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    if (data.status == 'error') {
                        toastr.error(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });

                       
                    }else {
                        toastr.success('Product updated successfully!', {
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

            $(".coba0").spartanMultiImagePicker({
                fieldName: 'product_colored_images[0][]',
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
        var lang = "<?=$product->lang_code?>";

       if(lang == 'en')
       {
         //  $("#en-form").find('input, select').attr('required', 'required');
       //    $("#fr-form").find('input, select').removeAttr('required');
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
            

                if(lang == 'en')
                {
                    $("#lang_code_en").val(lang);
                // $("#en-form").find('input, select,textarea').attr('required', 'required');
                //  $("#fr-form").find('input, select,textarea').removeAttr('required');
                }
                else
                {
                    $("#lang_code_fr").val(lang);
                    
                    //$("#fr-form").find('input, select,textarea').attr('required', 'required');
                //  $("#en-form").find('input, select,textarea').removeAttr('required');
                }
         });
    </script>
@endpush


