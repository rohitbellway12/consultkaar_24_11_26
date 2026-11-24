@extends('layouts.admin.app')

@section('title','Lab List')

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
                        <i class="tio-add-circle-outlined"></i> Lab  {{__('messages.list')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
     
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Lab   {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$labs->total()}}</span></h5>
                        <form id="search-form" method="post">
                            <input type="hidden" name="search_by_subcategory" value ="yes">
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch"  name="search"  type="search" class="form-control" placeholder="Search lab" aria-label="Search labs">
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
                                        <th>Name</th>
                                        <th >{{__('messages.status')}}</th>
                                        <th >{{__('messages.action')}}</th>
                                    </tr>
                                </thead>

                                <tbody id="table-div">
                                @foreach($labs as $key=>$lab)
                                <?php
                                $test_en =  \App\Models\Lab::where(['lid' => "l".$lab->id,'lang_code' => 'en'])->first();
                                 $test_fr =  \App\Models\Lab::where(['lid' => "l".$lab->id,'lang_code' => 'fr'])->first();
                                ?>
                                    <tr>
                                        <td>{{$key+$labs->firstItem()}}</td>
                                        <td>{{$test_en->name ?? 'N/A'}}</td>    
                                      
                                        
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$lab->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.lab.status',[$lab->id,$lab->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$lab->id}}" {{$lab->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                      
                                        <td>
                                            <a class="btn btn-sm btn-white"
                                                href="{{route('admin.lab.edit',[$lab->id])}}" title="{{__('messages.edit')}} "><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-white" href="javascript:"
                                            onclick="form_alert('category-{{$lab->id}}','Want to delete this category ?')" title="{{__('messages.delete')}} "><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.lab.delete',[$lab->id])}}" method="post" id="category-{{$lab->id}}">
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
                                    {!! $labs->links() !!}
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
                    $('#viewer').attr('src', e.target.resul);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
        $(document).on('ready', function () {
            
            // INITIALIZATION OF DATATABLES
            // =======================================================
           /* var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
                select: {
                    style: 'muli',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                    '<img class="mb-3" src="{{asset($assetPrefixPath . '/admin/svg/illustrations/sorry.svg')}}" al="Image Description" style="width: 7rem;">' +
                    '<p class="mb-0">No data to show</p>' +
                    '</div>'
                }
            });*/



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
                    url: '{{route('admin.lab.search')}}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
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
