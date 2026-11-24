@extends('layouts.admin.app')

@section('title','Add new domain')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} Domain
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{isset($domain)?__('messages.update'):__('messages.add').' '.__('messages.new')}} Domain</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{isset($domain)?route('admin.domain.update',[$domain['id']]):route('admin.domain.store')}}" method="post">
                        @csrf
                         
                         <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}}</label>
                                <input type="text" name="name" value="{{isset($domain)?$domain['name']:''}}"  class="form-control" placeholder="Domain Name" required>
                        </div>

                        <button type="submit" class="btn btn-primary">{{isset($domain)?__('messages.update'):__('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Domain List <span class="badge badge-soft-dark ml-2" id="itemCount">{{$domains->total()}}</span></h5>
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
                                            <button type="submit" class="btn btn-primary">{{__('messages.search')}}</button>
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
                                    <th>{{__('messages.#')}}</th>
                                    <th>{{__('messages.name')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                                </thead>

                                <tbody id="set-rows">
                                @include('admin-views.domain.partials._table', ['domains' => $domains])
                                </tbody>
                            </table>
                            <hr>
                            <div class="page-area">
                                <table>
                                    <tfoot>
                                    {!! $domains->links() !!}
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
            $.post('{{route('admin.domain.search')}}', formData, function (data) {
                $('#set-rows').html(data.view);
                $('#itemCount').html(data.count);
            });
        });
    </script>
@endpush
