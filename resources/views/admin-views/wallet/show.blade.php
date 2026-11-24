@extends('layouts.app.admin')

@section('content')
<div class="container">
    <h2>Transaction Details</h2>

    <table class="table table-striped">
        <tr><th>ID</th><td>{{ $transaction->id }}</td></tr>
        <tr><th>Customer</th><td>{{ $transaction->customer_name }}</td></tr>
        <tr><th>Role</th><td>{{ $transaction->role_id == 2 ? 'Customer' : 'Doctor' }}</td></tr>
        <tr><th>Amount</th><td>{{ $transaction->amount }}</td></tr>
        <tr><th>Type</th><td>{{ $transaction->is_credit == 1 ? 'Credit' : 'Debit' }}</td></tr>
       
        <tr><th>Order ID</th><td>{{ $transaction->order_id }}</td></tr>
        <tr><th>Created At</th><td>{{ $transaction->created_at }}</td></tr>
        <tr><th>Updated At</th><td>{{ $transaction->updated_at }}</td></tr>
    </table>

    <a href="{{ route('wallet.transactions') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
