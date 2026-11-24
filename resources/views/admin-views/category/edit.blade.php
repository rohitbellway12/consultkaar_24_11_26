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
                        <form action="{{route('admin.category.update',[$category['id']])}}" method="post" enctype="multipart/form-data">
                        @csrf                        
                           
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} <small style="color: red">*</small></label>
                                <input type="text" name="name" value="{{isset($category)?$category['name']:''}}"  class="form-control" placeholder="{{__('messages.category')}} {{__('messages.name')}}"
                                    required>
                            </div>
                                <div class="form-group">
                                    <label>{{__('messages.image')}}</label>( {{__('messages.ratio')}} 1:1 )</small>
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
                                        <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
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
    
    </script>

@endpush
