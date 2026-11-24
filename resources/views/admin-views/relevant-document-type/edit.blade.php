@extends('layouts.admin.app')
@section('title', 'Edit Document Type')

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.relevant-document-type.add-new') }}">Relevant Document
                        Types</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">Edit Document Type</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.relevant-document-type.update', $document->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="input-label">Document Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $document->name }}"
                                    required>
                            </div>
                            <!--<div class="form-group">-->
                            <!--    <label class="input-label">Domain (Optional)</label>-->
                            <!--    <select name="domain_id" class="form-control">-->
                            <!--        <option value="">All Domains</option>-->
                            <!--        @foreach ($domains as $domain)-->
                            <!--            <option value="{{ $domain->id }}"-->
                            <!--                {{ $document->domain_id == $domain->id ? 'selected' : '' }}>-->
                            <!--                {{ $domain->name }}-->
                            <!--            </option>-->
                            <!--        @endforeach-->
                            <!--    </select>-->
                            <!--    <small class="text-muted">Leave empty if applicable to all domains</small>-->
                            <!--</div>-->
                            <div class="form-group">
                                <label class="input-label">Description (Optional)</label>
                                <input type="text" name="description" class="form-control"
                                    value="{{ $document->description }}">
                            </div>
                            <!--<div class="form-group">-->
                            <!--    <label class="input-label">Language</label>-->
                            <!--    <select name="lang_code" class="form-control">-->
                            <!--        <option value="en" {{ $document->lang_code == 'en' ? 'selected' : '' }}>English-->
                            <!--        </option>-->
                            <!--        <option value="fr" {{ $document->lang_code == 'fr' ? 'selected' : '' }}>French-->
                            <!--        </option>-->
                            <!--    </select>-->
                            <!--</div>-->
                            <hr>
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('admin.relevant-document-type.add-new') }}"
                                    class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
