@extends('layouts.admin.app')
@section('title','Advisor List')
@push('css_or_js')
<style>
    #datatable_filter,#datatable_info{
        display:none
    }
    .date-filter-btn {
        height: 35px;
        padding: 4px 10px;
        font-size: 14px;
    }
    .date-filter-btn.active {
        background-color: #007bff;
        color: white;
    }
    
    .filter-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }
    
    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .filter-label {
        font-weight: 500;
        margin-right: 5px;
        white-space: nowrap;
    }
    
    .search-input-group {
        position: relative;
        flex: 1;
        min-width: 250px;
        max-width: 400px;
    }
    
    .search-input {
        width: 100%;
        padding: 8px 40px 8px 15px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        height: 38px;
    }
    
    .search-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        padding: 5px;
    }
    
    .date-filter-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .date-input-group {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .date-input {
        height: 38px;
        padding: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        width: 130px;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }
    
    .reset-btn {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        height: 38px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .reset-btn:hover {
        background-color: #5a6268;
    }
    
    .apply-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        height: 38px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .apply-btn:hover {
        background-color: #0069d9;
    }
    
    @media (max-width: 768px) {
        .filter-row {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-group {
            flex-direction: column;
            align-items: stretch;
            gap: 5px;
        }
        
        .search-input-group {
            max-width: 100%;
        }
        
        .date-filter-group {
            flex-direction: column;
            align-items: stretch;
        }
        
        .date-input-group {
            justify-content: space-between;
        }
        
        .action-buttons {
            margin-left: 0;
            justify-content: center;
        }
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
                <h1 class="page-header-title"> Advisor {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="foodCount">{{$doctors->total()}}</span></h1>
            </div>            
        </div>            
    </div>
    <!-- End Page Header -->

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <!-- Filter Form Section -->
                    <form action="{{ route('admin.doctor.list') }}" method="GET" id="search-form">
                        @csrf
                        <div class="filter-section">
                            <div class="filter-row">
                                <!-- Search Input -->
                                <div class="filter-group" style="flex: 1;">
                                    <div class="search-input-group">
                                        <input id="datatableSearch_" type="search" name="search" value="{{ request('search') }}"
                                               class="search-input"
                                               placeholder="{{ __('messages.search') }}" aria-label="Search">
                                        <button type="submit" class="search-btn" title="Search">
                                            <i class="tio-search"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Date Filters -->
                                <div class="filter-group">
                                    <span class="filter-label">Date:</span>
                                    <div class="date-filter-group">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary date-filter-btn {{ request('date_filter') == 'today' ? 'active' : '' }}" 
                                                    onclick="setDateFilter('today')">Today</button>
                                            <button type="button" class="btn btn-outline-secondary date-filter-btn {{ request('date_filter') == 'this_week' ? 'active' : '' }}" 
                                                    onclick="setDateFilter('this_week')">This Week</button>
                                            <button type="button" class="btn btn-outline-secondary date-filter-btn {{ request('date_filter') == 'this_month' ? 'active' : '' }}" 
                                                    onclick="setDateFilter('this_month')">This Month</button>
                                        </div>
                                        
                                        <div class="date-input-group">
                                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="date-input">
                                            <span>to</span>
                                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="date-input">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <button type="button" class="reset-btn" id="reset-all-btn">
                                        <i class="tio-clear"></i> Reset
                                    </button>
                                    <button type="submit" class="apply-btn">
                                        <i class="tio-search"></i> Apply
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Hidden input for date filter -->
                            <input type="hidden" name="date_filter" id="date_filter" value="{{ request('date_filter') }}">
                        </div>
                    </form>

                    <!-- Export Section -->
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Datatable Info -->
                        <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0" style="display: none;">
                            <div class="d-flex align-items-center">
                                  <span class="font-size-sm mr-3">
                                    <span id="datatableCounter">0</span>
                                    {{__('messages.selected')}}
                                  </span>
                            </div>
                        </div>

                        <!-- Export Dropdown -->
                        <div class="hs-unfold">
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
                                    <th>Bank Details</th>
                                    <th>Registration Date</th>
                                    <th>Commission (%)</th>
                                    <th>Status</th>
                                    <th style="width: 120px">{{trans('messages.action')}}</th>
                                </tr>
                            </thead>
                            <tbody id="set-rows">
                                @foreach($doctors as $k => $e)
                                    <tr>
                                        <th scope="row">{{ $k + $doctors->firstItem() }}</th>
                                        <td class="text-capitalize">{{ $e->name ?? 'N/A' }}</td>
                                        <td>{{ $e->email ?? 'N/A' }}</td>
                                        <td>{{ $e->mobile_number ?? 'N/A' }}</td>
                                        <td>
                                            @if($e->bank_name)
                                                <small>
                                                    {{ $e->bank_name ?? 'N/A' }}<br>
                                                    {{ $e->account_holder_name ?? 'N/A' }}<br>
                                                    {{ $e->account_number ?? 'N/A' }}<br>
                                                    {{ $e->ifsc_code ?? 'N/A' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not added</span>
                                            @endif
                                        </td>
                                        <td>{{ date('d M Y', strtotime($e->created_at)) ?? 'N/A' }}</td>
                                        <td>{{ number_format($e->commission_percentage ?? 0, 2) }}%</td>
                                        <td>
                                            @if($e->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <!-- Edit -->
                                                <a class="btn btn-sm btn-white" href="{{ route('admin.doctor.edit', [$e->id]) }}" title="{{ __('messages.edit') }}">
                                                    <i class="tio-edit"></i>
                                                </a>

                                                <!-- Delete -->
                                                <a class="btn btn-sm btn-danger" href="javascript:;"
                                                   onclick="form_alert('doctor-{{ $e->id }}', '{{ __('Want to delete this Advisor') }}')" 
                                                   title="{{ __('messages.delete') }}">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>

                                                <!-- Delete Form -->
                                                <form action="{{ route('admin.doctor.delete', [$e->id]) }}" method="post" id="doctor-{{ $e->id }}" style="display: none;">
                                                    @csrf
                                                    @method('delete')
                                                </form>

                                                <!-- View Bank Details -->
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
        var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', className: 'd-none', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
                { extend: 'excel', className: 'd-none', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
                { extend: 'csv', className: 'd-none', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
                { extend: 'pdf', className: 'd-none', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
                { extend: 'print', className: 'd-none', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] } },
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

        $('#export-copy').click(function () { datatable.button('.buttons-copy').trigger(); });
        $('#export-excel').click(function () { datatable.button('.buttons-excel').trigger(); });
        $('#export-csv').click(function () { datatable.button('.buttons-csv').trigger(); });
        $('#export-pdf').click(function () { datatable.button('.buttons-pdf').trigger(); });
        $('#export-print').click(function () { datatable.button('.buttons-print').trigger(); });
        
        // Reset all filters button functionality
        $('#reset-all-btn').click(function() {
            // Clear search input
            $('#datatableSearch_').val('');
            
            // Clear date inputs
            $('input[name="from_date"]').val('');
            $('input[name="to_date"]').val('');
            
            // Clear date filter hidden input
            $('#date_filter').val('');
            
            // Remove active class from date filter buttons
            $('.date-filter-btn').removeClass('active');
            
            // Submit the form to reset everything
            $('#search-form').submit();
        });
    });
    
    // Function to set date filter
    function setDateFilter(filter) {
        // Clear custom date fields when using quick filters
        $('input[name="from_date"]').val('');
        $('input[name="to_date"]').val('');
        
        // Set the date filter value
        $('#date_filter').val(filter);
        
        // Update button styles
        $('.date-filter-btn').removeClass('active');
        $(event.target).addClass('active');
        
        // Submit the form
        $('#search-form').submit();
    }
</script>
@endpush