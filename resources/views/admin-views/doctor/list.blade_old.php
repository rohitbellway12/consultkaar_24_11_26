    @extends('layouts.admin.app')
    @section('title','Doctor List')
    @push('css_or_js')
    <style>
        #datatable_filter,#datatable_info{
            display:none
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
                    <h1 class="page-header-title"> Doctor {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="foodCount">{{$doctors->total()}}</span></h1>
                </div>            
            </div>            
        </div>
        <!-- End Page Header -->
    
        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                <div class="card-header">
                      
                        <form action="javascript:" id="search-form">
                            @csrf
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{__('messages.search')}}" aria-label="Search">
                                <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>
                            </div>
                            <!-- End Search -->
                        </form>
                        <div class="col-lg-6">
                            <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
                                <!-- Datatable Info -->
                                <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0" style="display: none;">
                                    <div class="d-flex align-items-center">
                                          <span class="font-size-sm mr-3">
                                            <span id="datatableCounter">0</span>
                                            {{__('messages.selected')}}
                                          </span>
                                        {{--<a class="btn btn-sm btn-outline-danger" href="javascript:;">
                                            <i class="tio-delete-outlined"></i> Delete
                                        </a>--}}
                                    </div>
                                </div>
                                <!-- End Datatable Info -->
    
                                <!-- Unfold -->
                                <div class="hs-unfold mr-2">
                                    <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle" href="javascript:;"
                                       data-hs-unfold-options='{
                                         "target": "#usersExportDropdown",
                                         "type": "css-animation"
                                       }'>
                                        <i class="tio-download-to mr-1"></i> {{__('messages.export')}}
                                    </a>
    
                                    <div id="usersExportDropdown"
                                         class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                        <span class="dropdown-header">{{__('messages.options')}}</span>
                                        <a id="export-copy" class="dropdown-item" href="javascript:;">
                                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                 src="{{asset($assetPrefixPath . '/assets/admin')}}/svg/illustrations/copy.svg"
                                                 alt="Image Description">
                                            {{__('messages.copy')}}
                                        </a>
                                        <a id="export-print" class="dropdown-item" href="javascript:;">
                                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                 src="{{asset($assetPrefixPath . '/assets/admin')}}/svg/illustrations/print.svg"
                                                 alt="Image Description">
                                            {{__('messages.print')}}
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <span class="dropdown-header">{{__('messages.download')}} {{__('messages.options')}}</span>
                                        <a id="export-excel" class="dropdown-item" href="javascript:;">
                                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                 src="{{asset($assetPrefixPath . '/assets/admin')}}/svg/components/excel.svg"
                                                 alt="Image Description">
                                            {{__('messages.excel')}}
                                        </a>
                                        <a id="export-csv" class="dropdown-item" href="javascript:;">
                                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                 src="{{asset($assetPrefixPath . '/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                                 alt="Image Description">
                                            .{{__('messages.csv')}}
                                        </a>
                                        <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                 src="{{asset($assetPrefixPath . '/assets/admin')}}/svg/components/pdf.svg"
                                                 alt="Image Description">
                                            {{__('messages.pdf')}}
                                        </a>
                                    </div>
                                </div>
                                <!-- End Unfold -->
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%"
                                   data-hs-datatables-options='{
                                    "columnDefs": [{
                                       "targets": [0],
                                       "orderable": false
                                     }],
                                    "order": [],
                                    "info": {
                                      "totalQty": "#datatableWithPaginationInfoTotalQty"
                                    },
                                    "entries": "#datatableEntries",
                                    "isResponsive": false,
                                    "isShowPaging": false,
                                    "paging": false
                                  }'>
                                <thead class="thead-light">
                                <tr>
                                    <th>{{trans('messages.#')}}</th>
                                    <th>{{trans('messages.name')}}</th>
                                    <th>{{trans('messages.email')}}</th>
                                    <th>{{trans('messages.phone')}}</th>
                                    <!--<th>{{trans('Account Number')}}</th>-->
                                    <!--<th>{{trans('Bank Name')}}</th>-->
                                    <!--<th>{{trans('IFSC Code')}}</th>-->
                                    <!--<th>{{trans('Account Holder Name')}}</th>-->
                                    <th style="width: 50px">{{trans('messages.action')}}</th>
                                </tr>
                                </thead>
                                                 
