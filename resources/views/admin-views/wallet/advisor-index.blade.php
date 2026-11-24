@extends('layouts.admin.app')

@section('title', 'Advisor Wallet Summary')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-shopping-cart"></i> Advisor Wallet Overview
                </h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">All Advisors</h5>
                </div>
                <div class="card-body">
                    <!-- SEARCH FORM START -->
                    <form method="GET" action="{{ route('admin.wallet.advisor') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by advisor name..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="{{ route('admin.wallet.advisor') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                    <!-- SEARCH FORM END -->

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Wallet Amount</th>
                                    <th>Total Consultations</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advisors as $advisor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $advisor->doctor_name ?? 'N/A' }}</td>
                                        <td>₹{{ number_format($advisor->wallet_amount ?? 0, 2) }}</td>
                                        <td>{{ $advisor->total_consultations ?? 0 }}</td>
                                        <td>
                                            <a href="{{ route('admin.wallet.advisor.show', $advisor->doctor_id) }}"
                                                class="btn btn-info btn-sm">
                                                View Transactions
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No advisors found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $advisors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
