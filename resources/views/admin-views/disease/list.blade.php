@extends('layouts.admin.app')

@section('title','Counselor List')

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
                        <i class="tio-add-circle-outlined"></i>  Counselor {{__('messages.list')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
     
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Counselor {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$diseases->total()}}</span></h5>
                        <!--<form id="search-form" method="post">-->
                            <!-- Search -->
                        <!--    <div class="input-group input-group-merge input-group-flush">-->
                        <!--        <div class="input-group-prepend">-->
                        <!--            <div class="input-group-text">-->
                        <!--                <i class="tio-search"></i>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--        <input id="datatableSearch"  name="search"  type="search" class="form-control" placeholder="Search" aria-label="{{__('messages.search')}}">-->
                        <!--        <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>-->
                        <!--    </div>-->
                            <!-- End Search -->
                        <!--</form>-->
                                <form action="{{ route('admin.disease.list') }}" method="GET" id="search-form">
    @csrf
    <div class="input-group input-group-merge input-group-flush" style="height: 36px;">
        <div class="input-group-prepend">
            <div class="input-group-text" style="padding: 4px 8px;">
            </div>
        </div>
        <input id="datatableSearch_" type="search" name="search" value="{{ request('search') }}"
               class="form-control"
               placeholder="{{ __('messages.search') }}" aria-label="Search"
               style="height: 36px; font-size: 14px; padding: 4px 10px; min-width: 300px;
                      border: 1px solid #ced4da; border-radius: 4px;">

        <div class="input-group-append">
            <button type="submit" class="btn btn-primary" title="Search"
                    style="height: 32px; width: 36px; padding: 4px;">
                <i class="tio-search"></i>
            </button>
            <a href="{{ route('admin.disease.list') }}" class="btn btn-secondary ml-2" title="Clear"
               style="height: 32px; width: 36px; padding: 4px;">
                <i class="tio-clear"></i>
            </a>
        </div>
    </div>
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
                                        <th>Counselor Name</th>
                                        <th>Experts</th>
                                        <th>{{__('messages.image')}}</th>
                                        <th >{{__('messages.status')}}</th>
                                        <th >{{__('messages.action')}}</th>
                                    </tr>
                                </thead>

                                <tbody id="table-div">
                                @foreach($diseases as $key=>$disease)
                                    <tr>
                                        <td>{{$key+$diseases->firstItem()}}</td>
                                       
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{$disease->name}}
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                              $speciality_data = App\Models\Speciality::find($disease->speciality_id);
                                              $speciality_name = ($speciality_data) ? $speciality_data->name : 'N/A';
                                            ?>
                                            <span class="d-block font-size-sm text-body">
                                                {{$speciality_name}}
                                            </span>
                                        </td>
                                        <?php
                
                                        $diseaseImagePath = (env('APP_ENV') == 'local') ? asset('storage/disease/' . $disease->image) : asset('storage/app/public/disease/' . $disease->image);        
                                       ?>
                                       <td>
                                        <img class="avatar avatar-lg mr-3" src="{{$diseaseImagePath}}" 
                                                     alt="{{$disease->name}} image">
                                             
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$disease->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.disease.status',[$disease->id,$disease->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$disease->id}}" {{$disease->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>

                                        
                                      
                                        <td>
                                            <a class="btn btn-sm btn-white"
                                                href="{{route('admin.disease.edit',[$disease->id])}}" title="{{__('messages.edit')}} Disease"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-white" href="javascript:"
                                            onclick="form_alert('disease-{{$disease->id}}','Want to delete this disease ?')" title="{{__('messages.delete')}} Disease"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.disease.delete',[$disease->id])}}" method="post" id="disease-{{$disease->id}}">
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
                                    {!! $diseases->links() !!}
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
            
            // INITIALIZATION OF DATATABLES
            // =======================================================
           /* var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
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

    <!--<script>-->
    <!--     $('#search-form').on('submit', function (e) {-->
    <!--            e.preventDefault();-->
    <!--            var formData = new FormData(this);-->
    <!--            $.ajaxSetup({-->
    <!--                headers: {-->
    <!--                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')-->
    <!--                }-->
    <!--            });-->
    <!--            $.post({-->
    <!--                url: '{{route('admin.disease.search')}}',-->
    <!--                data: formData,-->
    <!--                cache: false,-->
    <!--                contentType: false,-->
    <!--                processData: false,-->
    <!--                beforeSend: function () {-->
    <!--                    $('#loading').show();-->
    <!--                },-->
    <!--                success: function (data) {-->
    <!--                    $('#table-div').html(data.view);-->
    <!--                    $('#itemCount').html(data.count);-->
    <!--                    $('.page-area').hide();-->
    <!--                },-->
    <!--                complete: function () {-->
    <!--                    $('#loading').hide();-->
    <!--                },-->
    <!--            });-->
    <!--        });-->
        
    <!--</script>-->
@endpush
