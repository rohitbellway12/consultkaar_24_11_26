@extends('layouts.admin.app')
@section('title', 'Advisor Request List')
@push('css_or_js')
    <style>
        #datatable_filter,
        #datatable_info {
            display: none
        }
    </style>
@endpush
@php
    $appEnv = env('APP_ENV');
    $assetPrefixPath = $appEnv == 'local' ? '' : 'public';
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"> Advisor Request {{ __('messages.list') }}<span
                            class="badge badge-soft-dark ml-2" id="foodCount">{{ $doctors->total() }}</span></h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <form action="{{ route('admin.doctor.request-list') }}" method="GET" id="search-form">
                            @csrf
                            <div class="input-group input-group-merge input-group-flush" style="height: 36px;">
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="padding: 4px 8px;"></div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" value="{{ request('search') }}"
                                    class="form-control" placeholder="{{ __('messages.search') }}" aria-label="Search"
                                    style="height: 36px; font-size: 14px; padding: 4px 10px; min-width: 300px;
                                          border: 1px solid #ced4da; border-radius: 4px;">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" title="Search"
                                        style="height: 35px; width: 36px; padding: 4px;">
                                        <i class="tio-search"></i>
                                    </button>
                                    <a href="{{ route('admin.doctor.request-list') }}" class="btn btn-secondary ml-2"
                                        title="Clear" style="height: 35px; width: 36px; padding: 4px;">
                                        <i class="tio-clear"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                style="width: 100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ trans('messages.#') }}</th>
                                        <th>{{ trans('messages.name') }}</th>
                                        <th>{{ trans('messages.email') }}</th>
                                        <th>{{ trans('messages.phone') }}</th>
                                        <th>Status</th>
                                        <th style="width: 120px">{{ trans('messages.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="set-rows">
                                    @foreach ($doctors as $k => $e)
                                        <tr>
                                            <th scope="row">{{ $k + $doctors->firstItem() }}</th>
                                            <td class="text-capitalize">{{ $e->name ?? 'N/A' }}</td>
                                            <td>{{ $e->email ?? 'N/A' }}</td>
                                            <td>{{ $e->mobile_number ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-warning">Pending Approval</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <!-- View Details/Documents -->
                                                    <a class="btn btn-sm btn-white"
                                                        href="{{ route('admin.doctor.view', [$e->id]) }}"
                                                        title="View Documents">
                                                        <i class="tio-visible"></i> Review
                                                    </a>

                                                    <!-- Approve -->
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('admin.doctor.approve', [$e->id]) }}"
                                                        title="Approve">
                                                        <i class="tio-done"></i> Approve
                                                    </a>

                                                    <!-- Reject -->
                                                    <button class="btn btn-sm btn-danger" type="button"
                                                        onclick="openRejectModal({{ $e->id }}, '{{ $e->name }}')"
                                                        title="Reject">
                                                        <i class="tio-clear"></i> Reject
                                                    </button>
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
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="rejectModalLabel">Reject Advisor: <span
                            id="doctorNameSpan"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason" class="input-label">Reason for Rejection</label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4"
                                placeholder="Enter reason for rejection..." required></textarea>
                            <small class="text-muted">This reason will be shown to the advisor.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(id, name) {
            $('#doctorNameSpan').text(name);
            $('#rejectForm').attr('action', '{{ url('admin/doctor/reject') }}/' + id);
            $('#rejectModal').modal('show');
        }
    </script>
@endsection
