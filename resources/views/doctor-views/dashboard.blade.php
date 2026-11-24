@extends('layouts.doctor.app')


@section('title',\App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value??'Dashboard')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .grid-card {
            border: 2px solid #00000012;
            border-radius: 10px;
            padding: 10px;
        }

        .label_1 {
            position: absolute;
            font-size: 10px;
            background: #865439;
            color: #ffffff;
            width: 60px;
            padding: 2px;
            font-weight: bold;
            border-radius: 6px;
        }

         /* Hide default month and year in Chart.js tooltips */
         .chartjs-tooltip-label {
            display: none !important;
        }

        .chartjs-tooltip {
            display: none !important;
        }

        .card-hover-effect {
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out, background-color 0.3s ease-in-out;
}

.card-hover-effect:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9; /* Adjust color as needed */
}

  
    </style>
@endpush

@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp

@section('content')
    <div class="content container-fluid">
        @if(auth()->user()->role_id == 3)
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"> <marquee> {{__('messages.welcome')}}, {{auth()->user()->name}}.  </marquee></h1>
                </div>
             
            </div>
        </div>
        <!-- End Page Header -->


        <!-- Stats -->
     <div class="card mb-3">
            <div class="card-body">
                <div class="row gx-2 gx-lg-3 mb-2">
                   
                  
                </div>
               
                
                <div class="row gx-2 gx-lg-3" id="order_stats">
                    @include('doctor-views.partials._dashboard-order-stats',['data'=>$data])
                </div>
            </div>
        </div> 

        <!-- End Stats -->

        <div class="row gx-2 gx-lg-3">
            <div class="col-lg-12 mb-3 mb-lg-12">
                <!-- Card -->
              
                <div class="card h-100" id="monthly-earning-graph3">
                    <!-- Body -->
                    <?php
                    
                            

                    ?>
                    @include('doctor-views.partials._monthly-earning-graph', [
                        'total_sell' => $total_sell,
                    ])
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>

        @else
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{__('messages.welcome')}}, {{auth()->user()->name}}.</h1>
                    <p class="page-header-text">{{__('messages.employee_welcome_message')}}</p> 
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        @endif
    </div>
@endsection

@push('script')
    <script src="{{asset($assetPrefixPath . '/assets/doctor')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset($assetPrefixPath . '/assets/doctor')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script
        src="{{asset($assetPrefixPath . '/assets/doctor')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
@endpush



@push('script_2')
    


    <script>
        $("#year").on("change",function(){
            var year  = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('doctor.dashboard-stats.year-wise-data') }}',
                data: {
                    year: year
                },
                beforeSend: function() {
                    $('#loading').show()
                },
                success: function(data) {
                    insert_param('year', year);
                    console.log("data",data)
                    location.reload()
                },
                complete: function() {
                    $('#loading').hide()
                }
            });
        });
        function insert_param(key, value) {
            key = encodeURIComponent(key);
            value = encodeURIComponent(value);
            // kvp looks like ['key1=value1', 'key2=value2', ...]
            var kvp = document.location.search.substr(1).split('&');
            let i = 0;

            for (; i < kvp.length; i++) {
                if (kvp[i].startsWith(key + '=')) {
                    let pair = kvp[i].split('=');
                    pair[1] = value;
                    kvp[i] = pair.join('=');
                    break;
                }
            }
            if (i >= kvp.length) {
                kvp[kvp.length] = [key, value].join('=');
            }
            // can return this or...
            let params = kvp.join('&');
            // change url page with new params
            window.history.pushState('page2', 'Title', '{{url()->current()}}?' + params);
        }

         // INITIALIZATION OF CHARTJS
    // =======================================================

    
    Chart.plugins.unregister(ChartDataLabels);

    $('.js-chart').each(function () {
        $.HSCore.components.HSChartJS.init($(this));
    });

    var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

</script>
@endpush

