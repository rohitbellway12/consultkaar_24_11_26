@extends('layouts.admin.app')
@section('title', 'Update Advisor')
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @media(max-width:375px) {
            #Customer-image-modal .modal-content {
                width: 367px !important;
                margin-left: 0 !important;
            }
        }

        @media(max-width:500px) {
            #Customer-image-modal .modal-content {
                width: 400px !important;
                margin-left: 0 !important;
            }
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .feature-group {
            margin-bottom: 8px;
        }

        .feature-fee-input {
            margin-top: 6px;
        }
    </style>
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{ __('messages.update') }} Advisor
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.doctor.update', [$user_record_en['id']]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="lang_name" id="lang_name" value="en">
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="en-link">English (EN)</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="fr-link">French (FR)</a>
                                </li>
                            </ul>

                            <!-- English Form -->
                            <div class="card mb-1 p-4 lang_form" id="en-form">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="name"> {{ __('messages.name') }} <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                placeholder="Name" value="{{ $user_record_en['name'] }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="phmobile_numberone">Mobile Number <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <input type="text" name="mobile_number"
                                                value="{{ $user_record_en['mobile_number'] }}" class="form-control"
                                                id="mobile_number" placeholder="Ex : +91017********" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="email">{{ __('messages.email') }} <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <input type="email" name="email" value="{{ $user_record_en['email'] }}"
                                                class="form-control" id="email" placeholder="Ex : ex@gmail.com"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="gender">Gender <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <select name="gender" class="form-control gender" id="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male"
                                                    {{ ($user_record_en['gender'] ?? '') == 'Male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="Female"
                                                    {{ ($user_record_en['gender'] ?? '') == 'Female' ? 'selected' : '' }}>
                                                    Female
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="domain_id">Domain <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <select name="domain_id" class="form-control domain_id" required>
                                                <option value="">Select Domain</option>
                                                @foreach ($domains as $dm)
                                                    <option value="{{ $dm->id }}"
                                                        {{ ($user_record_en->domain_id ?? '') == $dm->id ? 'selected' : '' }}>
                                                        {{ $dm->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="speciality_id">Specialization <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <select name="speciality_id" class="form-control speciality_id" required>
                                                <option value="">Select Specialization</option>
                                                @foreach ($specialities as $sp)
                                                    <option value="{{ $sp->id }}"
                                                        {{ ($user_record_en->speciality_id ?? '') == $sp->id ? 'selected' : '' }}>
                                                        {{ $sp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="qualification">Degree
                                                <span style="color:red;"><strong>*</strong></span></label>
                                            <select name="qualification" class="form-control qualification" required>
                                                <option value="">Select Degree</option>
                                                @foreach ($degrees as $degree)
                                                    <option value="{{ $degree->id }}"
                                                        {{ ($user_record_en->qualification ?? '') == $degree->id ? 'selected' : '' }}>
                                                        {{ $degree->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="experience">Total
                                                Experience (in years) <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <input type="number" name="experience"
                                                value="{{ $user_record_en->experience ?? '' }}" class="form-control"
                                                placeholder="Experience" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="date_of_birth">Date of birth
                                                <span style="color:red;"><strong>*</strong></span></label>
                                            <input type="date" name="date_of_birth"
                                                value="{{ $user_record_en->date_of_birth ?? '' }}" class="form-control"
                                                max="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="state">State <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <select name="state" class="form-control state" required>
                                                <option value="">Select State:</option>
                                                @php
                                                    $states = [
                                                        'andaman_and_nicobar_islands' => 'Andaman and Nicobar Islands',
                                                        'andhra_pradesh' => 'Andhra Pradesh',
                                                        'arunachal_pradesh' => 'Arunachal Pradesh',
                                                        'assam' => 'Assam',
                                                        'bihar' => 'Bihar',
                                                        'chandigarh' => 'Chandigarh',
                                                        'chhattisgarh' => 'Chhattisgarh',
                                                        'dadra_and_nagar_haveli_and_daman_and_diu' =>
                                                            'Dadra and Nagar Haveli and Daman and Diu',
                                                        'delhi' => 'Delhi',
                                                        'goa' => 'Goa',
                                                        'gujarat' => 'Gujarat',
                                                        'haryana' => 'Haryana',
                                                        'himachal_pradesh' => 'Himachal Pradesh',
                                                        'jammu_and_kashmir' => 'Jammu and Kashmir',
                                                        'jharkhand' => 'Jharkhand',
                                                        'karnataka' => 'Karnataka',
                                                        'kerala' => 'Kerala',
                                                        'ladakh' => 'Ladakh',
                                                        'lakshadweep' => 'Lakshadweep',
                                                        'madhya_pradesh' => 'Madhya Pradesh',
                                                        'maharashtra' => 'Maharashtra',
                                                        'manipur' => 'Manipur',
                                                        'meghalaya' => 'Meghalaya',
                                                        'mizoram' => 'Mizoram',
                                                        'nagaland' => 'Nagaland',
                                                        'odisha' => 'Odisha',
                                                        'puducherry' => 'Puducherry',
                                                        'punjab' => 'Punjab',
                                                        'rajasthan' => 'Rajasthan',
                                                        'sikkim' => 'Sikkim',
                                                        'tamil_nadu' => 'Tamil Nadu',
                                                        'telangana' => 'Telangana',
                                                        'tripura' => 'Tripura',
                                                        'uttar_pradesh' => 'Uttar Pradesh',
                                                        'uttarakhand' => 'Uttarakhand',
                                                        'west_bengal' => 'West Bengal',
                                                    ];
                                                @endphp
                                                @foreach ($states as $val => $label)
                                                    <option value="{{ $val }}"
                                                        {{ ($user_record_en->state ?? '') == $val ? 'selected' : '' }}>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="city">City <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <input type="text" name="city"
                                                value="{{ $user_record_en->city ?? '' }}" placeholder="City"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="address">Address <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <textarea name="address" class="form-control" id="address" placeholder="Address" required>{{ $user_record_en->address ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="profile_pic">Update
                                                Profile Picture</label>
                                            <input type="file" name="profile_pic" class="form-control profile_pic">
                                            <div class="col-12 mt-2">
                                                <center>
                                                    <img style="width: 30%;border: 1px solid; border-radius: 10px;"
                                                        id="viewer_en"
                                                        src="{{ asset('storage/app/public/users') . '/' . ($user_record_en->image ?? '') }}"
                                                        onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'"
                                                        alt="" />
                                                </center>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="documents">Upload More
                                                Documents</label>
                                            <input type="file" name="documents[]" class="form-control" id="documents"
                                                multiple>
                                            <small class="text-muted">Will be appended to existing
                                                documents.</small>

                                            @if (isset($user_record_en->documents) && !empty($user_record_en->documents))
                                                @php($docs = is_array($user_record_en->documents) ? $user_record_en->documents : json_decode($user_record_en->documents, true))
                                                @if (is_array($docs) && count($docs) > 0)
                                                    <div class="d-flex flex-wrap mt-2" style="gap: 10px;">
                                                        @foreach ($docs as $doc)
                                                            <div class="document-item" style="position: relative;">
                                                                <img src="{{ asset('storage/app/public/doctor_documents') . '/' . $doc }}"
                                                                    style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;"
                                                                    onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="language">Language</label>
                                            <input type="text" name="language" class="form-control"
                                                value="{{ $user_record_en->language ?? 'en' }}"
                                                placeholder="e.g. en, hi, fr">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="input-label qcont" for="description">Description</label>
                                            <textarea name="description" class="form-control" placeholder="Advisor's bio or description...">{{ $user_record_en->description ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="commission_percentage">Commission
                                                (%)</label>
                                            <input type="number" step="0.01" min="0" max="100"
                                                name="commission_percentage" class="form-control"
                                                value="{{ $user_record_en->commission_percentage ?? '0.00' }}">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Features Section -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont d-block" for="is_active">Profile
                                                Status</label>
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="is_active" value="0">
                                                <input class="form-check-input" type="checkbox" id="is_active_en"
                                                    name="is_active" value="1"
                                                    {{ ($user_record_en->is_active ?? 0) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active_en">Active</label>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <label class="input-label qcont d-block">Features</label>
                                            <div class="d-flex flex-column" style="gap:12px;">
                                                <!-- Chat Feature -->
                                                <div class="feature-group">
                                                    <div class="form-check d-flex align-items-center" style="gap:8px;">
                                                        <input type="hidden" name="enable_chat" value="0">
                                                        <input class="form-check-input feature-checkbox" type="checkbox"
                                                            id="enable_chat_en" name="enable_chat" value="1"
                                                            {{ ($user_record_en->enable_chat ?? 0) == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="enable_chat_en">Enable
                                                            Chat</label>
                                                    </div>
                                                    <div
                                                        class="feature-fee-input {{ ($user_record_en->enable_chat ?? 0) == 1 ? '' : 'd-none' }}">
                                                        <input type="number" step="0.01" min="0"
                                                            name="chat_fee" class="form-control form-control-sm"
                                                            placeholder="Chat Fee"
                                                            value="{{ $user_record_en->chat_fee ?? '' }}">
                                                    </div>
                                                </div>

                                                <!-- Audio Feature -->
                                                <div class="feature-group">
                                                    <div class="form-check d-flex align-items-center" style="gap:8px;">
                                                        <input type="hidden" name="enable_audio" value="0">
                                                        <input class="form-check-input feature-checkbox" type="checkbox"
                                                            id="enable_audio_en" name="enable_audio" value="1"
                                                            {{ ($user_record_en->enable_audio ?? 0) == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="enable_audio_en">Enable
                                                            Audio</label>
                                                    </div>
                                                    <div
                                                        class="feature-fee-input {{ ($user_record_en->enable_audio ?? 0) == 1 ? '' : 'd-none' }}">
                                                        <input type="number" step="0.01" min="0"
                                                            name="audio_fee" class="form-control form-control-sm"
                                                            placeholder="Audio Fee"
                                                            value="{{ $user_record_en->audio_fee ?? '' }}">
                                                    </div>
                                                </div>

                                                <!-- Video Feature -->
                                                <div class="feature-group">
                                                    <div class="form-check d-flex align-items-center" style="gap:8px;">
                                                        <input type="hidden" name="enable_video" value="0">
                                                        <input class="form-check-input feature-checkbox" type="checkbox"
                                                            id="enable_video_en" name="enable_video" value="1"
                                                            {{ ($user_record_en->enable_video ?? 0) == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="enable_video_en">Enable
                                                            Video</label>
                                                    </div>
                                                    <div
                                                        class="feature-fee-input {{ ($user_record_en->enable_video ?? 0) == 1 ? '' : 'd-none' }}">
                                                        <input type="number" step="0.01" min="0"
                                                            name="video_fee" class="form-control form-control-sm"
                                                            placeholder="Video Fee"
                                                            value="{{ $user_record_en->video_fee ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                            </div>

                            <!-- French Form -->
                            <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="name"> {{ __('messages.name') }}
                                                (FR) <span style="color:red;"><strong>*</strong></span></label>
                                            <input type="text" name="name" class="form-control" placeholder="Nom"
                                                value="{{ $user_record_fr->name ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="mobile_number">Mobile (FR) <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <input type="text" name="mobile_number"
                                                value="{{ $user_record_fr->mobile_number ?? ($user_record_en->mobile_number ?? '') }}"
                                                class="form-control" placeholder="Mobile">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="email">Email (FR)</label>
                                            <input type="email" name="email"
                                                value="{{ $user_record_fr->email ?? ($user_record_en->email ?? '') }}"
                                                class="form-control" placeholder="Email">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="gender">Gender (FR)</label>
                                            <select name="gender" class="form-control gender js-select2-custom">
                                                <option value="">Select Gender</option>
                                                <option value="Male"
                                                    {{ ($user_record_fr->gender ?? ($user_record_en->gender ?? '')) == 'Male' ? 'selected' : '' }}>
                                                    Male
                                                </option>
                                                <option value="Female"
                                                    {{ ($user_record_fr->gender ?? ($user_record_en->gender ?? '')) == 'Female' ? 'selected' : '' }}>
                                                    Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="domain_id">Domain (FR)</label>
                                            <select name="domain_id" class="form-control domain_id">
                                                <option value="">Select Domain</option>
                                                @foreach ($domains as $dm)
                                                    <option value="{{ $dm->id }}"
                                                        {{ ($user_record_fr->domain_id ?? ($user_record_en->domain_id ?? '')) == $dm->id ? 'selected' : '' }}>
                                                        {{ $dm->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="speciality_id">Specialization
                                                (FR)</label>
                                            <select name="speciality_id" class="form-control speciality_id">
                                                <option value="">Select Specialization</option>
                                                @foreach ($french_specialities as $sp)
                                                    <option value="{{ $sp->id }}"
                                                        {{ ($user_record_fr->speciality_id ?? '') == $sp->id ? 'selected' : '' }}>
                                                        {{ $sp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="qualification">Degree (FR)</label>
                                            <select name="qualification" class="form-control qualification">
                                                <option value="">Select Degree</option>
                                                @foreach ($degrees as $degree)
                                                    <option value="{{ $degree->id }}"
                                                        {{ ($user_record_fr->qualification ?? ($user_record_en->qualification ?? '')) == $degree->id ? 'selected' : '' }}>
                                                        {{ $degree->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="experience">Experience (FR)</label>
                                            <input type="number" name="experience"
                                                value="{{ $user_record_fr->experience ?? ($user_record_en->experience ?? '') }}"
                                                class="form-control" placeholder="Experience" step="0.01"
                                                min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="date_of_birth">DOB (FR)</label>
                                            <input type="date" name="date_of_birth"
                                                value="{{ $user_record_fr->date_of_birth ?? ($user_record_en->date_of_birth ?? '') }}"
                                                class="form-control" max="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="state">State (FR)</label>
                                            <select name="state" class="form-control state">
                                                <option value="">Select State:</option>
                                                @foreach ($states as $val => $label)
                                                    <option value="{{ $val }}"
                                                        {{ ($user_record_fr->state ?? ($user_record_en->state ?? '')) == $val ? 'selected' : '' }}>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="city">City (FR)</label>
                                            <input type="text" name="city"
                                                value="{{ $user_record_fr->city ?? ($user_record_en->city ?? '') }}"
                                                placeholder="City" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="address">Address (FR)</label>
                                            <textarea name="address" class="form-control" placeholder="Address">{{ $user_record_fr->address ?? ($user_record_en->address ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="input-label qcont" for="description">Description (FR)</label>
                                            <textarea name="description" class="form-control" placeholder="Description en français...">{{ $user_record_fr->description ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted mt-2 small">Note: Some fields are pre-filled from English for
                                    convenience.
                                </p>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function readURL(input, viewerId = 'viewer') {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + viewerId).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            $(".profile_pic").change(function() {
                var viewer = $(this).closest('.col-md-4').find('img').attr('id') || 'viewer';
                readURL(this, viewer);
            });

            $(".state, .speciality_id, .qualification, .gender").select2();

            // Rename fields for multilingual
            $("#en-form").find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name && name !== 'documents[]' && !name.includes('_en')) {
                    $(this).attr('name', name + '_en');
                }
            });

            $("#fr-form").find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name && name !== 'documents[]' && !name.includes('_fr')) {
                    $(this).attr('name', name + '_fr');
                }
            });

            // Set initial required based on current lang
            $("#en-form").find('input:not([type="checkbox"]):not([type="file"]), select, textarea').attr('required',
                'required');
            $("#fr-form").find('input:not([type="checkbox"]):not([type="file"]), select, textarea').removeAttr(
                'required');

            // Language tab switch
            $(".lang_link").click(function(e) {
                e.preventDefault();
                $(".lang_link").removeClass('active');
                $(".lang_form").addClass('d-none');
                $(this).addClass('active');
                let lang = this.id.replace('-link', '');
                $("#" + lang + "-form").removeClass('d-none');
                $("#lang_name").val(lang);

                if (lang === 'en') {
                    $("#en-form").find('input:not([type="checkbox"]):not([type="file"]), select, textarea')
                        .attr('required', 'required');
                    $("#fr-form").find('input:not([type="checkbox"]):not([type="file"]), select, textarea')
                        .removeAttr('required');
                } else {
                    $("#fr-form").find('input:not([type="checkbox"]):not([type="file"]), select, textarea')
                        .attr('required', 'required');
                    $("#en-form").find('input:not([type="checkbox"]):not([type="file"]), select, textarea')
                        .removeAttr('required');
                }
            });

            // Fee input toggle logic
            function toggleFeatureFee(checkbox) {
                const feeInput = $(checkbox).closest('.feature-group').find('.feature-fee-input');
                if ($(checkbox).is(':checked')) {
                    feeInput.removeClass('d-none');
                } else {
                    feeInput.addClass('d-none');
                }
            }

            $('.feature-checkbox').each(function() {
                toggleFeatureFee(this);
                $(this).change(function() {
                    toggleFeatureFee(this);
                });
            });
        });
    </script>
@endpush
