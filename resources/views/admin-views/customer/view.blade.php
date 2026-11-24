@extends('layouts.admin.app')
@section('title','Patient Details')

@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-print-none pb-2">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item">
                            <a class="breadcrumb-link" href="{{ route('admin.patient.list') }}">
                                {{__('messages.user')}}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{__('messages.details')}}</li>
                    </ol>
                </nav>

                <div class="d-sm-flex align-items-sm-center">
                    <h1 class="page-header-title">
                        Customer #{{ $customer->id }}
                    </h1>
                    <span class="ml-2 ml-sm-3">
                        <i class="tio-date-range"></i> 
                        Joined: {{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">Customer Information</h4>
                </div>
                <div class="card-body">
                    <!-- Profile Image Section -->
                    <div class="text-left mb-4">
                        @if($customer->image)
                            <img src="{{ asset('storage/app/public/users/' . $customer->image) }}" 
                                 alt="Profile Image" 
                                 class="img-thumbnail rounded-circle"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-light" 
                                 style="width: 120px; height: 120px;">
                                <i class="tio-user text-muted" style="font-size: 48px;"></i>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>{{__('messages.name')}}:</strong> 
                                    <span class="text-capitalize">{{ $customer->name ?? 'N/A' }}</span>
                                </li>
                                <li class="mb-2">
                                    <strong>{{__('messages.email')}}:</strong> 
                                    {{ $customer->email ?? 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>{{__('messages.phone')}}:</strong> 
                                    {{ $customer->mobile_number ?? 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>{{__('DOB')}}:</strong> 
                                    {{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>{{__('Gender')}}:</strong> 
                                    {{ $customer->gender ?? 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>{{__('Martial Status')}}:</strong> 
                                    {{ $customer->martial_status ?? 'N/A' }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>{{__('occupation')}}:</strong> 
                                    {{ $customer->occupation ?? 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>{{__('City')}}:</strong> 
                                    {{ $customer->city ?? 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>{{__('State')}}:</strong> 
                                    {{ $customer->state ?? 'N/A' }}
                                </li>
                                <li class="mb-2">
                                    <strong>Created At:</strong> 
                                    {{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y, h:i A') }}
                                </li>
                                @if($customer->updated_at && $customer->updated_at != $customer->created_at)
                                <li class="mb-2">
                                    <strong>Updated At:</strong> 
                                    {{ \Carbon\Carbon::parse($customer->updated_at)->format('d M Y, h:i A') }}
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.patient.list') }}" class="btn btn-secondary">
                            ← {{__('messages.back_to_list')}}
                        </a>
                        <a href="{{ route('admin.patient.edit', $customer->id) }}" class="btn btn-primary ml-2">
                            <i class="tio-edit"></i> {{__('messages.edit')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Proper closure of the first row -->

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">Wallet Transactions</h4>
                    <div class="ml-auto">
                        <strong>Current Balance: </strong>
                        <span class="badge badge-soft-info" style="font-size: 1.1em;">
                            {{ number_format($customer->wallet_amount, 2) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description / Advisor</th>
                                    <th>Running Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wallet_transactions as $key => $tx)
                                    <tr>
                                        <td>{{ $wallet_transactions_paginated->total() - (($wallet_transactions_paginated->currentPage() - 1) * $wallet_transactions_paginated->perPage() + $key) }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, h:i A') }}
                                        </td>
                                        <td>
                                            @if($tx->type == 'Credit')
                                                <span class="badge badge-soft-success">Credit</span>
                                            @else
                                                <span class="badge badge-soft-danger">Debit</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tx->type == 'Credit')
                                                <span class="text-success">+{{ number_format($tx->amount, 2) }}</span>
                                            @else
                                                <span class="text-danger">-{{ number_format($tx->amount, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $tx->doctor_name }}</strong><br>
                                            <small class="text-muted">{{ $tx->description }}</small>
                                        </td>
                                        <td>
                                            {{ number_format($tx->current_balance, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No transactions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {!! $wallet_transactions_paginated->appends(['wallet_page' => $wallet_transactions_paginated->currentPage()])->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection