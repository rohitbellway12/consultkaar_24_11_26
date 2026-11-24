@extends('layouts.admin.app')

@section('title', __('Customer Report'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-filter-list"></i> {{__('messages.customer')}} {{__('messages.report')}}</h1>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-5">
        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #10163A; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Total Customers</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">{{ number_format($summary['total_customers']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #28C76F; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Active Customers</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">{{ number_format($summary['active_customers']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #00CFE8; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Total Recharged (Period)</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">₹{{ number_format($summary['total_recharged'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #7367F0; color: white;">
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
            <form action="{{route('admin.report.customer-report')}}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="input-label">Customer</label>
                        <select name="customer_id" class="form-control js-select2-custom">
                            <option value="">All Customers</option>
                            @foreach($all_customers as $cust)
                                <option value="{{$cust->id}}" {{isset($customer_id) && $customer_id == $cust->id ? 'selected' : ''}}>{{$cust->name}} ({{$cust->mobile_number}})</option>
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
                            <a href="{{route('admin.report.customer-report')}}" class="btn btn-outline-secondary">
                                <i class="tio-clear"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <a href="{{route('admin.report.customer-report-export', ['from'=>$from, 'to'=>$to, 'customer_id'=>$customer_id ?? ''])}}" class="btn btn-success">
                        <i class="tio-download-to"></i> Download Report (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">Customer Activities</h5>
        </div>
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Activity Date</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Current Balance</th>
                        <th>Added in Period</th>
                        <th>Total Appointments</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($customers as $key => $customer)
                    <tr>
                        <td>{{$customers->firstItem() + $key}}</td>
                        <td>{{date('d M Y', strtotime($customer->latest_activity_at))}}</td>
                        <td>
                            <a href="{{ url('admin/wallet/view/'.$customer->id) }}{{ ($from && $to) ? '?from='.$from.'&to='.$to : '' }}" 
                               style="color: #1e88e5; font-weight: 600; text-decoration: none;"
                               title="View Wallet Transactions">
                                <i class="tio-user" style="font-size:13px;"></i> {{$customer->name}}
                            </a>
                        </td>
                        <td>{{$customer->mobile_number}}</td>
                        <td class="font-weight-bold">₹{{number_format($customer->wallet_amount, 2)}}</td>
                        <td class="text-success font-weight-bold">
                            @if($customer->total_recharged > 0)
                                ₹{{number_format($customer->total_recharged, 2)}}
                            @else
                                ₹0.00
                            @endif
                        </td>
                        <td class="text-center">{{$customer->total_appointments}}</td>
                        <td class="text-danger font-weight-bold">₹{{number_format($customer->total_spent ?? 0, 2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{$customers->links()}}
        </div>
    @if($from && $to)
    <!-- Detailed Recharges Section -->
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-header-title">Detailed Recharge History ({{date('d M Y', strtotime($from))}} - {{date('d M Y', strtotime($to))}})</h5>
        </div>
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Opening Balance</th>
                        <th>Amount Added</th>
                        <th>Closing Balance</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($detailed_recharges as $dr)
                    <tr>
                        <td>{{date('d M Y H:i A', strtotime($dr->created_at))}}</td>
                        <td>
                            {{$dr->customer_name}}<br>
                            <small class="text-muted">{{$dr->mobile_number}}</small>
                        </td>
                        <td>₹{{number_format($dr->balance_before, 2)}}</td>
                        <td class="text-success font-weight-bold">+₹{{number_format($dr->amount, 2)}}</td>
                        <td class="text-primary font-weight-bold">₹{{number_format($dr->balance_after, 2)}}</td>
                        <td><span class="badge badge-soft-info">{{ucfirst($dr->transaction_for)}}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No recharge transactions found for this period.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
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
