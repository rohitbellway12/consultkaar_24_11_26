@extends('layouts.admin.app')

@section('title', 'Knowledge Reel Earning Settings')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-settings"></i> Knowledge Reel Earning Settings
                </h1>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.reel.list') }}" class="btn btn-primary">
                    <i class="tio-chevron-left"></i> Back to Reels
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">Earning Criteria Settings</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.reel.earning-settings') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="minimum_views">Minimum Views <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="minimum_views" 
                                   name="minimum_views" 
                                   value="{{ old('minimum_views', $criteria->minimum_views) }}"
                                   min="0"
                                   required>
                            <small class="form-text text-muted">Advisor will be eligible for earnings when reel achieves this many views.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="minimum_values">Minimum Values (Likes) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="minimum_values" 
                                   name="minimum_values" 
                                   value="{{ old('minimum_values', $criteria->minimum_values) }}"
                                   min="0"
                                   required>
                            <small class="form-text text-muted">Advisor will be eligible for earnings when reel achieves this many values.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ $criteria->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Enable earning criteria check (Send notification when criteria is achieved)
                        </label>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
