@extends('layouts.admin.app')

@section('title', 'Blog Approval List')

@section('content')
<div class="content container-fluid">
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            Blog Approval List
            <span class="badge badge-soft-dark ml-2">{{ $blogs->total() }}</span>
        </h1>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">

                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Search title">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                        <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.blog.request-list') }}"
                       class="btn btn-secondary">Reset</a>
                </div>

            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="260">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $key => $blog)
                    <tr>
                        <td>{{ $key + $blogs->firstItem() }}</td>
                        <td><strong>{{ $blog->title }}</strong></td>
                        <td>{{ $blog->user->name ?? 'N/A' }}</td>
                        <td>
                            @if($blog->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($blog->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $blog->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.blog.view',$blog->id) }}"
                               class="btn btn-info btn-sm">View</a>

                          @if($blog->status == 'pending')
    <button class="btn btn-success btn-sm"  
        onclick="approveBlog({{ $blog->id }})">
        Approve
    </button>

    <button class="btn btn-danger btn-sm"
        onclick="rejectBlog({{ $blog->id }})">
        Reject
    </button>
@endif
 {{-- DELETE BUTTON - Sabhi blogs ke liye dikhega --}}
<form action="/admin/blog/delete/{{ $blog->id }}" method="POST" style="display: inline-block;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-dark btn-sm" onclick="return confirm('Are you sure you want to delete this blog?')">
        Delete
    </button>
</form>


                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No blogs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $blogs->links() }}
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
function deleteBlog(id) {
    Swal.fire({
        title: 'Delete Blog?',
        text: "This action cannot be undone! The blog will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete It',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Deleting blog with ID:', id); // Debug line
            
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = "/admin/blog/delete/" + id; // Use direct URL instead of route()
            console.log('Form action:', form.action); // Debug line
            
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            let methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            console.log('Submitting form...'); // Debug line
            form.submit();
        }
    });
}
</script>
@endpush
