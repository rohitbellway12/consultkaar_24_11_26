@extends('layouts.admin.app')

@section('title', 'Update Domain')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{ __('messages.update') }} Domain
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.domain.update', [$domain['id']]) }}" method="post">
                            @csrf

                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{ __('messages.name') }}</label>
                                <input type="text" name="name" value="{{ $domain['name'] }}" class="form-control"
                                    placeholder="Domain Name" required>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
