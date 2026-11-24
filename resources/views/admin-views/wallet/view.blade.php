@extends('layouts.admin.app')

@section('content')
<div class="content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a>
            </li>
            <li class="breadcrumb-item active">Wallet: {{ $customer->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>{{ $customer->name }}'s Wallet Transactions</h4>
        <a href="{{ route('admin.wallet.transactions') }}" class="btn btn-secondary btn-sm">← Back</a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Advisor Name</label>
                        <input type="text" name="advisor_name" class="form-control" placeholder="Search advisor..." value="{{ request('advisor_name') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th>S.No</th>
                    <th>Date</th>
                    <th>Current Balance</th>
                    <th>Advisor</th>
                    <!--<th>Description</th>-->
                    <th>Amount</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactionsWithBalance as $tx)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('d-m-Y H:i') }}</td>
                        <td>₹{{ number_format($tx->current_balance, 0) }}</td>
                        <td>{{ $tx->doctor_name }}</td>
                        <!--<td>{{ $tx->description ?? '—' }}</td>-->
                        <td>
                            @if($tx->type === 'Credit')
                                <span class="text-success">+₹{{ number_format($tx->amount, 0) }}</span>
                            @else
                                <span class="text-danger">-₹{{ number_format($tx->amount, 0) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $tx->type === 'Credit' ? 'badge-success' : 'badge-danger' }}">
                                {{ $tx->type }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No transactions found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection