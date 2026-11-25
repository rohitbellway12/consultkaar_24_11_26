@extends('layouts.admin.app')

@section('title', 'AI Assistant Settings')

@push('css_or_js')
<style>
    .ai-stat-card {
        border-radius: 10px;
        padding: 20px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-left: 4px solid #00c9db;
    }
    .ai-badge-luna {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
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
                    <i class="tio-settings"></i> AI Assistant & Consultation Settings 
                    <span class="ai-badge-luna ml-2"><i class="tio-star"></i> GPT-6 Luna Enabled</span>
                </h1>
                <p class="text-muted mb-0">Configure your AI consultation engine, timer, cooldown rules, and expert recommendation criteria.</p>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.ai.questions') }}" class="btn btn-outline-primary mr-2">
                    <i class="tio-help-outlined"></i> Trending Questions
                </a>
                <a href="{{ route('admin.ai.sessions') }}" class="btn btn-outline-info">
                    <i class="tio-chat-outlined"></i> Chat History & Logs
                </a>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="ai-stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total AI Sessions</h6>
                        <h2 class="mb-0 text-primary font-weight-bold">{{ $totalSessions }}</h2>
                    </div>
                    <div><i class="tio-chat-outlined" style="font-size: 36px; color: #667eea;"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ai-stat-card" style="border-left-color: #00c9a7;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Active Ongoing Chats</h6>
                        <h2 class="mb-0 text-success font-weight-bold">{{ $activeSessions }}</h2>
                    </div>
                    <div><i class="tio-time" style="font-size: 36px; color: #00c9a7;"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ai-stat-card" style="border-left-color: #ffc107;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Trending Questions</h6>
                        <h2 class="mb-0 text-warning font-weight-bold">{{ $totalQuestions }}</h2>
                    </div>
                    <div><i class="tio-help-outlined" style="font-size: 36px; color: #ffc107;"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="tio-tune"></i> Core Configuration</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ai.update-settings') }}" method="POST">
                @csrf
                
                <!-- Status Switch -->
                <div class="form-group row align-items-center mb-4">
                    <label class="col-md-3 col-form-label font-weight-bold">AI Assistant Service Status</label>
                    <div class="col-md-9">
                        <label class="toggle-switch toggle-switch-sm d-flex align-items-center">
                            <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{ !empty($config['status']) ? 'checked' : '' }}>
                            <span class="toggle-switch-label">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                            <span class="ml-3 text-muted">Enable or disable AI chat across the mobile application.</span>
                        </label>
                    </div>
                </div>

                <hr>

                <!-- API Credentials -->
                <h5 class="text-primary mb-3"><i class="tio-key"></i> Model & Provider Credentials</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">AI Model Name <span class="text-danger">*</span></label>
                        <input type="text" name="model" class="form-control" value="{{ $config['model'] ?? 'gpt-6-luna' }}" placeholder="e.g. gpt-6-luna, gpt-4o" required>
                        <small class="form-text text-muted">Configured for <strong>GPT-6 Luna</strong>. You can change or specify any OpenAI-compatible model.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">API Base Endpoint URL</label>
                        <input type="url" name="api_endpoint" class="form-control" value="{{ $config['api_endpoint'] ?? 'https://api.openai.com/v1' }}" placeholder="https://api.openai.com/v1">
                        <small class="form-text text-muted">Base URL for GPT-6 Luna or OpenAI API service.</small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="font-weight-bold">API Secret Key <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="apiKeyInput" name="api_key" class="form-control" value="{{ $config['api_key'] ?? '' }}" placeholder="Enter your API secret key here">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleApiKeyVisibility()">
                                    <i class="tio-hidden-outlined" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Your private API key. Stored securely in database settings.</small>
                    </div>
                </div>

                <hr>

                <!-- Free Consultation Limits -->
                <h5 class="text-primary mb-3"><i class="tio-time"></i> Free Consultation & Restriction Rules</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Free Chat Duration (Minutes) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="duration_minutes" class="form-control" min="1" max="60" value="{{ $config['duration_minutes'] ?? 5 }}" required>
                            <div class="input-group-append"><span class="input-group-text">Min</span></div>
                        </div>
                        <small class="form-text text-muted">Duration of free AI consultation per session (Default: 5 Minutes).</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Cooldown Restriction (Hours) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="cooldown_hours" class="form-control" min="1" max="168" value="{{ $config['cooldown_hours'] ?? 24 }}" required>
                            <div class="input-group-append"><span class="input-group-text">Hours</span></div>
                        </div>
                        <small class="form-text text-muted">Cooldown before a user can start another free session (Default: 24 Hours).</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Max Messages Per Session <span class="text-danger">*</span></label>
                        <input type="number" name="max_messages" class="form-control" min="5" max="100" value="{{ $config['max_messages'] ?? 30 }}" required>
                        <small class="form-text text-muted">Safety cap to prevent infinite message spamming within the 5 minutes.</small>
                    </div>
                </div>

                <hr>

                <!-- Advisor Recommendations -->
                <h5 class="text-primary mb-3"><i class="tio-user-big"></i> Human Advisor Recommendations</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Number of Recommended Advisors <span class="text-danger">*</span></label>
                        <input type="number" name="recommendation_count" class="form-control" min="1" max="10" value="{{ $config['recommendation_count'] ?? 3 }}" required>
                        <small class="form-text text-muted">How many doctors/advisors to suggest to the user after 5-minute chat ends.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Recommendation Sorting Order <span class="text-danger">*</span></label>
                        <select name="recommendation_filter" class="form-control" required>
                            <option value="rating" {{ ($config['recommendation_filter'] ?? '') == 'rating' ? 'selected' : '' }}>Highest Rated Advisors First</option>
                            <option value="latest" {{ ($config['recommendation_filter'] ?? '') == 'latest' ? 'selected' : '' }}>Recently Joined Advisors</option>
                            <option value="random" {{ ($config['recommendation_filter'] ?? '') == 'random' ? 'selected' : '' }}>Random / Balanced Rotation</option>
                        </select>
                        <small class="form-text text-muted">Matches advisors from user's chosen category sorted by this rule.</small>
                    </div>
                </div>

                <hr>

                <!-- Prompt Engineering -->
                <h5 class="text-primary mb-3"><i class="tio-comment-text-outlined"></i> System Prompt & Instructions</h5>
                <div class="form-group mb-4">
                    <label class="font-weight-bold">System Instruction for GPT-6 Luna <span class="text-danger">*</span></label>
                    <textarea name="system_prompt" class="form-control" rows="4" required>{{ $config['system_prompt'] ?? '' }}</textarea>
                    <small class="form-text text-muted">The core instruction guiding how the AI assistant speaks, handles consultation inquiries, and transitions to human advisors.</small>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        <i class="tio-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    function toggleApiKeyVisibility() {
        var input = document.getElementById('apiKeyInput');
        var icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('tio-hidden-outlined');
            icon.classList.add('tio-visible-outlined');
        } else {
            input.type = 'password';
            icon.classList.remove('tio-visible-outlined');
            icon.classList.add('tio-hidden-outlined');
        }
    }
</script>
@endpush
