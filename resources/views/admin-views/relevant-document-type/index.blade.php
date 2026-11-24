@extends('layouts.admin.app')
@section('title', 'Relevant Document Types')
@push('css_or_js')
    <style>
        #datatable_filter, #datatable_info { display: none }
    </style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Relevant Document Types 
                    <span class="badge badge-soft-dark ml-2">{{ $documents->total() }}</span>
                </h1>
                <p class="text-muted">Documents that advisor need to upload during registration </p>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Add New Document Type</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.relevant-document-type.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">Document Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g." required>
                                </div>
                            </div>
                            <!--<div class="col-md-4">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label">Domain (Optional)</label>-->
                            <!--        <select name="domain_id" class="form-control">-->
                            <!--            <option value="">All Domains</option>-->
                            <!--            @foreach($domains as $domain)-->
                            <!--                <option value="{{ $domain->id }}">{{ $domain->name }}</option>-->
                            <!--            @endforeach-->
                            <!--        </select>-->
                            <!--        <small class="text-muted">Leave empty if applicable to all domains</small>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-md-4">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label">Language</label>-->
                            <!--        <select name="lang_code" class="form-control">-->
                            <!--            <option value="en">English</option>-->
                            <!--            <option value="fr">French</option>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="input-label">Description (Optional)</label>
                                    <input type="text" name="description" class="form-control" placeholder="Brief description of the document">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Add Document Type</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- List -->
    <div class="row" style="margin-top: 20px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Document Types List</h5>
                </div>
                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <!--<th>Domain</th>-->
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th style="width: 120px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $k => $doc)
                                <tr>
                                    <td>{{ $k + $documents->firstItem() }}</td>
                                    <td>{{ $doc->name }}</td>
                                    <!--<td>{{ $doc->domain->name ?? 'All Domains' }}</td>-->
                                    <td>{{ $doc->description ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.relevant-document-type.status', [$doc->id, $doc->status ? 0 : 1]) }}">
                                            <span class="badge badge-{{ $doc->status ? 'success' : 'danger' }}">
                                                {{ $doc->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-white" href="{{ route('admin.relevant-document-type.edit', $doc->id) }}" title="Edit">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn-danger" href="javascript:void(0)" onclick="confirmDelete({{ $doc->id }})" title="Delete">
                                            <i class="tio-delete"></i>
                                        </a>
                                        <form id="delete-form-{{ $doc->id }}" action="{{ route('admin.relevant-document-type.delete', $doc->id) }}" method="POST" style="display:none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="page-area">
                        {!! $documents->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this document type?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection
