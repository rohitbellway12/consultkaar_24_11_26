@extends('layouts.admin.app')

@section('title', 'Wallet Withdrawal Requests')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .wallet-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .wallet-info p {
            margin: 5px 0;
        }

        .receipt-upload {
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center mb-3">
                <div class="col-12 col-md-9">
                    <h1 class="page-header-title">
                        <i class="tio-wallet mr-2"></i>
                        Wallet Withdrawal Requests
                        <span class="badge badge-secondary ml-2">
                            {{ $total }} Total
                        </span>
                    </h1>
                    <p class="page-header-text text-muted">
                        Manage and process advisor wallet withdrawal requests
                    </p>
                </div>
                <div class="col-12 col-md-3 text-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                            <i class="tio-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-body text-center">
                    <h4 class="mb-0">{{ $withdrawal_requests->where('status', 'pending')->count() }}</h4>
                    <small>Pending</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body text-center">
                    <h4 class="mb-0">{{ $withdrawal_requests->where('status', 'approve')->count() }}</h4>
                    <small>Approved</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body text-center">
                    <h4 class="mb-0">{{ $withdrawal_requests->where('status', 'reject')->count() }}</h4>
                    <small>Rejected</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body text-center">
                    <h4 class="mb-0">₹{{ number_format($withdrawal_requests->where('status', 'approve')->sum('amount')) }}
                    </h4>
                    <small>Total Paid</small>
                </div>
            </div>
        </div>

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="row justify-content-between align-items-center">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <form action="javascript:" id="search-form">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="tio-search"></i></div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    placeholder="Search by advisor name, amount, or ID..."
                                    aria-label="Search withdrawal requests">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
                            <div class="d-flex align-items-center">
                                <span class="mr-2">Filter:</span>
                                <select class="form-control form-control-sm" style="width: 150px;"
                                    onchange="filterByStatus(this.value)">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Approved
                                    </option>
                                    <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Rejected
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle"
                    style="width: 100%">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 10%;">Payment ID</th>
                            <th style="width: 12%;">Request Date</th>
                            <th style="width: 18%;">Request By</th>
                            <th style="width: 12%;">Wallet Balance</th>
                            <th style="width: 12%;">Requested Amount</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 21%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawal_requests as $key => $withdrawal_request)
                            <tr>
                                <td>{{ $withdrawal_requests->firstItem() + $key }}</td>
                                <td>
                                    <span
                                        class="font-weight-bold">#{{ str_pad($withdrawal_request['id'], 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div>
                                        <div>
                                            {{ \Carbon\Carbon::parse($withdrawal_request['created_at'])->format('d M Y') }}
                                        </div>
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($withdrawal_request['created_at'])->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if ($withdrawal_request->advisor)
                                        <div>
                                            <div class="font-weight-bold">{{ $withdrawal_request->advisor['name'] }}</div>
                                            <small
                                                class="text-muted">{{ $withdrawal_request->advisor->email ?? 'N/A' }}</small>
                                        </div>
                                    @else
                                        <span class="text-danger">Invalid Advisor Data</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($withdrawal_request->advisor)
                                        <span class="font-weight-bold text-primary">₹{{ number_format($withdrawal_request->advisor->wallet_amount ?? 0, 2) }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="font-weight-bold">₹{{ number_format($withdrawal_request->amount, 2) }}</span>
                                </td>
                                <td>
                                    @if ($withdrawal_request['status'] == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($withdrawal_request['status'] == 'approve')
                                        <span class="badge badge-success">Settled</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap">


                                        @if ($withdrawal_request['status'] == 'pending')
                                            <button type="button" class="btn btn-sm btn-success manage-request"
                                                data-request-id="{{ $withdrawal_request['id'] }}"
                                                data-request-amount="{{ $withdrawal_request['amount'] }}"
                                                data-wallet-balance="{{ $withdrawal_request->advisor->wallet_amount ?? 0 }}"
                                                data-request-status="approve" title="Approve Request">
                                                <i class="tio-checkmark-circle"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger manage-request"
                                                data-request-id="{{ $withdrawal_request['id'] }}"
                                                data-request-status="reject" title="Reject Request">
                                                <i class="tio-clear-circle"></i> Reject
                                            </button>
                                        @else
                                            <span class="text-muted small">
                                                @if ($withdrawal_request['status'] == 'approve')
                                                    <span class="text-success font-weight-bold">Settled</span>
                                                @else
                                                    <span class="text-danger font-weight-bold">Rejected</span>
                                                @endif
                                            </span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div>
                                        <i class="tio-wallet display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">No withdrawal requests found</h5>
                                        <p class="text-muted">There are currently no wallet withdrawal requests to display.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            {!! $withdrawal_requests->appends(request()->query())->links() !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Footer -->
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeApproveModal()">&times;</span>
            <h3>Approve Withdrawal Request</h3>

            <div class="wallet-info">
                <p><strong>Advisor:</strong> <span id="advisorName"></span></p>
                <p><strong>Current Wallet Balance:</strong> <span class="badge badge-info" id="walletBalance"></span></p>
                <p><strong>Requested Amount:</strong> <span class="badge badge-warning" id="requestedAmount"></span></p>
                <p><strong>Remaining Balance:</strong> <span class="badge badge-secondary" id="remainingBalance"></span></p>
            </div>

      <form id="approveForm" action="{{ route('admin.withdraw.approve') }}" method="POST" enctype="multipart/form-data"> 
            @csrf
            <input type="hidden" id="withdrawId" name="withdraw_id">

            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="check">Check</option>
                    <option value="upi">UPI</option>
                    <option value="bank">Through Bank</option>
                    <option value="online">Online</option>
                </select>
            </div>

            <div class="form-group">
                <label for="transaction_id">Transaction ID</label>
                <input type="text" id="transaction_id" name="transaction_id">
            </div>

            <div class="form-group receipt-upload">
                <label for="receipt">Upload Receipt</label>
                <input type="file" id="receipt" name="receipt" accept="image/*,application/pdf">
            </div>

            <div class="form-group">
                <label for="note">Note (Optional)</label>
                <textarea id="note" name="note" rows="3"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">Approve Request</button>
                <button type="button" class="btn btn-secondary" onclick="closeApproveModal()">Cancel</button>
            </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRejectModal()">&times;</span>
            <h3>Reject Withdrawal Request</h3>

            <form id="rejectForm" action="{{ route('admin.withdraw.reject') }}" method="POST">
                @csrf
                <input type="hidden" id="rejectWithdrawId" name="withdraw_id">

                <div class="form-group">
                    <label for="reason">Reason for Rejection</label>
                    <textarea id="reason" name="reason" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[title]').tooltip();

            // Clear search on empty input
            $('#datatableSearch_').on('input', function() {
                if ($(this).val().trim() === '') {
                    location.reload();
                }
            });
        });

        // Filter by status
        function filterByStatus(status) {
            const url = new URL(window.location);
            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            window.location.href = url.toString();
        }

        // Refresh table
        function refreshTable() {
            location.reload();
        }

        // Handle approve/reject actions
        $(document).on('click', '.manage-request', function() {
            const requestId = $(this).data('request-id');
            const requestStatus = $(this).data('request-status');
            const requestAmount = $(this).data('request-amount');
            const walletBalance = $(this).data('wallet-balance');
            const advisorName = $(this).closest('tr').find('.font-weight-bold').eq(1).text(); // Simple way to get advisor name

            if (requestStatus === 'approve') {
                openApproveModal(requestId, requestAmount, walletBalance, advisorName);
            } else if (requestStatus === 'reject') {
                openRejectModal(requestId, advisorName);
            }
        });

        // Approve Modal Functions
        function openApproveModal(requestId, requestedAmount, walletBalance, advisorName) {
            document.getElementById('withdrawId').value = requestId;
            document.getElementById('advisorName').textContent = advisorName;
            document.getElementById('walletBalance').textContent = '₹' + parseFloat(walletBalance).toFixed(2);
            document.getElementById('requestedAmount').textContent = '₹' + parseFloat(requestedAmount).toFixed(2);

            // Calculate remaining balance
            const remainingBalance = parseFloat(walletBalance) - parseFloat(requestedAmount);
            document.getElementById('remainingBalance').textContent = '₹' + remainingBalance.toFixed(2);

            document.getElementById('approveModal').style.display = 'block';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').style.display = 'none';
        }

        // Reject Modal Functions
        function openRejectModal(requestId) {
            document.getElementById('rejectWithdrawId').value = requestId;
            document.getElementById('rejectModal').style.display = 'block';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        // Handle form submissions
        $(document).ready(function() {
            $('#approveForm').on('submit', function(e) {
                e.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(response) {
                        if (response.status) {
                            closeApproveModal();
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Great'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorStr = '';
                        for (var key in errors) {
                            errorStr += errors[key][0] + '\n';
                        }
                        Swal.fire('Error', errorStr, 'error');
                    }
                });
            });

            $('#rejectForm').on('submit', function(e) {
                e.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(response) {
                        if (response.status) {
                            closeRejectModal();
                            Swal.fire({
                                title: 'Rejected',
                                text: response.message,
                                icon: 'info',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorStr = '';
                        for (var key in errors) {
                            errorStr += errors[key][0] + '\n';
                        }
                        Swal.fire('Error', errorStr, 'error');
                    }
                });
            });
        });

        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target == document.getElementById('approveModal')) {
                closeApproveModal();
            }
            if (event.target == document.getElementById('rejectModal')) {
                closeRejectModal();
            }
        }
    </script>
@endpush
