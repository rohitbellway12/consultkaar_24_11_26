@extends('layouts.admin.app')

@section('title', 'Appointment Details')

@push('css_or_js')
    <style>
        .info-card {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .info-label {
            font-weight: 600;
            color: #5a6474;
            font-size: 0.875rem;
        }
        .info-value {
            font-size: 1rem;
            color: #212529;
            margin-top: 0.25rem;
        }
        .section-title {
            border-bottom: 1px solid #e7eaf3;
            padding-bottom: 0.5rem;
            margin-bottom: 1.25rem;
            color: #377dff;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <i class="tio-eye"></i> Appointment #{{ $appointment->id }}
                    </h1>
                </div>
                <div class="col-sm-auto">
                    <a href="{{ url()->previous() }}" class="btn btn-primary">
                        <i class="tio-chevron-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Appointment Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-header-title">Appointment Overview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-card">
                            <div class="info-label">Appointment ID</div>
                            <div class="info-value">#{{ $appointment->id }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card">
                            <div class="info-label">Appointment Date</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($appointment->appoinmtent_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card">
                            <div class="info-label">Appointment Type</div>
                            <div class="info-value">
                                <span class="badge badge-soft-info">
                                    {{ ucfirst($appointment->appoinmtent_type ?? 'N/A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="info-card">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="badge badge-soft-{{ $appointment->status == 'confirmed' ? 'success' : ($appointment->status == 'pending' ? 'warning' : 'secondary') }}">
                                    {{ str_replace('_', ' ', ucfirst($appointment->status ?? 'N/A')) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card">
                            <div class="info-label">Payment Method</div>
                            <div class="info-value">
                                {{ ucfirst($appointment->payment_by ?? 'N/A') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card">
                            <div class="info-label">Payment Status</div>
                            <div class="info-value">
                                <span class="badge badge-soft-success">Paid</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-header-title">Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Name</div>
                            <div class="info-value">
                                {{ $appointment->patent_name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Mobile Number</div>
                            <div class="info-value">
                                {{ $appointment->patient_mobile_number ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                @if($appointment->reason)
                <div class="info-card mt-3">
                    <div class="info-label">Reason for Appointment</div>
                    <div class="info-value">{{ $appointment->reason }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Consultant (Doctor) Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-header-title">Consultant Information</h5>
            </div>
            <div class="card-body">
                @if($appointment->doctor)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Name</div>
                                <div class="info-value">{{ $appointment->doctor->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $appointment->doctor->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger">Consultant information not available.</div>
                @endif
            </div>
        </div>

        <!-- Fee Breakdown -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-title">Fee Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-card">
                            <div class="info-label">Consultation Fee</div>
                            <div class="info-value">Rs. {{ number_format($appointment->consultation_fee ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-card">
                            <div class="info-label">Chat Fee</div>
                            <div class="info-value">
                                @if($appointment->chat_fee)
                                    Rs. {{ number_format($appointment->chat_fee) }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-card">
                            <div class="info-label">Audio Fee</div>
                            <div class="info-value">
                                @if($appointment->audio_fee)
                                    Rs. {{ number_format($appointment->audio_fee) }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-card">
                            <div class="info-label">Video Fee</div>
                            <div class="info-value">
                                @if($appointment->video_fee)
                                    Rs. {{ number_format($appointment->video_fee) }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <!-- Optional: Add JS if needed later (e.g., print, etc.) -->
@endpush