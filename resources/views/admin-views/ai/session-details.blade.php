@extends('layouts.admin.app')

@section('title', 'AI Session Transcript #' . $session->id)

@push('css_or_js')
<style>
    .chat-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 24px;
        max-height: 550px;
        overflow-y: auto;
    }
    .chat-bubble-user {
        background: #007bff;
        color: #fff;
        border-radius: 18px 18px 4px 18px;
        padding: 12px 18px;
        max-width: 75%;
        margin-left: auto;
        margin-bottom: 15px;
    }
    .chat-bubble-ai {
        background: #ffffff;
        color: #2d3748;
        border: 1px solid #e2e8f0;
        border-radius: 18px 18px 18px 4px;
        padding: 14px 20px;
        max-width: 80%;
        margin-right: auto;
        margin-bottom: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .ai-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <a href="{{ route('admin.ai.sessions') }}" class="mr-2 text-secondary"><i class="tio-back-ui"></i></a>
                    Session Transcript #{{ $session->id }}
                </h1>
                <p class="text-muted mb-0">UUID: <code>{{ $session->session_uuid }}</code></p>
            </div>
            <div class="col-sm-auto">
                <span class="badge badge-lg badge-{{ $session->status == 'active' ? 'success' : 'warning' }} p-2">
                    Status: {{ strtoupper($session->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chat Column -->
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="tio-chat-bubble"></i> Conversation Transcript</h5>
                    <span class="text-muted small">Total Messages: {{ $session->messages->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="chat-box">
                        @forelse($session->messages as $msg)
                            @if($msg->sender == 'user')
                                <div class="d-flex flex-column align-items-end mb-3">
                                    <span class="small text-muted mb-1">{{ $session->user ? $session->user->f_name : 'User' }} &bull; {{ $msg->created_at->format('h:i:s A') }}</span>
                                    <div class="chat-bubble-user">
                                        {{ $msg->message }}
                                    </div>
                                </div>
                            @else
                                <div class="d-flex flex-column align-items-start mb-3">
                                    <span class="small text-muted mb-1"><span class="ai-badge mr-1">GPT-6 Luna</span> AI Assistant &bull; {{ $msg->created_at->format('h:i:s A') }}</span>
                                    <div class="chat-bubble-ai">
                                        {!! nl2br(e($msg->message)) !!}
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-muted py-5">No messages sent in this session yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta & Summary Column -->
        <div class="col-lg-4 mb-3">
            <!-- Session Info -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="tio-info"></i> Session Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><strong>User:</strong> {{ $session->user ? ($session->user->f_name . ' ' . $session->user->l_name) : 'User #' . $session->user_id }}</li>
                        <li class="mb-2"><strong>Phone:</strong> {{ $session->user->phone ?? 'N/A' }}</li>
                        <li class="mb-2"><strong>Speciality:</strong> {{ $session->speciality->name ?? 'General' }}</li>
                        <li class="mb-2"><strong>Started:</strong> {{ $session->started_at ? $session->started_at->format('d M Y, h:i A') : 'N/A' }}</li>
                        <li class="mb-2"><strong>Expires:</strong> {{ $session->expires_at ? $session->expires_at->format('d M Y, h:i A') : 'N/A' }}</li>
                        <li><strong>Time Spent:</strong> {{ $session->started_at && $session->expires_at ? $session->started_at->diffInMinutes($session->expires_at) . ' Minutes' : 'N/A' }}</li>
                    </ul>
                </div>
            </div>

            <!-- AI Summary Card -->
            <div class="card shadow-sm mb-3" style="border-top: 3px solid #667eea;">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="tio-document-text"></i> AI Clinical Summary</h5>
                </div>
                <div class="card-body">
                    @if($session->summary)
                        <p class="text-dark">{{ $session->summary }}</p>
                    @else
                        <p class="text-muted font-italic mb-0">Summary will be automatically generated once the 5-minute session ends or user completes the chat.</p>
                    @endif
                </div>
            </div>

            <!-- Recommended Advisors Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="tio-user-big"></i> Recommended Advisors</h5>
                </div>
                <div class="card-body">
                    @if(!empty($session->recommended_advisor_ids) && is_array($session->recommended_advisor_ids))
                        @php
                            $doctors = \App\Models\Doctor::whereIn('id', $session->recommended_advisor_ids)->get();
                        @endphp
                        @forelse($doctors as $doc)
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                <div class="avatar avatar-sm avatar-circle mr-2">
                                    <img class="avatar-img" src="{{ asset('storage/app/public/doctor/' . $doc->image) }}" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'" alt="Doctor">
                                </div>
                                <div class="media-body">
                                    <h6 class="mb-0">{{ $doc->f_name }} {{ $doc->l_name }}</h6>
                                    <small class="text-muted">{{ $doc->speciality->title ?? 'Advisor' }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0 small">No advisors linked yet.</p>
                        @endforelse
                    @else
                        <p class="text-muted font-italic mb-0 small">Advisors are automatically recommended based on the user's category upon session completion.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
