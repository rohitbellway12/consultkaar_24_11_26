@extends('layouts.admin.app')

@section('title', 'Wallet Withdrawal Request Details')

@section('content')
<div class="content container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.wallet-withdrawal-requests') }}">Wallet Withdrawal Requests</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Request Details</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Wallet Withdrawal Request Details</h4>
            <a href="{{ route('admin.wallet-withdrawal-requests') }}" class="btn btn-secondary btn-sm">
                ← Back to All Requests
            </a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Request ID:</label>
                        <p>{{ $request->id }}</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Advisor Name:</label>
                        <p>
                            @if($request->advisor)
                                {{ $request->advisor->name }}
                            @else
                                <span class="text-danger">Advisor not found</span>
                            @endif
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Requested Amount:</label>
                        <p class="text-success font-weight-bold">₹{{ number_format($request->amount, 2) }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Request Date:</label>
                        <p>{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') }}</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Current Wallet Balance:</label>
                        <p>
                            @if($request->advisor)
                                ₹{{ number_format($request->advisor->wallet_amount ?? 0, 2) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Status:</label>
                        <p>
                            <span class="badge badge-soft-info ml-2 ml-sm-3">
                                <span class="legend-indicator bg-info"></span>
                                {{ str_replace('_',' ', $request->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Advisor Wallet Link -->
            @if($request->advisor)
            <div class="mt-4">
                <a href="{{ route('admin.wallet.advisor.show', $request->advisor->id) }}"
                   class="btn btn-primary">
                    <i class="tio-wallet"></i> View {{ $request->advisor->name }}'s Wallet Transactions
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
