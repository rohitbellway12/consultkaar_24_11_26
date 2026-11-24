@extends('layouts.admin.app')
@section('title', 'Blog Review')

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm">
                <h1 class="page-header-title">
                    Blog Review
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.blog.request-list') }}" class="btn btn-secondary">
                    <i class="tio-back-ui"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Blog Card -->
    <div class="card">
        <div class="card-body">

            <!-- Author Info -->
            <div class="mb-4">
                <h4 class="mb-1">{{ $blog->title }}</h4>
                <p class="text-muted mb-0">
                    By <strong>{{ $blog->user->name ?? 'N/A' }}</strong>
                    | {{ $blog->created_at->format('d M Y, h:i A') }}
                </p>
            </div>

            <!-- Blog Image -->
            @if($blog->image)
            <div class="mb-4">
                <img src="{{ asset('storage/app/public/blogs/'.$blog->image) }}"
                     class="img-fluid rounded"
                     style="max-height:300px;">
            </div>
            @endif

            <!-- Blog Content -->
            <div class="mb-4">
                <h5>Content</h5>
                <div class="border p-3 rounded" style="background:#f9f9f9">
                    {!! $blog->content !!}
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <strong>Status :</strong>
                @if($blog->status == 'pending')
                    <span class="badge badge-warning">Pending</span>
                @elseif($blog->status == 'approved')
                    <span class="badge badge-success">Approved</span>
                @else
                    <span class="badge badge-danger">Rejected</span>
                @endif
            </div>

            <!-- Rejection Reason -->
            @if($blog->status == 'rejected')
                <div class="alert alert-danger">
                    <strong>Rejection Reason:</strong><br>
                    {{ $blog->rejection_reason }}
                </div>
            @endif

            <!-- Action Buttons -->
            @if($blog->status == 'pending')
                <div class="d-flex gap-2">
                    <button class="btn btn-success"
                            onclick="approveBlog({{ $blog->id }})">
                        <i class="tio-done"></i> Approve
                    </button>

                    <button class="btn btn-danger"
                            onclick="rejectBlog({{ $blog->id }})">
                        <i class="tio-clear"></i> Reject
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>

@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function approveBlog(id) {
    Swal.fire({
        title: 'Approve Blog?',
        text: 'This blog will be published.',
        // Try using 'type' instead of 'icon'
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Approve'
    }).then((result) => {
        if (result.value) { // Changed from isConfirmed to value
            // Make sure the URL is correct
            window.location.href = "{{ route('admin.blog.approve', ['id' => ':id']) }}".replace(':id', id);
        }
    });
}

function rejectBlog(id) {
    Swal.fire({
        title: 'Reject Blog',
        input: 'text',
        inputLabel: 'Rejection Reason',
        inputPlaceholder: 'Enter rejection reason',
        // Try using 'type' instead of 'icon'
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Reject',
        preConfirm: (reason) => {
            if (!reason) {
                Swal.showValidationMessage('Rejection reason is required');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.value && result.value) { // Changed from isConfirmed to value
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('admin.blog.reject', ['id' => ':id']) }}".replace(':id', id);
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add rejection reason
            let reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = result.value;
            form.appendChild(reasonInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush