@extends('layouts.admin.app')

@section('title', __('Earning Report'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-chart-bar-4"></i> {{__('messages.earning')}} {{__('messages.report')}}</h1>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-5">
        <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #10163A; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Total Gross Earnings</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">₹{{ number_format($summary['total_gross'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #28C76F; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Admin Profit (Commission)</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">₹{{ number_format($summary['total_commission'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #7367F0; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Net Advisor Earnings</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">₹{{ number_format($summary['total_net'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3 mb-lg-5">
        <div class="card-body">
            <form action="{{route('admin.report.earning')}}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="input-label">Advisor</label>
                        <select name="doctor_id" class="form-control js-select2-custom">
                            <option value="">All Advisors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{$doctor->id}}" {{$doctor_id == $doctor->id ? 'selected' : ''}}>{{$doctor->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="input-label">From Date</label>
                        <input type="date" name="from" value="{{$from}}" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="input-label">To Date</label>
                        <input type="date" name="to" value="{{$to}}" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="input-label">&nbsp;</label>
                        <div class="d-flex justify-content-end" style="gap: 10px;">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="tio-filter-list"></i> Filter
                            </button>
                            <a href="{{route('admin.report.earning')}}" class="btn btn-outline-secondary">
                                <i class="tio-clear"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <a href="{{route('admin.report.earning-export', ['from'=>$from, 'to'=>$to, 'doctor_id'=>$doctor_id])}}" class="btn btn-success">
                        <i class="tio-download-to"></i> Download Report (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">Earning Transactions</h5>
        </div>
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Advisor</th>
                        <th>Gross Amount</th>
                        <th>Admin Commission</th>
                        <th>Net Earning</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($transactions as $key => $tx)
                    @php
                        $comm = ($tx->gross_amount * ($tx->commission_percentage ?? 0)) / 100;
                    @endphp
                    <tr>
                        <td>{{$transactions->firstItem() + $key}}</td>
                        <td>{{date('d M Y H:i', strtotime($tx->created_at))}}</td>
                        <td>{{$tx->order_id}}</td>
                        <td>{{$tx->doctor_name}} ({{$tx->commission_percentage ?? 0}}%)</td>
                        <td>₹{{number_format($tx->gross_amount, 2)}}</td>
                        <td class="text-danger">₹{{number_format($comm, 2)}}</td>
                        <td class="text-success">₹{{number_format($tx->gross_amount - $comm, 2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{$transactions->links()}}
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    $(document).on('ready', function () {
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });
</script>
@endpush
