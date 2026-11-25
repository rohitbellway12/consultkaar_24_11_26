@extends('layouts.admin.app')

@section('title', 'AI Trending Questions')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-help-outlined"></i> AI Trending & Frequently Asked Questions
                </h1>
                <p class="text-muted mb-0">Manage predefined prompt suggestions displayed on the user's AI Chat Home screen.</p>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.ai.settings') }}" class="btn btn-outline-primary">
                    <i class="tio-settings"></i> AI Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Create Question Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="tio-add-circle"></i> Add New Trending Question</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ai.store-question') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Question / Prompt <span class="text-danger">*</span></label>
                        <input type="text" name="question" class="form-control" placeholder="e.g. What are the common symptoms of migraine?" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Speciality / Topic (Optional)</label>
                        <select name="category_id" class="form-control js-select2-custom">
                            <option value="">-- All Specialities / General --</option>
                            @foreach($specialities as $spec)
                                <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="font-weight-bold">Priority Order</label>
                        <input type="number" name="priority" class="form-control" value="0" min="0">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="tio-add"></i> Add Question</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Question Table Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Trending Questions List ({{ $questions->total() }})</h5>
            <form action="{{ route('admin.ai.questions') }}" method="GET" class="form-inline">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search question..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="tio-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th style="width: 50%;">Question</th>
                        <th>Speciality / Topic</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $key => $item)
                        <tr>
                            <td>{{ $questions->firstItem() + $key }}</td>
                            <td><strong>{{ $item->question }}</strong></td>
                            <td>
                                @if($item->speciality)
                                    <span class="badge badge-soft-info">{{ $item->speciality->name }}</span>
                                @else
                                    <span class="badge badge-soft-secondary">General</span>
                                @endif
                            </td>
                            <td>{{ $item->priority }}</td>
                            <td>
                                <a href="{{ route('admin.ai.status-question', [$item->id, $item->status ? 0 : 1]) }}"
                                   class="btn btn-sm btn-{{ $item->status ? 'success' : 'secondary' }}">
                                    {{ $item->status ? 'Active' : 'Inactive' }}
                                </a>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-white text-danger" 
                                        onclick="confirmDelete('{{ route('admin.ai.delete-question', $item->id) }}')">
                                    <i class="tio-delete"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No trending questions added yet. Add one above!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end">
            {{ $questions->links() }}
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('script_2')
<script>
    function confirmDelete(url) {
        if (confirm('Are you sure you want to delete this trending question?')) {
            var form = document.getElementById('deleteForm');
            form.action = url;
            form.submit();
        }
    }
</script>
@endpush
