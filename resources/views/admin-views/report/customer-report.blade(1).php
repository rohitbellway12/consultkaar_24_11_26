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
        <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5">
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

        <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5">
            <div class="card card-hover-shadow h-100" style="background: #28C76F; color: white;">
                <div class="card-body">
                    <h6 class="card-subtitle text-white">Active Customers (Transacted)</h6>
                    <div class="row align-items-center gx-2">
                        <div class="col">
                            <span class="display-4 text-white">{{ number_format($summary['active_customers']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5">
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
                    <div class="col-md-4 mb-3">
                        <label class="input-label">From Date (Joined)</label>
                        <input type="date" name="from" value="{{$from}}" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="input-label">To Date (Joined)</label>
                        <input type="date" name="to" value="{{$to}}" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="input-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>
            <div class="row mt-2">
                <div class="col-md-12 text-right">
                    <a href="{{route('admin.report.customer-report-export', ['from'=>$from, 'to'=>$to])}}" class="btn btn-success">
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
                        <th>Joined At</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Wallet Balance</th>
                        <th>Total Appointments</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($customers as $key => $customer)
                    <tr>
                        <td>{{$customers->firstItem() + $key}}</td>
                        <td>{{date('d M Y', strtotime($customer->created_at))}}</td>
                        <td>{{$customer->name}}</td>
                        <td>{{$customer->mobile_number}}</td>
                        <td class="text-primary font-weight-bold">₹{{number_format($customer->wallet_amount, 2)}}</td>
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
    </div>
</div>
@endsection
