@extends('layouts.admin.app')
@section('title','Employee Role')
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid"> 
   

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">Employee Role</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{__('messages.role_form')}}
                </div>
                <div class="card-body">
                    <form action="{{route('admin.custom-role.create')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="input-label qcont" for="name">{{__('messages.role_name')}}</label>
                            <input type="text" name="name" class="form-control" id="name" aria-describedby="emailHelp"
                                   placeholder="Ex : Sub Admin" required>
                        </div>

                        <label class="input-label qcont" for="name">{{__('messages.module_permission')}} : </label>
                        <hr>
                        <div class="row">
                            
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="patients" class="form-check-input"
                                           id="patients">
                                    <label class="form-check-label qcont text-dark" for="patient">Customer</label>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="doctors" class="form-check-input"
                                           id="doctors">
                                    <label class="form-check-label qcont text-dark" for="doctors">Advisor</label>
                                </div>
                            </div>
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="medicine_bookings" class="form-check-input"-->
                            <!--               id="medicine_bookings">-->
                            <!--        <label class="form-check-label qcont text-dark" for="medicine_bookings">Medicine Bookings</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="lab_test_bookings" class="form-check-input"-->
                            <!--               id="lab_test_bookings">-->
                            <!--        <label class="form-check-label qcont text-dark" for="lab_test_bookings">Lab Test Bookings</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="appointments" class="form-check-input"
                                           id="appointments">
                                    <label class="form-check-label qcont text-dark" for="appointments">Consultation</label>
                                </div>
                            </div>
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="appointments" class="form-check-input"-->
                            <!--               id="appointments">-->
                            <!--        <label class="form-check-label qcont text-dark" for="appointments">Appointments</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="wallet_withdrawal_requests" class="form-check-input"
                                           id="wallet_withdrawal_requests">
                                    <label class="form-check-label qcont text-dark" for="wallet_withdrawal_requests">Wallet Withdrawal Requests</label>
                                </div>
                            </div>
                           
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="product_categories" class="form-check-input"-->
                            <!--               id="product_categories">-->
                            <!--        <label class="form-check-label qcont text-dark" for="product_categories">Product Categories</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="product_types" class="form-check-input"-->
                            <!--               id="product_types">-->
                            <!--        <label class="form-check-label qcont text-dark" for="product_types">Product Types</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="products" class="form-check-input"-->
                            <!--               id="products">-->
                            <!--        <label class="form-check-label qcont text-dark" for="products">Products</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="specialities" class="form-check-input"-->
                            <!--               id="specialities">-->
                            <!--        <label class="form-check-label qcont text-dark" for="specialities">Specialities</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="disease" class="form-check-input"-->
                            <!--               id="disease">-->
                            <!--        <label class="form-check-label qcont text-dark" for="disease">Diseases</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="lab_categories" class="form-check-input"-->
                            <!--               id="lab_categories">-->
                            <!--        <label class="form-check-label qcont text-dark" for="lab_categories">Lab Categories</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="lab_tests" class="form-check-input"-->
                            <!--               id="lab_tests">-->
                            <!--        <label class="form-check-label qcont text-dark" for="lab_tests">Lab Tests</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="labs" class="form-check-input"-->
                            <!--               id="labs">-->
                            <!--        <label class="form-check-label qcont text-dark" for="labs">Labs</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="custom_role" class="form-check-input"
                                           id="custom_role">
                                    <label class="form-check-label qcont text-dark" for="custom_role">{{__('messages.custom_role')}}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Banners Checkbox -->
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="banners" class="form-check-input" id="banners">
                                    <label class="form-check-label qcont text-dark" for="banners">Banners</label>
                                </div>
                            </div>
                            
                            <!--<div class="col-md-3">-->
                                <!-- Allergies Checkbox -->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="allergies" class="form-check-input" id="allergies">-->
                            <!--        <label class="form-check-label qcont text-dark" for="allergies">Allergies</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <!--<div class="col-md-3">-->
                                <!-- Medications Checkbox -->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="medications" class="form-check-input" id="medications">-->
                            <!--        <label class="form-check-label qcont text-dark" for="medications">Medications</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <!--<div class="col-md-3">-->
                                <!-- Injuries Checkbox -->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="injuries" class="form-check-input" id="injuries">-->
                            <!--        <label class="form-check-label qcont text-dark" for="injuries">Injuries</label>-->
                            <!--    </div>-->
                            <!--</div>-->

                            
                           

                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group form-check">-->
                            <!--        <input type="checkbox" name="modules[]" value="faqs" class="form-check-input"-->
                            <!--               id="faqs">-->
                            <!--        <label class="form-check-label qcont text-dark" for="faqs">FAQS</label>-->
                            <!--    </div>-->
                            <!--</div>-->
                          
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="employee" class="form-check-input"
                                           id="employee">
                                    <label class="form-check-label qcont text-dark" for="employee">{{__('messages.Employee')}}</label>
                                </div>
                            </div>

                            
                            
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="settings" class="form-check-input"
                                           id="settings">
                                    <label class="form-check-label qcont text-dark" for="settings">{{__('messages.business')}} {{__('messages.settings')}}</label>
                                </div>
                            </div>
                            
                        </div>

                        <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-0">
                    <h5>{{__('messages.roles_table')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$rl->total()}}</span></h5>
                    <form action="javascript:" id="search-form">
                        @csrf
                        <!-- Search -->
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="Search" aria-label="Search">
                            <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th scope="col" style="width: 50px">SL#</th>
                                <th scope="col" style="width: 50px">{{__('messages.role_name')}}</th>
                                <th scope="col" style="width: 200px">{{__('messages.modules')}}</th>
                                <th scope="col" style="width: 50px">{{__('messages.created_at')}}</th>
                                <th scope="col" style="width: 50px">{{__('messages.status')}}</th>
                                <th scope="col" style="width: 50px">{{__('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody  id="set-rows">
                            @foreach($rl as $k=>$r)
                                <tr>
                                    <td scope="row">{{$k+$rl->firstItem()}}</td>
                                    <td>{{$r['name']}}</td>
                                    <td class="text-capitalize">
                                    @if($r['modules'] != null)
                                                @php
                                                    $modules = json_decode($r['modules']);
                                                    $lastModule = end($modules); // Get the last module
                                                @endphp

                                                @foreach($modules as $key => $m)
                                                    {{ str_replace('_', ' ', $m) }}
                                                    @if(!$loop->last) {{-- Check if it's not the last iteration --}}
                                                        , {{-- Add comma if it's not the last item --}}
                                                    @endif
                                                @endforeach
                                            @endif

                                    </td>
                                    <td>{{date('d-M-y',strtotime($r['created_at']))}}</td>
                                    <td>
                                        {{$r->status?'Active':'Inactive'}}
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-white"
                                            href="{{route('admin.custom-role.edit',[$r['id']])}}" title="{{__('messages.edit')}} {{__('messages.role')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn-danger" href="javascript:"
                                            onclick="form_alert('role-{{$r['id']}}','{{__('messages.Want_to_delete_this_role')}}')" title="{{__('messages.delete')}} {{__('messages.role')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.custom-role.delete',[$r['id']])}}"
                                                method="post" id="role-{{$r['id']}}">
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
                                {!! $rl->links() !!}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
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
                url: '{{route('admin.custom-role.search')}}',
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
        $(document).ready(function() {
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));
        });
    </script>
@endpush
