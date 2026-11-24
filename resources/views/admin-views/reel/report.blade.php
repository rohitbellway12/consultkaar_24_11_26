@extends('layouts.admin.app')

@section('title', 'Reel Report')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-movie"></i> Reel Report
                </h1>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-sm-6 col-lg" style="flex: 0 0 20%; max-width: 20%;">
            <div class="card card-hover-shadow h-100 border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">Total Reels</h5>
                    <h3 class="text-primary">{{ number_format($summary['total_reels']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg" style="flex: 0 0 20%; max-width: 20%;">
            <div class="card card-hover-shadow h-100 border-success">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">Total Views</h5>
                    <h3 class="text-success">{{ number_format($summary['total_views']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg" style="flex: 0 0 20%; max-width: 20%;">
            <div class="card card-hover-shadow h-100 border-info">
                <div class="card-body text-center">
                    <h5 class="card-title text-info">Total Likes</h5>
                    <h3 class="text-info">{{ number_format($summary['total_likes']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg" style="flex: 0 0 20%; max-width: 20%;">
            <div class="card card-hover-shadow h-100 border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">Total Reel Credits</h5>
                    <h3 class="text-warning">₹{{ number_format($summary['total_reel_credits'], 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg" style="flex: 0 0 20%; max-width: 20%;">
            <div class="card card-hover-shadow h-100 border-danger">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">Total Reel Debits</h5>
                    <h3 class="text-danger">₹{{ number_format($summary['total_reel_debits'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 mb-lg-5">
        <div class="card-body">
            <form action="{{ route('admin.reel.report') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="input-label">Advisor</label>
                        <select name="doctor_id" class="form-control js-select2-custom">
                            <option value="">All Advisors</option>
                            @foreach($all_advisors as $adv)
                                <option value="{{ $adv->id }}" {{ isset($doctor_id) && $doctor_id == $adv->id ? 'selected' : '' }}>{{ $adv->name }} ({{ $adv->mobile_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="input-label">From Date</label>
                        <input type="date" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="input-label">To Date</label>
                        <input type="date" name="to" value="{{ $to }}" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="input-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-header-title">Advisor-wise Reel Performance</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Advisor Name</th>
                        <th>Mobile</th>
                        <th>Total Reels</th>
                        <th>Views</th>
                        <th>Likes</th>
                        <th>Comments</th>
                        <th>Shares</th>
                        <th>Reel Credits</th>
                        <th>Reel Debits</th>
                        <th>Wallet Balance</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($advisors as $key => $advisor)
                    <tr>
                        <td>{{ $advisors->firstItem() + $key }}</td>
                        <td>
                            <a href="{{ url('admin/wallet/advisors/'.$advisor->id) }}{{ ($from && $to) ? '?from='.$from.'&to='.$to : '' }}"
                               style="color: #7367f0; font-weight: 600; text-decoration: none;"
                               title="View Advisor Wallet Transactions">
                                <i class="tio-user" style="font-size:13px;"></i> {{ $advisor->name }}
                            </a>
                        </td>
                        <td>{{ $advisor->mobile_number }}</td>
                        <td class="text-center">{{ $advisor->total_reels }}</td>
                        <td class="text-center">{{ number_format($advisor->total_views ?? 0) }}</td>
                        <td class="text-center">{{ number_format($advisor->total_likes ?? 0) }}</td>
                        <td class="text-center">{{ number_format($advisor->total_comments ?? 0) }}</td>
                        <td class="text-center">{{ number_format($advisor->total_shares ?? 0) }}</td>
                        <td class="font-weight-bold text-success">₹{{ number_format($advisor->total_reel_credits ?? 0, 2) }}</td>
                        <td class="font-weight-bold text-danger">₹{{ number_format($advisor->total_reel_debits ?? 0, 2) }}</td>
                        <td class="text-primary">₹{{ number_format($advisor->wallet_amount, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $advisors->links() }}
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