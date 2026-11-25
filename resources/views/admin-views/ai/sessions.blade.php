@extends('layouts.admin.app')

@section('title', 'AI Consultation Sessions')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-chat-outlined"></i> AI Chat Sessions & Logs
                </h1>
                <p class="text-muted mb-0">Monitor user conversations with GPT-6 Luna, 5-minute countdowns, and generated clinical summaries.</p>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.ai.settings') }}" class="btn btn-outline-primary">
                    <i class="tio-settings"></i> AI Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Sessions Filter & Table Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="card-title mb-0">Chat Sessions ({{ $sessions->total() }})</h5>

            <div class="d-flex align-items-center mt-2 mt-sm-0">
                <form action="{{ route('admin.ai.sessions') }}" method="GET" class="form-inline">
                    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">-- All Statuses --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (In-Progress)</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired (5 Mins Finished)</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="User name or phone..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="tio-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Speciality / Topic</th>
                        <th>Started At</th>
                        <th>Expires At</th>
                        <th>Status</th>
                        <th>AI Summary</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $key => $session)
                        <tr>
                            <td>{{ $sessions->firstItem() + $key }}</td>
                            <td>
                                @if($session->user)
                                    <strong>{{ $session->user->f_name }} {{ $session->user->l_name }}</strong>
                                    <div class="text-muted small">{{ $session->user->phone ?? $session->user->email }}</div>
                                @else
                                    <span class="text-muted">Guest / User #{{ $session->user_id }}</span>
                                @endif
                            </td>
                            <td>
                                @if($session->speciality)
                                    <span class="badge badge-soft-info">{{ $session->speciality->name }}</span>
                                @else
                                    <span class="badge badge-soft-secondary">General</span>
                                @endif
                            </td>
                            <td>{{ $session->started_at ? $session->started_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td>{{ $session->expires_at ? $session->expires_at->format('h:i A') : 'N/A' }}</td>
                            <td>
                                @if($session->status == 'active')
                                    <span class="badge badge-success"><i class="tio-time"></i> Active</span>
                                @elseif($session->status == 'expired')
                                    <span class="badge badge-warning">5-Min Expired</span>
                                @else
                                    <span class="badge badge-info">Completed</span>
                                @endif
                            </td>
                            <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $session->summary ?? 'Summary not generated yet' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.ai.session-details', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="tio-visible"></i> View Chat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No AI chat sessions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection
