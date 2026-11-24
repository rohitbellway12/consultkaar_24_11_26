@extends('layouts.admin.app')

@section('title', 'Knowledge Reels')

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

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-movie"></i> Knowledge Reels
                    <span class="badge badge-soft-dark ml-2">{{ $reels->total() }}</span>
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.reel.earning-settings') }}" class="btn btn-warning">
                    <i class="tio-settings"></i> Earning Settings
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-header-title">Filter Reels</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Search by title / advisor">
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-control js-select2-custom">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Reel::CATEGORIES as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-control js-select2-custom">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.reel.list') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">All Reels</h5>
        </div>
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                   data-hs-datatables-options='{
                     "order": [],
                     "orderCellsTop": true
                   }'>
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Video</th>
                        <th>Title</th>
                        <th>Advisor</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Likes</th>
                        <th>Comments</th>
                        <th>Shares</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reels as $key => $reel)
                    <tr>
                        <td>{{ $key + $reels->firstItem() }}</td>
                        <td>
                            @if($reel->video)
                                <video width="80" height="120" style="object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="viewReel('{{ $reel->video }}')">
                                    <source src="{{ $reel->video }}" type="video/mp4">
                                </video>
                            @else
                                <span class="badge badge-soft-warning">No video</span>
                            @endif
                        </td>
                        <td><strong>{{ $reel->title }}</strong></td>
                        <td>{{ $reel->advisor->name ?? 'N/A' }}</td>
                        <td>{{ $reel->category }}</td>
                        <td>{{ $reel->views_count }}</td>
                        <td>{{ $reel->value_count }}</td>
                        <td>{{ $reel->comments_count }}</td>
                        <td>{{ $reel->shares_count }}</td>
                        <td>
                            @if($reel->status)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $reel->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.reel.view', $reel->id) }}"
                                   class="btn btn-primary btn-sm" title="View">
                                    View
                                </a>

                                @if($reel->status)
                                    <a href="{{ route('admin.reel.status', [$reel->id, 0]) }}"
                                       class="btn btn-warning btn-sm" title="Deactivate"
                                       onclick="return confirm('Deactivate this reel?')">
                                        Deactivate
                                    </a>
                                @else
                                    <a href="{{ route('admin.reel.status', [$reel->id, 1]) }}"
                                       class="btn btn-success btn-sm" title="Activate"
                                       onclick="return confirm('Activate this reel?')">
                                        Activate
                                    </a>
                                @endif

                                <button class="btn btn-danger btn-sm" title="Delete"
                                        onclick="deleteReel({{ $reel->id }})">
                                    Delete
                                </button>
                                <form action="{{ route('admin.reel.delete', [$reel->id]) }}"
                                      method="post" id="reel-{{ $reel->id }}">
                                    @csrf @method('delete')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center">No reels found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $reels->links() }}
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

function viewReel(videoUrl) {
    Swal.fire({
        title: 'Reel Preview',
        html: '<video width="100%" controls autoplay><source src="' + videoUrl + '" type="video/mp4"></video>',
        showCloseButton: true,
        showConfirmButton: false,
        width: 400
    });
}
</script>
@endpush
