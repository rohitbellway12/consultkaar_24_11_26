@extends('layouts.admin.app')

@section('title', __('Advisor Report'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-filter-list"></i> {{__('messages.advisor')}} {{__('messages.report')}}</h1>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-5">
        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #10163A; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Total Advisors</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">{{ number_format($summary['total_advisors']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #28C76F; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Total Appointments</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">{{ number_format($summary['total_appointments']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #7367F0; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Total Gross Business</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">₹{{ number_format($summary['total_gross'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #FF9F43; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Total Wallet Balance</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">₹{{ number_format($summary['total_wallet_balance'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3 mb-lg-5">
        <div class="card-body">
            <form action="{{route('admin.report.advisor-report')}}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="input-label">Advisor</label>
                        <select name="doctor_id" class="form-control js-select2-custom">
                            <option value="">All Advisors</option>
                            @foreach($all_advisors as $adv)
                                <option value="{{$adv->id}}" {{isset($doctor_id) && $doctor_id == $adv->id ? 'selected' : ''}}>{{$adv->name}} ({{$adv->mobile_number}})</option>
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
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <a href="{{route('admin.report.advisor-report-export', ['from'=>$from, 'to'=>$to, 'doctor_id'=>$doctor_id ?? ''])}}" class="btn btn-success">
                        <i class="tio-download-to"></i> Download Report (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">Advisor Performance</h5>
        </div>
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Advisor Name</th>
                        <th>Mobile</th>
                        <th>Comm. %</th>
                        <th>Total Appointments</th>
                        <th>Gross Earnings</th>
                        <th>Admin Commission</th>
                        <th>Net Earnings</th>
                        <th>Wallet Balance</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($advisors as $key => $advisor)
                    @php
                        $comm = ($advisor->total_gross * ($advisor->commission_percentage ?? 0)) / 100;
                    @endphp
                    <tr>
                        <td>{{$advisors->firstItem() + $key}}</td>
                        <td>
                            <a href="{{ url('admin/wallet/advisors/'.$advisor->id) }}{{ ($from && $to) ? '?from='.$from.'&to='.$to : '' }}"
                               style="color: #7367f0; font-weight: 600; text-decoration: none;"
                               title="View Advisor Wallet Transactions">
                                <i class="tio-user" style="font-size:13px;"></i> {{$advisor->name}}
                            </a>
                        </td>
                        <td>{{$advisor->mobile_number}}</td>
                        <td class="text-center">{{$advisor->commission_percentage ?? 0}}%</td>
                        <td class="text-center">{{$advisor->total_appointments}}</td>
                        <td class="font-weight-bold font-primary">₹{{number_format($advisor->total_gross ?? 0, 2)}}</td>
                        <td class="text-danger">₹{{number_format($comm, 2)}}</td>
                        <td class="text-success font-weight-bold">₹{{number_format(($advisor->total_gross ?? 0) - $comm, 2)}}</td>
                        <td class="text-primary">₹{{number_format($advisor->wallet_amount, 2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{$advisors->links()}}
        </div>
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
