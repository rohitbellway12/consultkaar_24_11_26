@extends('layouts.admin.app')
@section('title','Customers List')
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
                    <h1 class="page-header-title"> Customers {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="foodCount">{{$user_list->total()}}</span></h1>
                </div>            
            </div>            
        </div>
        <!-- End Page Header -->

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header">
         <form action="{{ route('admin.patient.list') }}" method="GET" id="search-form">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group input-group-merge input-group-flush" style="height: 36px;">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="padding: 4px 8px;">
                        
                    </div>
                </div>
                <input id="datatableSearch_" type="search" name="search" value="{{ request('search') }}"
                       class="form-control"
                       placeholder="{{ __('messages.search') }}" aria-label="Search"
                       style="height: 36px; font-size: 14px; padding: 4px 10px; min-width: 300px;">

                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary" title="Search"
                            style="height: 32px; width: 36px; padding: 4px;">
                        <i class="tio-search"></i>
                    </button>
                    <a href="{{ route('admin.patient.list') }}" class="btn btn-secondary ml-2" title="Clear"
                       style="height: 32px; width: 36px; padding: 4px;">
                        <i class="tio-clear"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <div class="mr-2">
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" style="height: 36px; font-size: 14px; padding: 4px 10px;" placeholder="From Date">
                </div>
                <div class="mr-2">
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" style="height: 36px; font-size: 14px; padding: 4px 10px;" placeholder="To Date">
                </div>
                <button type="submit" class="btn btn-primary" style="height: 36px;">
                    <i class="tio-search"></i> Filter
                </button>
            </div>
        </div>
    </div>
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
                                <th>{{trans('DOB')}}</th>
                                <th>{{trans('Gender')}}</th>
                                <th>{{trans('Martial Status')}}</th>
                                <th>{{trans('Proffession')}}</th>
                                <th>{{trans('City')}}</th>
                                <th>{{trans('State')}}</th>
                                <th style="width: 50px">{{trans('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($user_list as $k=>$e)
                                <tr>
                                    <th scope="row">{{ $loop->iteration}}</th>
                                    <td class="text-capitalize">{{$e->name ?? 'N/A'}}</td>
                                    <td >{{$e->email ?? 'N/A'}}</td>
                                    <td>{{$e->mobile_number ?? 'N/A'}}</td>
                                    <td>{{$e->date_of_birth ?? 'N/A'}}</td>
                                    <td>{{$e->gender ?? 'N/A'}}</td>
                                    <td>{{$e->martial_status ?? 'N/A'}}</td>
                                    <td>{{$e->occupation ?? 'N/A'}}</td>
                                    <td>{{$e->city ?? 'N/A'}}</td>
                                    <td>{{$e->state ?? 'N/A'}}</td>
                                   
                                    
                                    <td>
    {{-- View Button --}}
    <a class="btn btn-sm btn-info mr-1" href="{{ route('admin.patient.view', $e->id) }}" title="{{__('messages.view')}}">
     <i class="tio-visible"></i>
   </a>

    <!-- Edit Button -->
    <a class="btn btn-sm btn-primary mr-1" href="{{ route('admin.patient.edit', $e->id) }}" title="{{__('messages.edit')}}">
        <i class="tio-edit"></i>
    </a>

    <!-- Delete Button -->
    <a class="btn btn-sm btn-danger" href="javascript:"
        onclick="form_alert('customer-{{$e->id}}','Want to delete this customer ?')" 
        title="{{__('messages.delete')}}">
        <i class="tio-delete-outlined"></i>
    </a>

    <!-- Delete Form -->
    <form action="{{ route('admin.patient.delete', $e->id) }}" method="post" id="customer-{{$e->id}}">
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
                   <div class="d-flex justify-content-center mt-3">
        {!! $user_list->links() !!}
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
                           columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'd-none',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'd-none',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'd-none',
                        exportOptions: {
                           columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'd-none',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
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
            
        });
    </script>
@endpush