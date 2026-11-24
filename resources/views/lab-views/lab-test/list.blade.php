@extends('layouts.lab.app')

@section('title','Lab test List')

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
                    <h1 class="page-header-title">
                        <i class="tio-add-circle-outlined"></i> Lab Test  {{__('messages.list')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
     
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Lab Test  {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$lab_tests->total()}}</span></h5>
                        <form id="search-form" method="post">
                            <input type="hidden" name="search_by_subcategory" value ="yes">
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch"  name="search"  type="search" class="form-control" placeholder="Search lab test" aria-label="Search lab test">
                                <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive datatable-custom">
                            <table id="columnSearchDatatable"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                style="width: 100%;"
                                data-hs-datatables-options='{
                                    "search": "#datatableSearch",
                                    "entries": "#datatableEntries",
                                    "isResponsive": false,
                                    "isShowPaging": false,
                                    "paging":false,
                                }'>
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{__('messages.#')}}</th>
                                        <th>Image</th> 
                                        <th>Name</th>
                                        <th >{{__('messages.status')}}</th>
                                        <th >{{__('messages.action')}}</th>
                                    </tr>
                                </thead>

                                <tbody id="table-div">
                                @foreach($lab_tests as $key=>$lab_test)
                                <?php
                                $test_en =  \App\Models\LabTest::where(['ltid' => "lt".$lab_test->id,'lang_code' => 'en'])->first();
                                 $test_fr =  \App\Models\LabTest::where(['ltid' => "lt".$lab_test->id,'lang_code' => 'fr'])->first();
                                //   $imagePath = asset('storage/app/public/' . $lab_test->image);
                                ?>
                                    <tr>
                                        <td>{{$key+$lab_tests->firstItem()}}</td>
                                        <td>
                                            <img src="{{ asset('storage/app/public/' . $lab_test->image) }}" alt="Lab Test Image" width="100" height="50" class="rounded">
                                        </td>
                                        <td>{{$test_en->name ?? 'N/A'}}</td>  
                                        
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$lab_test->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('lab.lab-test.status',[$lab_test->id,$lab_test->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$lab_test->id}}" {{$lab_test->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                      
                                        <td>
                                            <a class="btn btn-sm btn-white"
                                                href="{{route('lab.lab-test.edit',[$lab_test->id])}}" title="{{__('messages.edit')}} "><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-white" href="javascript:"
                                            onclick="form_alert('category-{{$lab_test->id}}','Want to delete this lab test ?')" title="{{__('messages.delete')}} "><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('lab.lab-test.delete',[$lab_test->id])}}" method="post" id="category-{{$lab_test->id}}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <!-- Pagination -->
                        <div class="row justify-content-center justify-content-sm-between align-items-sm-center"> 
                            <div class="col-sm-auto">
                                <div class="d-flex justify-content-center justify-content-sm-end">
                                    <!-- Pagination -->
                                    {!! $lab_tests->links() !!}
                                </div>
                            </div>
                        </div>
                        <!-- End Pagination -->
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
        $(document).on('ready', function () {
            

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
                    url: '{{route('lab.lab-test.search')}}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
                        console.log(data);
                        $('#table-div').html(data.view);
                        $('#itemCount').html(data.count);
                        $('.page-area').hide();
                    },
                    complete: function () {
                        $('#loading').hide();
                    },
                });
            });
        
    </script>
@endpush
