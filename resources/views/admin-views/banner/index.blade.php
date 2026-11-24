@extends('layouts.admin.app')

@section('title','Banner')

@push('css_or_js')

@endpush
@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
  $storagePath = ($appEnv == 'local') ? asset('/storage/banner') : asset('/storage/app/public/banner');
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} {{__('messages.banner')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.banner.store')}}" method="post" id="banner_form" enctype="multipart/form-data">
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
                                            <label class="input-label qcont" for="speciality_id">Speciality <small style="color: red">* </small> </label> 
                                            <select name="speciality_id_en"  class="form-control speciality_id" required>
                                                <option value="">Select Speciality</option>
                                                <?php
                                                    if(count($specialities) > 0){
                                                    foreach ($specialities as $key=>$sp){
                
                                                ?>
                                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                
                                                <?php }} ?>
                                            </select>
                                        </div>
                                   
                                </div>


                                <div class="mb-1 p-4 lang_form d-none" id="fr-form">
                                    <div class="form-group">
                                        <label class="input-label qcont" for="speciality_id">Speciality <small style="color: red">* </small> </label> 
                                        <select name="speciality_id_fr"  class="form-control speciality_id" required>
                                            <option value="">Select Speciality</option>
                                            <?php
                                                if(count($french_specialities) > 0){
                                                foreach ($french_specialities as $key=>$sp){
            
                                            ?>
                                                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
            
                                            <?php }} ?>
                                        </select>
                                    </div>
                               
                            </div>

                                 


                                    <!--</div>-->
                                    <!--------------------------------Rifal changes--------------------------------->
                                    <div class="row">
                                        <!-- Banner Image Upload -->
                                        <div class="form-group mb-1 p-4 col-md-4">
                                            <label>{{__('messages.banner')}} {{__('messages.image')}}</label>
                                            <small style="color: red">* ( {{__('messages.ratio')}} 3:1 )</small>
                                            <div class="custom-file">
                                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                                <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                            </div>
                                            <div class="form-group" style="margin-top:20px;">
                                                <center>
                                                    <img style="width:50%;border: 1px solid; border-radius: 10px;" id="viewer"
                                                        src="{{asset($assetPrefixPath . '/assets/admin/img/900x400/img1.jpg')}}" alt="banner image"/>
                                                </center>
                                            </div>
                                        </div>
                                    
                                        <!-- Banner For Dropdown -->
                                        <div class="form-group mb-1 p-4 col-md-4">
                                            <label>{{__('messages.banner')}} for</label>
                                            <small style="color: red">*</small>
                                            <select name="banner_for" class="form-control" id="bannerForDropdown" required>
                                                <option value="">Select</option>
                                                <option value="home">Home</option>
                                                <option value="lab_test">Lab Test</option>
                                            </select>
                                        </div>
                                    
                                        <!-- Lab Test Dropdown (Initially Hidden) -->
                                        <div class="form-group mb-1 p-4 col-md-4" id="labTestContainer" style="display: none;">
                                            <label>Select Lab Test</label>
                                            <small style="color: red">*</small>
                                            <select name="lab_test_id" class="form-control">
                                                <option value="">Select Lab Test</option>
                                                <option value="blood_test">Blood Test</option>
                                                <option value="urine_test">Urine Test</option>
                                                <option value="xray">X-Ray</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- jQuery to Show/Hide Lab Test Dropdown -->
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                    $(document).ready(function () {
                                        $('#bannerForDropdown').on('change', function () {
                                            if ($(this).val() === 'lab_test') {
                                                $('#labTestContainer').show(); // Show the dropdown
                                    
                                                // Fetch lab tests from the database
                                                $.ajax({
                                                    url: "{{ route('admin.banner.get-lab-tests') }}",
                                                    type: "GET",
                                                    success: function (data) {
                                                        let options = '<option value="">Select Lab Test</option>';
                                                        $.each(data, function (key, value) {
                                                            options += `<option value="${value.id}">${value.name}</option>`;
                                                        });
                                                        $('select[name="lab_test_id"]').html(options);
                                                    }
                                                });
                                    
                                            } else {
                                                $('#labTestContainer').hide(); // Hide the dropdown
                                            }
                                        });
                                    });
                                    </script>


                             
                                  
                                   
                              
                                <button type="submit" class="btn btn-primary mt-5">{{__('messages.submit')}}</button>

                            </div>
                        </form>
                    </div>
                </div>                
            </div>
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5>{{__('messages.banner')}} {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$banners->count()}}</span></h5>
                        <form id="search-form">
                            @csrf
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{__('messages.search_here')}}" aria-label="{{__('messages.search_here')}}">
                                <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                "order": [],
                                "orderCellsTop": true,
                                "search": "#datatableSearch",
                                "entries": "#datatableEntries",
                                "isResponsive": false,
                                "isShowPaging": false,
                                "paging": false,
                               }'>
                            <thead class="thead-light">
                                <tr>
                                    <th>{{__('messages.#')}}</th>
                                    <th>Speciality name</th>
                                    <th>{{__('messages.image')}}</th>
                                    <th>{{__('messages.status')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($banners as $key=>$banner)   
                            @php
                                   $specialityData  =   \App\Models\Speciality::where('id' , $banner->speciality_id)->first();
                                @endphp                            
                                <tr>
                                    <td>{{$key+$banners->firstItem()}}</td>
                                    <td>{{ $specialityData->name }}</td>
                                    <td>
                                        <span class="media align-items-center">
                                            <img class="avatar avatar-lg mr-3" src="{{$storagePath}}/{{$banner->image}}" 
                                                  alt=" image">
                                            <div class="media-body">
                                                <h5 class="text-hover-primary mb-0"></h5>
                                            </div>
                                        </span>
                                    <span class="d-block font-size-sm text-body"> </span>
                                    </td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$banner->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.banner.status',[$banner->id,$banner->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$banner->id}}" {{$banner->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-white" href="{{route('admin.banner.edit',[$banner->id])}}"title="{{__('messages.edit')}} {{__('messages.banner')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('banner-{{$banner->id}}','Want to delete this banner ?')" title="{{__('messages.delete')}} {{__('messages.banner')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.banner.delete',[$banner->id])}}"
                                                    method="post" id="banner-{{$banner->id}}">
                                                @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <div class="page-area">
                            <table>
                                <tfoot>
                                    {!! $banners->links() !!}
                                </tfoot>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
<script>
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
    });
</script>
<script>
    $(document).on('ready', function () {
        var zone_id = [];
        $('#zone').on('change', function(){
            if($(this).val())
            {
                zone_id = $(this).val();
            }
            else
            {
                zone_id = [];
            }
        });

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{url('/')}}/admin/vendor/get-restaurants',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        zone_ids: [zone_id],
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
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
                select: {
                    style: 'multi',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                    '<img class="mb-3" src="{{asset($assetPrefixPath . '/admin/svg/illustrations/sorry.svg')}}" alt="Image Description" style="width: 7rem;">' +
                    '<p class="mb-0">No data to show</p>' +
                    '</div>'
                }
            });

            $('#datatableSearch').on('mouseup', function (e) {
                var $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function(){
                    var newValue = $input.val();

                    if (newValue == ""){
                    // Gotcha
                    datatable.search('').draw();
                    }
                }, 1);
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
        $('#item_wise').hide();
        function banner_type_change(order_type) {
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

        $('#banner_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.banner.store')}}',
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
                        toastr.success('Banner uploaded successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.banner.add-new')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
    <script>
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.banner.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
        $(".speciality_id").select2();
    </script>
@endpush
