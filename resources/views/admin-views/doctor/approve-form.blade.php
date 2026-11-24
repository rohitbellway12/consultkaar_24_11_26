@extends('layouts.admin.app')

@section('title', 'Approve Advisor')

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.doctor.request-list') }}">Advisor Requests</a></li>
                <li class="breadcrumb-item active" aria-current="page">Approve Advisor</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">Approve Advisor: {{ $doctor->name }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.doctor.approve.submit', $doctor->id) }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="input-label" for="commission_percentage">Commission Percentage (%)</label>
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="commission_percentage" class="form-control" placeholder="e.g. 10" required>
                                    <small class="text-muted">This percentage will be deducted from each
                                        consultation.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="input-label" for="chat_fee">Chat Fee (Per Minute/Session)</label>
                                    <input type="number" step="0.01" min="0" name="chat_fee" class="form-control"
                                        placeholder="0.00" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="input-label" for="audio_fee">Audio Fee (Per Minute/Session)</label>
                                    <input type="number" step="0.01" min="0" name="audio_fee"
                                        class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="input-label" for="video_fee">Video Fee (Per Minute/Session)</label>
                                    <input type="number" step="0.01" min="0" name="video_fee"
                                        class="form-control" placeholder="0.00" required>
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('admin.doctor.request-list') }}" class="btn btn-secondary mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Approve & Activate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
