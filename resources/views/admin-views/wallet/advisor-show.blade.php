@extends('layouts.admin.app')

@section('title', 'Advisor Wallet Transactions - ' . ($doctor->name ?? 'N/A'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Transaction History - {{ $doctor->name ?? 'Unknown Advisor' }}</h4>
                    <a href="{{ route('admin.wallet.advisor') }}" class="btn btn-secondary btn-sm">
                        ← Back to Advisors
                    </a>
                </div>
                <div class="card-body">
                    <!-- Date Filter Form -->
                    <form method="GET" action="{{ route('admin.wallet.advisor.show', $doctor->id) }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="from_date">From Date</label>
                                    <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to_date">To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ request('customer_name') }}" placeholder="Search by customer name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        <a href="{{ route('admin.wallet.advisor.show', $doctor->id) }}" class="btn btn-outline-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- ====== NEW: SUMMARY SECTION ====== -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card text-center border-primary">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Total Earnings</h5>
                                    <h3>₹{{ number_format($summary->total_earnings, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center border-warning">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">Total Commission</h5>
                                    <h3>₹{{ number_format($summary->total_commission, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center border-info">
                                <div class="card-body">
                                    <h5 class="card-title text-info">Total Consultations</h5>
                                    <h3>{{ $summary->total_consultations }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-success">
                                <div class="card-body">
                                    <h5 class="card-title text-success">Current Balance</h5>
                                    <h3>₹{{ number_format($currentBalance, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-danger">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">Reel Earnings</h5>
                                    <h3>₹{{ number_format($summary->reel_earnings, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Credit / Debit Form -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-header-title">Manual Credit / Debit</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.wallet.advisor.credit') }}" method="POST" id="creditDebitForm" onsubmit="
                                var txType = document.getElementById('txType');
                                if (txType && txType.value === 'debit') {
                                    this.action = '{{ route('admin.wallet.advisor.debit') }}';
                                } else {
                                    this.action = '{{ route('admin.wallet.advisor.credit') }}';
                                }
                                return true;
                            ">
                                @csrf
                                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="txType">Transaction Type</label>
                                            <select name="tx_type" id="txType" class="form-control" required onchange="updateTransactionType()">
                                                <option value="credit">Credit</option>
                                                <option value="debit">Debit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="amount">Amount</label>
                                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="Amount" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reason">Reason</label>
                                            <input type="text" name="description" id="reason" class="form-control" placeholder="Reason (required)" required>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="reel_earning" id="reelEarning" value="1" class="form-check-input">
                                            <label class="form-check-label" for="reelEarning">Reel Earning</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success form-control" id="submitBtn">Credit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Current Amount</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Commission</th>
                                    <th>Source</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrichedTransactions as $tx)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('d-m-Y H:i') }}</td>
                                        <td>
                                            @php
                                                $sourceName = 'Patient';
                                                if (strpos($tx->transaction_for, 'reel') !== false) {
                                                    $sourceName = 'Reel (Admin)';
                                                } elseif (strpos($tx->transaction_for, 'manual') !== false) {
                                                    $sourceName = $tx->admin_name ?? 'Admin';
                                                } elseif (in_array($tx->transaction_for, ['call', 'chat'])) {
                                                    $sourceName = $tx->patient_name ?? 'Patient';
                                                }
                                            @endphp
                                            {{ $sourceName }}
                                        </td>
                                        <td>₹{{ number_format($tx->current_amount, 2) }}</td>
                                        <td>{{ $tx->description ?? 'N/A' }}</td>
                                        <td>
                                            @if($tx->is_credit == '1')
                                                <span class="text-success">+₹{{ number_format($tx->amount, 2) }}</span>
                                            @else
                                                <span class="text-danger">-₹{{ number_format($tx->amount, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>₹{{ number_format($tx->commission_amount, 2) }}</td>
                                        <td>
                                            @if(strpos($tx->transaction_for, 'reel') !== false)
                                                <span class="badge badge-info">Reel</span>
                                            @elseif(strpos($tx->transaction_for, 'manual') !== false)
                                                <span class="badge badge-secondary">Manual</span>
                                            @else
                                                <span class="badge badge-primary">{{ ucfirst($tx->transaction_for) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tx->is_credit == '1')
                                                <span class="badge badge-success">Credit</span>
                                            @else
                                                <span class="badge badge-danger">Debit</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No transactions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateTransactionType() {
        try {
            var txTypeSelect = document.getElementById('txType');
            var form = document.getElementById('creditDebitForm');
            var submitBtn = document.getElementById('submitBtn');
            
            if (!txTypeSelect || !form || !submitBtn) {
                return true;
            }
            
            var txType = txTypeSelect.value;
            
            if (txType === 'credit') {
                form.action = '{{ route("admin.wallet.advisor.credit") }}';
                submitBtn.textContent = 'Credit';
                submitBtn.classList.remove('btn-danger');
                submitBtn.classList.add('btn-success');
            } else {
                form.action = '{{ route("admin.wallet.advisor.debit") }}';
                submitBtn.textContent = 'Debit';
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-danger');
            }
        } catch (e) {
            console.error('Error updating transaction type:', e);
        }
        return true;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateTransactionType();
    });
</script>

@endsection