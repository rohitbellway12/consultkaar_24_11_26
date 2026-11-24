@extends('layouts.admin.app')

@section('title', 'Consultants')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <form method="GET" action="{{ url()->current() }}" id="filter-form">
                <div class="row g-2">

                    <div class="col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search customer / advisor">
                    </div>

                    <div class="col-md-2">
                        <select name="call_type" id="call_type" class="form-control">
                            <option value="">All Type</option>
                            <option value="chat" {{ request('call_type') == 'chat' ? 'selected' : '' }}>Chat</option>
                            <option value="audio" {{ request('call_type') == 'audio' ? 'selected' : '' }}>Audio</option>
                            <option value="video" {{ request('call_type') == 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="call_status" id="call_status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('call_status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="progress" {{ request('call_status') == 'progress' ? 'selected' : '' }}>Progress
                            </option>
                            <option value="complete" {{ request('call_status') == 'complete' ? 'selected' : '' }}>Complete
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="date_filter" id="date_filter" class="form-control">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week
                            </option>
                            <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>Custom
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2" id="date-from-container" style="display: none;">
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                    </div>

                    <div class="col-md-2" id="date-to-container" style="display: none;">
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ url()->current() }}" class="btn btn-secondary w-100">Reset</a>
                    </div>

                </div>
            </form>
        </div>

        <div class="card">

            <!-- Header -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Consultation Records</h5>
                <div>
                    <span class="me-2">Total Records: <strong>{{ $orders->total() }}</strong></span>
                    <span>Showing: <strong>{{ $orders->firstItem() }}-{{ $orders->lastItem() }}</strong></span>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-borderless table-align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Advisor</th>
                            <th>Duration</th>
                            <th>Chat Fee</th>
                            <th>Audio Fee</th>
                            <th>Video Fee</th>
                            <th>Call Type</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($orders as $key => $order)
                            @php
                                $chatFee = $order->doctor->chat_fee ?? 0;
                                $audioFee = $order->doctor->audio_fee ?? 0;
                                $videoFee = $order->doctor->video_fee ?? 0;

                                $duration = $order->total_min ?? 0;
                                $total = $order->deduction ?? 0;
                                $type = $order->call_type;
                            @endphp

                            <tr>
                                <td>{{ $key + $orders->firstItem() }}</td>
                                <td>{{ $order->id }}</td>
                                <td>{{ date('d M Y H:i', strtotime($order->created_at)) }}</td>

                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td>{{ $order->doctor->name ?? '-' }}</td>

                                <td>
                                    <span class="badge badge-info">{{ $duration }} min</span>
                                </td>

                                <td>
                                    @if ($type == 'chat')
                                        ₹ {{ number_format($chatFee, 2) }} /min
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if ($type == 'audio')
                                        ₹ {{ number_format($audioFee, 2) }} /min
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if ($type == 'video')
                                        ₹ {{ number_format($videoFee, 2) }} /min
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    <span class="badge badge-primary">{{ ucfirst($type) }}</span>
                                </td>

                                <td>
                                    <strong>₹ {{ number_format($total, 2) }}</strong>
                                </td>

                                <td>
                                    @if ($total > 0)
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $class = 'secondary';
                                        if ($order->call_status == 'complete') {
                                            $class = 'success';
                                        }
                                        if ($order->call_status == 'progress') {
                                            $class = 'primary';
                                        }
                                        if ($order->call_status == 'pending') {
                                            $class = 'warning';
                                        }
                                    @endphp

                                    <span class="badge badge-{{ $class }}">
                                        {{ ucfirst($order->call_status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                        @if ($orders->isEmpty())
                            <tr>
                                <td colspan="13" class="text-center py-4">No records found</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {!! $orders->links() !!}
            </div>

        </div>
    </div>

@endsection

@push('script_2')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle date filter dropdown
            const dateFilter = document.getElementById('date_filter');
            const dateFromContainer = document.getElementById('date-from-container');
            const dateToContainer = document.getElementById('date-to-container');

            // Show/hide date fields based on selection
            function toggleDateFields() {
                if (dateFilter.value === 'custom') {
                    dateFromContainer.style.display = 'block';
                    dateToContainer.style.display = 'block';
                } else {
                    dateFromContainer.style.display = 'none';
                    dateToContainer.style.display = 'none';
                }
            }

            // Initial state
            toggleDateFields();

            // Change event
            dateFilter.addEventListener('change', toggleDateFields);

            // Auto-submit form on filter change (optional)
            const filterForm = document.getElementById('filter-form');
            const filterSelects = ['call_type', 'call_status'];

            filterSelects.forEach(id => {
                document.getElementById(id).addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        });
    </script>
@endpush
