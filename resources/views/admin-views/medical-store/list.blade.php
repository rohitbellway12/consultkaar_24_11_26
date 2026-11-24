@extends('layouts.admin.app')

@section('title', __('Medical Stores'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <i class="tio-store"></i> {{ __('Medical Stores') }}
        </h1>
    </div>
    <!-- End Page Header -->

    <div class="card">
        <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.medical-store.list') }}" class="d-flex flex-wrap align-items-center gap-2">
        <div class="input-group input-group-sm">
            <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name, email or phone') }}" value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">
                <i class="tio-search"></i>
            </button>
            <a href="{{ route('admin.medical-store.list') }}" class="btn btn-secondary">
                <i class="tio-clear"></i>
            </a>
        </div>
    </form>

    <!-- Add Button -->
    <a href="{{ route('admin.medical-store.add-new') }}" class="btn btn-sm btn-primary mt-2 mt-md-0">
        <i class="tio-add-circle"></i> {{ __('Add New Store') }}
    </a>
</div>



            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone Number') }}</th>
                            <th>{{ __('Address') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stores as $store)
                            <tr>
                                <td>{{ $store->name }}</td>
                                <td>{{ $store->email }}</td>
                                <td>{{ $store->mobile_number }}</td>
                                <td>{{ $store->address }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.medical-store.edit', $store->id) }}" class="btn btn-warning btn-sm">
                                        <i class="tio-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.medical-store.delete', $store->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="tio-delete"></i> {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $stores->links() }}
        </div>
    </div>
</div>
@endsection
