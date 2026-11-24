@extends('layouts.admin.app')

@section('content')
    <div class="content container-fluid">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Customer Wallet</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Customer Wallet</h4>

                <form action="{{ route('admin.wallet.transactions') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Search customer..."
                        value="{{ request('search') }}">
                    <button class="btn btn-primary">Search</button>

                    @if (request()->search)
                        <a href="{{ route('admin.wallet.transactions') }}" class="btn btn-secondary ml-2">Reset</a>
                    @endif
                </form>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Wallet Amount</th>
                                <th>Last Recharge Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->wallet_amount }}</td>
                                    <td>{{ $customer->last_recharge_date ? date('d M Y', strtotime($customer->last_recharge_date)) : '-' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.wallet.view', $customer->id) }}"
                                            class="btn btn-sm btn-info">
                                            view<i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $customers->links() }}


                    <div class="d-flex justify-content-end">
                        {{ $customers->links() }}
                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection
