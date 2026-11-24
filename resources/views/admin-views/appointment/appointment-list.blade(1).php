@extends('layouts.admin.app')

@section('title','Consultants')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: transparent;
        }
        .select2-container--default .select2-selection--multiple {
            border-color: #e7eaf300;
            padding: 0 .875rem;
        }
        .calculation-detail {
            font-size: 11px;
            color: #6c757d;
            margin-top: 2px;
            line-height: 1.2;
        }
        .fee-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 3px;
            margin-bottom: 2px;
        }
        .active-fee {
            background-color: #28a745 !important;
            color: white !important;
            font-weight: bold;
            border: 2px solid #1e7e34;
        }
        .fee-column {
            min-width: 120px;
        }
        .appointment-type-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .chat-badge { background-color: #17a2b8; color: white; }
        .audio-badge { background-color: #ffc107; color: #212529; }
        .video-badge { background-color: #dc3545; color: white; }
        .offline-badge { background-color: #6c757d; color: white; }
    </style>
@endpush

@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
  
  function formatCurrency($amount) {
      return number_format($amount, 2);
  }
@endphp

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center mb-3">
                <div class="col-9">
                    <h1 class="page-header-title text-capitalize">
                        @if(!Request::is('admin/order/list/service_ongoing')) 
                            Consultants  
                        @endif
                        <span class="badge badge-soft-dark ml-2">{{$total}}</span>
                    </h1>
                </div>
                <div class="col-3">
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="row justify-content-between align-items-center flex-grow-1">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <form action="javascript:" id="search-form">
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                       placeholder="{{__('messages.search')}}" aria-label="{{__('messages.search')}}" required>
                                <input type="hidden" name="status" value="{{ request('status') }}" />
                                
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">{{__('messages.search')}}</button>
                                    <button type="button" id="clear-search" class="btn btn-secondary ml-2">Clear</button>
                                </div>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
                            <!-- Datatable Info -->
                            <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0" style="display: none;">
                                <div class="d-flex align-items-center">
                                    <span class="font-size-sm mr-3">
                                        <span id="datatableCounter">0</span>
                                        {{__('messages.selected')}}
                                    </span>
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
                            
                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white" href="javascript:;"
                                   onclick="$('#datatableFilterSidebar,.hs-unfold-overlay').show(500)">
                                    <i class="tio-filter-list mr-1"></i> Filters 
                                    <span class="badge badge-success badge-pill ml-1" id="filter_count"></span>
                                </a>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
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
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "isResponsive": false,
                     "isShowPaging": false,
                     "paging": false
                   }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="">{{__('messages.#')}}</th>
                        <th class="table-column-pl-0">Consultant ID</th>
                        <th>Consultant Date</th>
                        <th>Customer Name</th>
                        <th>Advisor Name</th>
                        <th>Duration (min)</th>
                        <th class="fee-column">Chat Fee</th>
                        <th class="fee-column">Audio Fee</th>
                        <th class="fee-column">Video Fee</th>
                        <th>Consultant Type</th>
                        <th>Calculated Total</th>
                        <th>{{__('messages.payment')}} {{__('messages.status')}}</th>                       
                        <th>Appointment {{__('messages.status')}}</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($orders as $key=>$order)
                        @php
                            $durationDisplay = $order->duration_display ?? '0 min';
                            $calculatedFee = isset($order->calculated_total) ? (float)$order->calculated_total : 0;
                            $calculationText = $order->calculation_formula ?? '';
                            $appointmentType = $order->appoinmtent_type;
                            $activeFeeType = $appointmentType;

                            // Extract fees from doctor (already loaded)
                            $chatFee = isset($order->doctor->chat_fee) ? (float)$order->doctor->chat_fee : 0;
                            $audioFee = isset($order->doctor->audio_fee) ? (float)$order->doctor->audio_fee : 0;
                            $videoFee = isset($order->doctor->video_fee) ? (float)$order->doctor->video_fee : 0;
                        @endphp
                        
                        <tr class="status-{{$order['order_status']}} class-all">
                            <td>{{$key+$orders->firstItem()}}</td>
                            <td class="table-column-pl-0">{{$order['id']}}</td>
                            <td>{{date('d M Y',strtotime($order['appoinmtent_date']))}}</td>
                            <td>
                                @if($order->customer)
                                    {{$order->customer['name']}}
                                @else
                                    <label class="badge badge-danger">{{__('messages.invalid')}} {{__('messages.customer')}} {{__('messages.data')}}</label>
                                @endif
                            </td>
                            <td>
                                @if($order->doctor)
                                    {{$order->doctor['name']}}
                                @else
                                    <label class="badge badge-danger">{{__('messages.invalid')}} Advisor {{__('messages.data')}}</label>
                                @endif
                            </td>
                            
                            <!-- Duration Column -->
                            <td>
                                @if($durationDisplay == 'N/A')
                                    <span class="badge badge-secondary">{{ $durationDisplay }}</span>
                                @elseif(strpos($durationDisplay, '0 min') !== false)
                                    <span class="badge badge-warning">{{ $durationDisplay }}</span>
                                @else
                                    <span class="badge badge-info">{{ $durationDisplay }}</span>
                                @endif
                            </td>
                            
                            <!-- Chat Fee Column -->
                            <td>
                                @if($activeFeeType == 'chat' && $chatFee > 0)
                                    <div class="font-weight-bold text-success">
                                        Rs. {{ formatCurrency($chatFee) }}/min
                                    </div>
                                    @if(!empty($calculationText) && strpos($calculationText, 'chat') !== false)
                                        <div class="calculation-detail">
                                            <small>Total: Rs. {{ formatCurrency($calculatedFee) }}</small>
                                            <br>
                                            <small>{{ $calculationText }}</small>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            <!-- Audio Fee Column -->
                            <td>
                                @if($activeFeeType == 'audio' && $audioFee > 0)
                                    <div class="font-weight-bold text-warning">
                                        Rs. {{ formatCurrency($audioFee) }}/min
                                    </div>
                                    @if(!empty($calculationText) && strpos($calculationText, 'audio') !== false)
                                        <div class="calculation-detail">
                                            <small>Total: Rs. {{ formatCurrency($calculatedFee) }}</small>
                                            <br>
                                            <small>{{ $calculationText }}</small>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            <!-- Video Fee Column -->
                            <td>
                                @if($activeFeeType == 'video' && $videoFee > 0)
                                    <div class="font-weight-bold text-danger">
                                        Rs. {{ formatCurrency($videoFee) }}/min
                                    </div>
                                    @if(!empty($calculationText) && strpos($calculationText, 'video') !== false)
                                        <div class="calculation-detail">
                                            <small>Total: Rs. {{ formatCurrency($calculatedFee) }}</small>
                                            <br>
                                            <small>{{ $calculationText }}</small>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            <!-- Consultant Type Column -->
                            <td>
                                @php
                                    $badgeClass = '';
                                    if($appointmentType == 'chat') $badgeClass = 'chat-badge';
                                    elseif($appointmentType == 'audio') $badgeClass = 'audio-badge';
                                    elseif($appointmentType == 'video') $badgeClass = 'video-badge';
                                    else $badgeClass = 'offline-badge';
                                @endphp
                                
                                <span class="appointment-type-badge {{ $badgeClass }}">
                                    {{ ucfirst($appointmentType) }}
                                </span>
                            </td>
                            
                            <!-- Calculated Total Fee Column -->
                            <td>
                                <div class="font-weight-bold text-primary">
                                    Rs. {{ formatCurrency($calculatedFee) }}
                                </div>
                                
                                @if($calculationText)
                                    <div class="calculation-detail">
                                        <small>{{ $calculationText }}</small>
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Payment Status Column -->
                            <td class="text-capitalize">
                                @php
                                    $paymentStatus = $order->payment_status ?? 'pending';
                                @endphp
                                
                                @if($paymentStatus == 'paid')
                                    <span class="badge badge-soft-success ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-success"></span>Paid
                                    </span>
                                @elseif($paymentStatus == 'pending')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-warning"></span>Pending
                                    </span>
                                @else
                                    <span class="badge badge-soft-secondary ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-secondary"></span>{{ ucfirst($paymentStatus) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Appointment Status Column -->
                            <td class="text-capitalize">
                                @php
                                    $appointmentStatus = $order['status'] ?? 'pending';
                                    $statusClass = 'info';
                                    if($appointmentStatus == 'cancelled') $statusClass = 'danger';
                                    elseif($appointmentStatus == 'completed') $statusClass = 'success';
                                    elseif($appointmentStatus == 'pending') $statusClass = 'warning';
                                    elseif($appointmentStatus == 'confirmed') $statusClass = 'primary';
                                @endphp
                                
                                <span class="badge badge-soft-{{ $statusClass }} ml-2 ml-sm-3">
                                    <span class="legend-indicator bg-{{ $statusClass }}"></span>
                                    {{ str_replace('_',' ', $appointmentStatus) }}
                                </span>
                            </td>
                            
                            <!-- Action Column -->
                            <td>
                                <!-- Delete Button -->
                                <a class="btn btn-sm btn-white" 
                                   href="javascript:void(0);" 
                                   onclick="deleteAppointment(event, {{ $order->id }})" 
                                   title="{{ __('messages.delete') }}">
                                    <i class="tio-delete-outlined"></i>
                                </a>

                                <!-- Hidden Delete Form -->
                                <form id="delete-form-{{ $order->id }}" 
                                      action="{{ route('doctor.appointment.destroy', $order->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            {!! $orders->appends($_GET)->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
        
        <!-- Order Filter Modal -->
        <div id="datatableFilterSidebar" class="hs-unfold-content_ sidebar sidebar-bordered sidebar-box-shadow" style="display: none">
            <div class="card card-lg sidebar-card sidebar-footer-fixed">
                <div class="card-header">
                    <h4 class="card-header-title">{{__('messages.order')}} {{__('messages.filter')}}</h4>
                    <!-- Toggle Button -->
                    <a class="js-hs-unfold-invoker_ btn btn-icon btn-xs btn-ghost-dark ml-2" href="javascript:;"
                    onclick="$('#datatableFilterSidebar,.hs-unfold-overlay').hide(500)">
                        <i class="tio-clear tio-lg"></i>
                    </a>
                    <!-- End Toggle Button -->
                </div>
                
                <?php 
                $filter_count=0;
                if(isset($zone_ids) && count($zone_ids) > 0) $filter_count += 1;
                if(isset($vendor_ids) && count($vendor_ids)>0) $filter_count += 1;
                if((isset($from_date) && isset($to_date)) || isset($date_type)) $filter_count += 1;
                if(isset($appointment_type) && !empty($appointment_type)) $filter_count += 1;
                if(isset($customer_id) && !empty($customer_id)) $filter_count += 1;
                if(isset($doctor_id) && !empty($doctor_id)) $filter_count += 1;
                ?>
                
                <!-- Body -->
                <form class="card-body sidebar-body sidebar-scrollbar" action="{{route('admin.appointment-filter')}}" method="POST" id="order_filter_form">
                    @csrf
                    
                    <div class="form-group">
                        <label class="input-label" for="date_type">Date Type</label>
                        <div class="btn-group btn-group-toggle bubble-group_ mb-3" data-toggle="buttons">
                            <label class="btn btn-outline-primary btn-sm {{isset($date_type) && $date_type == 'today' ? 'active' : ''}}">
                                <input type="radio" name="date_type" value="today" {{isset($date_type) && $date_type == 'today' ? 'checked' : ''}}> Today
                            </label>
                            <label class="btn btn-outline-primary btn-sm {{isset($date_type) && $date_type == 'this_month' ? 'active' : ''}}">
                                <input type="radio" name="date_type" value="this_month" {{isset($date_type) && $date_type == 'this_month' ? 'checked' : ''}}> This Month
                            </label>
                            <label class="btn btn-outline-primary btn-sm {{!isset($date_type) || empty($date_type) ? 'active' : ''}}">
                                <input type="radio" name="date_type" value="" {{!isset($date_type) || empty($date_type) ? 'checked' : ''}}> Custom
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="doctor_id">Advisor Name</label>
                        <select name="doctor_id" id="doctor_id" class="form-control js-select2-custom">
                            <option value="">All Advisors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{$doctor->id}}" {{(isset($doctor_id) && $doctor_id == $doctor->id) ? 'selected' : ''}}>{{$doctor->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="customer_id">Customer Name</label>
                        <select name="customer_id" id="customer_id" class="form-control js-select2-custom">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer->id}}" {{(isset($customer_id) && $customer_id == $customer->id) ? 'selected' : ''}}>{{$customer->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="appointment_type">Appointment Type</label>
                        <select name="appointment_type" id="appointment_type" class="form-control">
                            <option value="">All Types</option>
                            <option value="chat" {{ (isset($appointment_type) && $appointment_type == 'chat') ? 'selected' : '' }}>Chat</option>
                            <option value="audio" {{ (isset($appointment_type) && $appointment_type == 'audio') ? 'selected' : '' }}>Audio</option>
                            <option value="video" {{ (isset($appointment_type) && $appointment_type == 'video') ? 'selected' : '' }}>Video</option>
                            <option value="offline" {{ (isset($appointment_type) && $appointment_type == 'offline') ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                  
                    <hr class="my-4">
                    
                    <div id="custom_date_range" style="{{isset($date_type) && !empty($date_type) ? 'display:none' : ''}}">
                        <small class="text-cap mb-3">{{__('messages.date')}} {{__('messages.between')}}</small>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group" style="margin:0;">
                                    <input type="date" name="from_date" class="form-control" id="date_from" value="{{isset($from_date)?$from_date:''}}">
                                </div>
                            </div>
                            <div class="col-12 text-center">----TO----</div>
                            <div class="col-12">
                                <div class="form-group">
                                    <input type="date" name="to_date" class="form-control" id="date_to" value="{{isset($to_date)?$to_date:''}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer sidebar-footer">
                        <div class="row gx-2">
                            <div class="col">
                                <button type="reset" class="btn btn-block btn-white" id="reset">Clear all filters</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-block btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                    <!-- End Footer -->
                </form>
            </div>
        </div>
        <!-- End Order Filter Modal -->
    </div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            @if($filter_count>0)
            $('#filter_count').html({{$filter_count}});
            @endif
            
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', className: 'd-none' },
                    { extend: 'excel', className: 'd-none' },
                    { extend: 'csv', className: 'd-none' },
                    { extend: 'pdf', className: 'd-none' },
                    { extend: 'print', className: 'd-none' },
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

            $('#export-copy').click(function () { datatable.button('.buttons-copy').trigger() });
            $('#export-excel').click(function () { datatable.button('.buttons-excel').trigger() });
            $('#export-csv').click(function () { datatable.button('.buttons-csv').trigger() });
            $('#export-pdf').click(function () { datatable.button('.buttons-pdf').trigger() });
            $('#export-print').click(function () { datatable.button('.buttons-print').trigger() });

            $("#date_from").on("change", function () { $('#date_to').attr('min',$(this).val()); });
            $("#date_to").on("change", function () { $('#date_from').attr('max',$(this).val()); });

            $('input[name="date_type"]').on('change', function() {
                if($(this).val() == '') {
                    $('#custom_date_range').show();
                } else {
                    $('#custom_date_range').hide();
                }
            });
        });

        $('#reset').on('click', function(){
            location.href = '{{url('/')}}/admin/appointment/filter/reset';
        });

        function deleteAppointment(event, id) {
            event.preventDefault();
            let confirmation = confirm('Are you sure you want to delete this appointment?');
            if (confirmation) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        function fetchOrders(formData) {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $.get({
                url: '{{route('admin.search-appointment')}}',
                data: formData,
                beforeSend: function () { $('#loading').show(); },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    let searchValue = $('input[name="search"]').val().trim();
                    if (searchValue === '') {
                        $('.card-footer').show();
                    } else {
                        $('.card-footer').hide();
                    }
                },
                complete: function () { $('#loading').hide(); }
            });
        }

        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            let formData = $(this).serialize();
            fetchOrders(formData);
        });

        $('#clear-search').on('click', function () {
            $('input[name="search"]').val('');
            let formData = $('#search-form').serialize();
            fetchOrders(formData);
            window.location.reload();
        });
    </script>
@endpush