@extends('layouts.admin.app')

@section('title', 'Reel Details')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-eye"></i> Reel #{{ $reel->id }} Details
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.reel.list') }}" class="btn btn-primary">
                    <i class="tio-chevron-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">Reel Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 text-center mb-3 mb-md-0">
                            @php
                                $videoUrl = $reel->video;
                            @endphp
                            @if($videoUrl)
                                <video width="100%" style="max-height: 400px; object-fit: cover; border-radius: 8px;" controls>
                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <div class="alert alert-warning">No video uploaded</div>
                            @endif
                        </div>
                        <div class="col-md-7">
                            <h4 class="text-primary mb-3">{{ $reel->title }}</h4>
                            <p class="mb-2"><strong>Description:</strong> {{ $reel->description ?: 'N/A' }}</p>
                            <p class="mb-2"><strong>Category:</strong> {{ $reel->category }}</p>
                            <p class="mb-2"><strong>Advisor:</strong> {{ $reel->advisor->name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Status:</strong>
                                @if($reel->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </p>
                            <p class="mb-2"><strong>Created At:</strong> {{ $reel->created_at->format('d M Y, h:i A') }}</p>
                            <p class="mb-2"><strong>Updated At:</strong> {{ $reel->updated_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <div class="info-label">Total Views</div>
                                <div class="info-value text-primary" style="font-size: 1.5rem; font-weight: bold;">{{ $reel->views_count }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <div class="info-label">Total Likes</div>
                                <div class="info-value text-danger" style="font-size: 1.5rem; font-weight: bold;">{{ $reel->value_count }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <div class="info-label">Total Comments</div>
                                <div class="info-value text-success" style="font-size: 1.5rem; font-weight: bold;">{{ $reel->comments_count }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card text-center">
                                <div class="info-label">Total Shares</div>
                                <div class="info-value text-warning" style="font-size: 1.5rem; font-weight: bold;">{{ $reel->shares_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">Comments ({{ $reel->comments->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($reel->comments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Comment</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reel->comments as $comment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $comment->user->name ?? 'Unknown' }}</td>
                                        <td>{{ $comment->comment }}</td>
                                        <td>{{ $comment->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">No comments yet</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-header-title">Actions</h5>
                </div>
                <div class="card-body">
                    @if($reel->status)
                        <a href="{{ route('admin.reel.status', [$reel->id, 0]) }}"
                           class="btn btn-warning btn-block mb-2"
                           onclick="return confirm('Deactivate this reel?')">
                            <i class="tio-blocked"></i> Deactivate Reel
                        </a>
                    @else
                        <a href="{{ route('admin.reel.status', [$reel->id, 1]) }}"
                           class="btn btn-success btn-block mb-2"
                           onclick="return confirm('Activate this reel?')">
                            <i class="tio-check"></i> Activate Reel
                        </a>
                    @endif

                    <button class="btn btn-danger btn-block mb-2"
                            onclick="deleteReel({{ $reel->id }})">
                        <i class="tio-delete"></i> Delete Reel
                    </button>
                    <form action="{{ route('admin.reel.delete', [$reel->id]) }}"
                          method="post" id="reel-{{ $reel->id }}">
                        @csrf @method('delete')
                    </form>

                    <a href="{{ route('admin.reel.list') }}" class="btn btn-secondary btn-block">
                        <i class="tio-chevron-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-title">Advisor Info</h5>
                </div>
                <div class="card-body">
                    @if($reel->advisor)
                        <p class="mb-2"><strong>Name:</strong> {{ $reel->advisor->name }}</p>
                        <p class="mb-2"><strong>Email:</strong> {{ $reel->advisor->email ?? 'N/A' }}</p>
                        <p class="mb-2"><strong>Mobile:</strong> {{ $reel->advisor->mobile_number ?? 'N/A' }}</p>
                        <p class="mb-0">
                            <strong>Status:</strong>
                            @if($reel->advisor->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </p>
                    @else
                        <p class="text-muted">Advisor information not available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
function deleteReel(id) {
    Swal.fire({
        title: 'Delete Reel?',
        text: "This action cannot be undone! The reel and its video will be permanently deleted.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete It',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.value || result.isConfirmed) {
            if ($('#reel-' + id).length) {
                $('#reel-' + id).submit();
            } else {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ url('admin/reel/delete') }}/" + id;

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
                form.submit();
            }
        }
    });
}
</script>
@endpush
