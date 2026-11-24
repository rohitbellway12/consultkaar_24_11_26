@extends('layouts.admin.app')
@section('title','View Bank Details')
@section('content')
<div class="container py-4">
    
    <!-- Doctor Information Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Doctor Profile</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('storage/app/public/users/' . ($doctor->image ?? 'default-avatar.png')) }}" 
                         alt="Doctor Image" class="img-fluid rounded-circle shadow-sm" 
                         style="width: 180px; height: 180px; object-fit: cover;">
                </div>
                <div class="col-md-8">
                    <h4 class="text-primary">{{ $doctor->name ?? 'N/A' }}</h4>
                    <p class="mb-1"><strong>Email:</strong> {{ $doctor->email ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $doctor->mobile_number ?? 'N/A' }}</p>
                  <p class="mb-1"><strong>Specialization:</strong> {{ $specialization_name }}</p>

                    <p class="mb-0"><strong>Experience:</strong> {{ $doctor->experience ?? 'N/A' }} years</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Details Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Bank Details</h5>
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

    <!-- Action Buttons -->
    <div class="mt-4 d-flex justify-content-between">
        <a href="{{ route('admin.doctor.list') }}" class="btn btn-outline-secondary">
            <i class="tio-arrow-left"></i> Back to List
        </a>
      
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this doctor?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>

<form id="delete-form-{{ $doctor->id }}" 
      action="{{ route('admin.doctor.delete', $doctor->id) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection
