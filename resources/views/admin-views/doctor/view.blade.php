@extends('layouts.admin.app')
@section('title', 'View Doctor Details')
@section('content')
    <div class="container py-4">

        <!-- Doctor Information Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Advisor Profile</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @php
                            $imagePath = $doctor->image
                                ? (env('APP_ENV') == 'local'
                                    ? asset('storage/users/' . $doctor->image)
                                    : asset('storage/app/public/users/' . $doctor->image))
                                : asset('assets/admin/img/160x160/img1.jpg');
                        @endphp
                        <img src="{{ $imagePath }}" alt="Advisor Image" class="img-fluid rounded-circle shadow-sm"
                            style="width: 180px; height: 180px; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <h4 class="text-primary">{{ $doctor->name ?? 'N/A' }}</h4>
                        <p class="mb-1"><strong>Email:</strong> {{ $doctor->email ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $doctor->mobile_number ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Gender:</strong> {{ ucfirst($doctor->gender ?? 'N/A') }}</p>
                        <p class="mb-1"><strong>Date of Birth:</strong> 
                            {{ $doctor->date_of_birth ? \Carbon\Carbon::parse($doctor->date_of_birth)->format('d M, Y') : 'N/A' }}
                        </p>
                        <p class="mb-1"><strong>Language:</strong> {{ $doctor->language ?? 'en' }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge badge-{{ $doctor->is_active ? 'success' : 'secondary' }}">
                                {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Details Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Professional Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Specialization:</strong>
                            {{
                                !empty($doctor->custom_specialization)
                                    ? $doctor->custom_specialization
                                    : ($specialization_name ?? 'N/A')
                            }}
                        </p>
                        <p class="mb-2"><strong>Domain:</strong>
                            {{
                                !empty($doctor->custom_domain_name)
                                    ? $doctor->custom_domain_name
                                    : ($doctor->domain->name ?? 'N/A')
                            }}
                        </p>
                        <p class="mb-2"><strong>Degree/Qualification:</strong> 
                            {{ $degree_name ?? ($doctor->qualification ?? 'N/A') }}
                        </p>
                        <p class="mb-2"><strong>Total Experience:</strong> 
                            {{ $doctor->experience ?? 'N/A' }} years
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Commission Percentage:</strong> 
                            {{ $doctor->commission_percentage ?? '0.00' }}%
                        </p>
                        <p class="mb-2"><strong>Registration Status:</strong>
                            <span class="badge badge-{{ $doctor->isregister ? 'success' : 'warning' }}">
                                {{ $doctor->isregister ? 'Registered' : 'Pending' }}
                            </span>
                        </p>
                        @if($doctor->rejection_reason)
                            <p class="mb-2"><strong>Rejection Reason:</strong> 
                                <span class="text-danger">{{ $doctor->rejection_reason }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Details Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 text-white">Location Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!--<p class="mb-2"><strong>Country:</strong> {{ $doctor->country->name ?? 'N/A' }}</p>-->
                        <p class="mb-2"><strong>State:</strong> {{ $doctor->state ?? 'N/A' }}</p>
                        <p class="mb-2"><strong>City:</strong> {{ $doctor->city ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <!--<p class="mb-2"><strong>Pincode:</strong> {{ $doctor->pincode ?? 'N/A' }}</p>-->
                        <p class="mb-2"><strong>Address:</strong> {{ $doctor->address ?? 'N/A' }}</p>
                        <!--<p class="mb-2"><strong>Location ID:</strong> {{ $doctor->location_id ?? 'N/A' }}</p>-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if ($doctor->description)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0 text-white">About / Description</h5>
                </div>
                <div class="card-body">
                    <p>{{ $doctor->description }}</p>
                </div>
            </div>
        @endif

        <!-- Documents Section -->
        @if (!empty($doctor->documents))
            @php
                $docs = is_array($doctor->documents) ? $doctor->documents : json_decode($doctor->documents, true);
            @endphp
            @if (is_array($docs) && count($docs) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Uploaded Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($docs as $doc)
                                @php
                                    $docPath = env('APP_ENV') == 'local'
                                        ? asset('storage/doctor_documents/' . $doc)
                                        : asset('storage/app/public/doctor_documents/' . $doc);
                                @endphp
                                <div class="col-md-3 mb-3">
                                    <a href="{{ $docPath }}" target="_blank">
                                        <img src="{{ $docPath }}" alt="Document" class="img-fluid border rounded"
                                            style="height: 120px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Identity Proofs -->
        @if (!empty($doctor->identity_proofs))
            @php
                $identityProofs = is_array($doctor->identity_proofs)
                    ? $doctor->identity_proofs
                    : json_decode($doctor->identity_proofs, true);
            @endphp
            @if (is_array($identityProofs) && count($identityProofs) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0 text-white">Identity Proofs</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($identityProofs as $proof)
                                <div class="col-md-12 mb-4">
                                    <h6>Type: <span
                                            class="text-capitalize">{{ str_replace('_', ' ', $proof['type'] ?? 'N/A') }}</span></h6>
                                    <div class="row">
                                        @if (!empty($proof['front']))
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Front Image:</strong></p>
                                                @php
                                                    $frontPath = env('APP_ENV') == 'local'
                                                        ? asset('storage/users/identity_proofs/' . $proof['front'])
                                                        : asset('storage/app/public/users/identity_proofs/' . $proof['front']);
                                                @endphp
                                                <a href="{{ $frontPath }}" target="_blank">
                                                    <img src="{{ $frontPath }}" alt="Front Image"
                                                        class="img-fluid border rounded"
                                                        style="max-height: 250px; width: 100%; object-fit: contain;">
                                                </a>
                                            </div>
                                        @endif
                                        @if (!empty($proof['back']))
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Back Image:</strong></p>
                                                @php
                                                    $backPath = env('APP_ENV') == 'local'
                                                        ? asset('storage/users/identity_proofs/' . $proof['back'])
                                                        : asset('storage/app/public/users/identity_proofs/' . $proof['back']);
                                                @endphp
                                                <a href="{{ $backPath }}" target="_blank">
                                                    <img src="{{ $backPath }}" alt="Back Image"
                                                        class="img-fluid border rounded"
                                                        style="max-height: 250px; width: 100%; object-fit: contain;">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Relevant Documents -->
        @if (!empty($doctor->relevant_documents))
            @php
                $relevantDocs = is_array($doctor->relevant_documents)
                    ? $doctor->relevant_documents
                    : json_decode($doctor->relevant_documents, true);
            @endphp
            @if (is_array($relevantDocs) && count($relevantDocs) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Relevant Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($relevantDocs as $doc)
                                @php
                                    $docPath = env('APP_ENV') == 'local'
                                        ? asset('storage/users/relevant_documents/' . ($doc['image'] ?? ''))
                                        : asset('storage/app/public/users/relevant_documents/' . ($doc['image'] ?? ''));
                                @endphp
                                @if(!empty($doc['image']))
                                <div class="col-md-3 mb-3">
                                    <p class="mb-1 small"><strong>Type ID: {{ $doc['type'] ?? 'N/A' }}</strong></p>
                                    <a href="{{ $docPath }}" target="_blank">
                                        <img src="{{ $docPath }}" alt="Document"
                                            class="img-fluid border rounded shadow-sm"
                                            style="height: 150px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Pricing & Features -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 text-white">Pricing & Features</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-3"><strong>Features:</strong></p>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center {{ $doctor->enable_chat ? 'bg-light' : '' }}">
                                    <i class="tio-messages tio-2x mb-2"></i>
                                    <h6>Chat</h6>
                                    <span class="badge badge-{{ $doctor->enable_chat ? 'success' : 'danger' }}">
                                        {{ $doctor->enable_chat ? 'Enabled' : 'Disabled' }}
                                    </span>
                                    @if ($doctor->enable_chat && $doctor->chat_fee)
                                        <p class="mt-2 mb-0"><strong>Fee:</strong> ₹{{ number_format($doctor->chat_fee, 2) }} <small>/min</small></p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center {{ $doctor->enable_audio ? 'bg-light' : '' }}">
                                    <i class="tio-call tio-2x mb-2"></i>
                                    <h6>Audio Call</h6>
                                    <span class="badge badge-{{ $doctor->enable_audio ? 'success' : 'danger' }}">
                                        {{ $doctor->enable_audio ? 'Enabled' : 'Disabled' }}
                                    </span>
                                    @if ($doctor->enable_audio && $doctor->audio_fee)
                                        <p class="mt-2 mb-0"><strong>Fee:</strong> ₹{{ number_format($doctor->audio_fee, 2) }} <small>/min</small></p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center {{ $doctor->enable_video ? 'bg-light' : '' }}">
                                    <i class="tio-videocam tio-2x mb-2"></i>
                                    <h6>Video Call</h6>
                                    <span class="badge badge-{{ $doctor->enable_video ? 'success' : 'danger' }}">
                                        {{ $doctor->enable_video ? 'Enabled' : 'Disabled' }}
                                    </span>
                                    @if ($doctor->enable_video && $doctor->video_fee)
                                        <p class="mt-2 mb-0"><strong>Fee:</strong> ₹{{ number_format($doctor->video_fee, 2) }} <small>/min</small></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0 text-white">Bank Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">Account Number:</th>
                            <td>{{ $doctor->bankDetails->account_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Bank Name:</th>
                            <td>{{ $doctor->bankDetails->bank_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>IFSC Code:</th>
                            <td>{{ $doctor->bankDetails->ifsc_code ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Account Holder Name:</th>
                            <td>{{ $doctor->bankDetails->account_holder_name ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Wallet Transactions Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Wallet Transactions (Earning History)</h5>
                <div class="ml-auto">
                    <strong>Total Earning: </strong>
                    <span class="badge badge-soft-info" style="font-size: 1.1em;">
                        ₹{{ number_format($doctor->wallet_amount ?? 0, 2) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Date Filter Form -->
                <form action="{{ url()->current() }}" method="GET" class="mb-4">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="small font-weight-bold">From Date</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="small font-weight-bold">To Date</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="tio-filter-list"></i> Filter
                            </button>
                            @if(request()->has('from_date'))
                                <a href="{{ url()->current() }}" class="btn btn-link btn-sm btn-block text-danger">Clear Filter</a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>User</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wallet_transactions_paginated ?? [] as $key => $tx)
                                <tr>
                                    <td>{{ ($wallet_transactions_paginated->currentPage() - 1) * $wallet_transactions_paginated->perPage() + $loop->iteration }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, h:i A') }}
                                    </td>
                                    <td>
                                        @if($tx->is_credit == '1')
                                            <span class="badge badge-soft-success">Credit</span>
                                        @else
                                            <span class="badge badge-soft-danger">Debit / Payout</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tx->is_credit == '1')
                                            <span class="text-success">+₹{{ number_format($tx->amount, 2) }}</span>
                                        @else
                                            <span class="text-danger">-₹{{ number_format($tx->amount, 2) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tx->patient_name)
                                            <strong>{{ $tx->patient_name }}</strong><br>
                                            <small class="text-muted">{{ $tx->patient_mobile }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $tx->description }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No earning records found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(isset($wallet_transactions_paginated) && $wallet_transactions_paginated->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {!! $wallet_transactions_paginated->links() !!}
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('admin.doctor.list') }}" class="btn btn-outline-secondary">
                <i class="tio-arrow-left"></i> Back to List
            </a>
            <div>
                <a href="{{ route('admin.doctor.edit', $doctor->id) }}" class="btn btn-primary">
                    <i class="tio-edit"></i> Edit Advisor
                </a>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this doctor?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>

    <form id="delete-form-{{ $doctor->id }}" action="{{ route('admin.doctor.delete', $doctor->id) }}" method="POST"
        style="display: none;">
        @csrf
        @method('DELETE')
    </form>

@endsection