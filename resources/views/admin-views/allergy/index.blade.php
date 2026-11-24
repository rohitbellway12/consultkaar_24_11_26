@extends('layouts.admin.app')

@section('title','Allergy')

@push('css_or_js')

@endpush
@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
  $storagePath = ($appEnv == 'local') ? asset('/storage/allergy') : asset('/storage/app/public/allergy');
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} Allergy</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.allergy.store')}}" method="post" id="allergy_form">
                            @csrf
                            <input type="hidden" name="lang_code" id="lang_code" value="en">    
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                   <a class="nav-link lang_link active" href="#" id="en-link">English<span class="text-uppercase">(EN)</span></a>
                                </li>
                              {{--  <li class="nav-item">
                                   <a class="nav-link lang_link" href="#" id="fr-link">French<span class="text-uppercase">(FR)</span></a>
                                </li> --}}
                             </ul>
                             <div class="card mb-1 p-4 lang_form" id="en-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{__('messages.name')}}</label>
                                        <small style="color: red">* </small>
                                        <input type="text" name="name_en" class="form-control" required> 
                                        <br>                                    
                                    </div>                             
                                </div>
                             </div>
                             <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{__('messages.name')}}</label>
                                        <small style="color: red">* </small>
                                        <input type="text" name="name_fr"  class="form-control" required> 
                                        <br>                                    
                                    </div>                             
                                </div>
                             </div>
                            
                            <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                        </form>
                    </div>
                </div>                
            </div>
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5>Allergy {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$allergies->count()}}</span></h5>
                      <form action="{{ route('admin.allergy.add-new') }}" method="GET" id="search-form">
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
            <a href="{{ route('admin.allergy.add-new') }}" class="btn btn-secondary ml-2" title="Clear"
               style="height: 32px; width: 36px; padding: 4px;">
                <i class="tio-clear"></i>
            </a>
        </div>
    </div>
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
                                    <th>Name</th>
                                    <th>{{__('messages.status')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($allergies as $key=>$allergy)
                               <?php
                                $allergy_fr =  \App\Models\Allergy::where(['aid' => "a".$allergy->id,'lang_code' => 'fr'])->first();
                               ?>
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$allergy->name}}</td> 
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$allergy->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.allergy.status',[$allergy->id,$allergy->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$allergy->id}}" {{$allergy->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-white" href="{{route('admin.allergy.edit',[$allergy->id])}}" title="{{__('messages.edit')}} allergy"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('{{$allergy->id}}','Want to delete this allergy ?')" title="{{__('messages.delete')}} allergy"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.allergy.delete',[$allergy->id])}}"
                                                    method="post" id="{{$allergy->id}}">
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
                                    {!! $allergies->links() !!}
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
    $(document).on('ready', function () {
        

        
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
        function allergy_type_change(order_type) {
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

        $('#allergy_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.allergy.store')}}',
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
                        toastr.success('Allergy added successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.allergy.add-new')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>

<script>
    // $('#search-form').on('submit', function (e) {
    //     e.preventDefault();
    //     var formData = new FormData(this);
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     $.post({
    //         url: '{{route('admin.allergy.search')}}',
    //         data: formData,
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         beforeSend: function () {
    //             $('#loading').show();
    //         },
    //         success: function (data) {
    //             $('#set-rows').html(data.view);
    //             $('#itemCount').html(data.count);
    //             $('.page-area').hide();
    //         },
    //         complete: function () {
    //             $('#loading').hide();
    //         },
    //     });
    // });

    
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
</script>

@endpush
