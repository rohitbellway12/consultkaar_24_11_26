@extends('layouts.admin.app')

@section('title','Update location')

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
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update')}} location</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.location.update', [$location->id])}}" method="post" id="location_form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{__('messages.name')}}</label>
                                    <small style="color: red">* </small>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ $location->name }}" required>
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
    $(document).on('ready', function () {
      

      



        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });   
    });

        function location_type_change(order_type) {
           if(order_type=='service_wise')
            {
                $('#restaurant_wise').hide();
                $('#item_wise').show();
            }
            else if(order_type=='vendor_wise')
            {
                $('#restaurant_wise').show();
                $('#item_wise').hide();
            }
            else{
                $('#item_wise').hide();
                $('#restaurant_wise').hide();
            }
        }
        @if($location->type == 'service_wise')
        getRequest('{{url('/')}}/admin/service/get-foods?zone_id={{$location->zone_id}}&data[]={{$location->data}}','choice_item');
        @endif 
        $('#location_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.location.update', [$location['id']])}}',
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
                        toastr.success('location updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.location.add-new')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
@endpush
