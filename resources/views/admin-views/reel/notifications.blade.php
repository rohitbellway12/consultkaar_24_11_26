@extends('layouts.admin.app')

@section('title', 'Reel Earning Notifications')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-notifications"></i> Reel Earning Notifications
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
            <h5 class="card-header-title">Criteria Achievement Notifications</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $key => $notification)
                        <tr>
                            <td>{{ $key + $notifications->firstItem() }}</td>
                            <td><strong>{{ $notification->title }}</strong></td>
                            <td>{{ $notification->description }}</td>
                            <td>
                                @if($notification->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('d M Y, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No reel earning notifications found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