<tbody id="set-rows">
    @foreach($doctors as $k => $e)
        <tr>
            <th scope="row">{{ $k + $doctors->firstItem() }}</th>
            <td class="text-capitalize">{{ $e->name ?? 'N/A' }}</td>
            <td>{{ $e->email ?? 'N/A' }}</td>
            <td>{{ $e->mobile_number ?? 'N/A' }}</td>
            <!--<td>{{ $e->account_number ?? 'N/A' }}</td>-->
            <!--<td>{{ $e->bank_name ?? 'N/A' }}</td>-->
            <!--<td>{{ $e->ifsc_code ?? 'N/A' }}</td>-->
            <!--<td>{{ $e->account_holder_name ?? 'N/A' }}</td>-->
           <td>
    <div class="d-flex align-items-center gap-2">
        <!-- Edit Button -->
        <a class="btn btn-sm btn-white" href="{{ route('admin.doctor.edit', [$e->id]) }}" title="{{ __('messages.edit') }}">
            <i class="tio-edit"></i>
        </a>

        <!-- Delete Button -->
        <a class="btn btn-sm btn-danger" href="javascript:;"
           onclick="form_alert('doctor-{{ $e->id }}', '{{ __('Want to delete this doctor') }}')" 
           title="{{ __('messages.delete') }}">
            <i class="tio-delete-outlined"></i>
        </a>

        <!-- Delete Form -->
        <form action="{{ route('admin.doctor.delete', [$e->id]) }}" method="post" id="doctor-{{ $e->id }}" style="display: none;">
            @csrf
            @method('delete')
        </form>

        <!-- Bank Details View Button -->
        <a class="btn btn-sm btn-white" href="{{ route('admin.doctor.view', [$e->id]) }}" 
           title="{{ __('messages.bankDetailsview') }}">
            <i class="tio-visible"></i>
        </a>
    </div>
</td>

        </tr>
    @endforeach
</tbody>

                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="page-area">
                            <table>
                                <tfoot>
                                {!! $doctors->links() !!}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    
    @push('script_2')
        <script>
            $(document).ready(function () {
                // INITIALIZATION OF DATATABLES
                // =======================================================
                var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            className: 'd-none',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'd-none',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'd-none',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'd-none',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'print',
                            className: 'd-none',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                    ],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child input[type="checkbox"]',
                        classMap: {
                            checkAll: '#datatableCheckAll',
                            counter: '#datatableCounter',
                            counterInfo: '#datatableCounterInfo'
                        }
                    },
                    language: {
                        zeroRecords: '<div class="text-center p-4">' +
                            '<img class="mb-3" src="{{asset($assetPrefixPath . '/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">' +
                            '<p class="mb-0">No data to show</p>' +
                            '</div>'
                    }
                });
    
                $('#export-copy').click(function () {
                    datatable.button('.buttons-copy').trigger()
                });
    
                $('#export-excel').click(function () {
                    datatable.button('.buttons-excel').trigger()
                });
    
                $('#export-csv').click(function () {
                    datatable.button('.buttons-csv').trigger()
                });
    
                $('#export-pdf').click(function () {
                    datatable.button('.buttons-pdf').trigger()
                });
    
                $('#export-print').click(function () {
                    datatable.button('.buttons-print').trigger()
                });
                
                $('#search-form').on('submit', function (e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.post({
                        url: '{{route('admin.doctor.search')}}',
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
                            $('.page-area').html(data.pagination);
                        },
                        complete: function () {
                            $('#loading').hide();
                        },
                    });
                });
            });
        </script>
    @endpush
