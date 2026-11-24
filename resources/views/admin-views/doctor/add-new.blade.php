@extends('layouts.admin.app')
@section('title', 'Add Advisor')
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @media(max-width:375px) {
            #doctor-image-modal .modal-content {
                width: 367px !important;
                margin-left: 0 !important;
            }
        }

        @media(max-width:500px) {
            #doctor-image-modal .modal-content {
                width: 400px !important;
                margin-left: 0 !important;
            }
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        /* Optional: improve spacing for feature groups */
        .feature-group {
            margin-bottom: 12px;
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
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{ __('messages.add') }}
                        {{ __('messages.new') }} Advisor</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.doctor.add-new') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="en-link">English (EN)</a>
                                </li>
                                {{-- <li class="nav-item">
                     <a class="nav-link lang_link" href="#" id="fr-link">French (FR)</a>
                  </li> --}}
                            </ul>

                            <input type="hidden" name="lang_name" id="lang_name" value="en">


                            <div class="card mb-1 p-4 lang_form" id="en-form">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="name"> {{ __('messages.name') }} <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            
                                                <input type="text" name="name_en" class="form-control" id="name" placeholder="Name" value="{{ old('name_en') }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="mobile_number">Mobile Number <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                           <input type="text" name="mobile_number_en" value="{{ old('mobile_number_en') }}" class="form-control" id="mobile_number" placeholder="Ex : +91017********" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="email">{{ __('messages.email') }} <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                           <input type="email" name="email_en" value="{{ old('email_en') }}" class="form-control" id="email" placeholder="Ex : ex@gmail.com" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="gender">Gender <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                           <select name="gender_en" class="form-control gender js-select2-custom" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="domain_id">Domain <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <select name="domain_id_en" class="form-control domain_id" required>

                                                <option value="">Select Domain</option>
                                                @foreach ($domains as $dm)
                                                    <option value="{{ $dm->id }}">{{ $dm->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="speciality_id">Specialization <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <select name="speciality_id_en" class="form-control speciality_id" required>
                                                <option value="">Select Specialization</option>
                                                @foreach ($specialities as $sp)
                                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="qualification">Degree <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                           <select name="qualification_en" class="form-control qualification" required>

                                                <option value="">Select Degree</option>
                                                @foreach ($degrees as $degree)
                                                    <option value="{{ $degree->id }}">{{ $degree->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="experience">Total Experience (in years)
                                                <span style="color:red;"><strong>*</strong></span></label>
                                          <input type="number" name="experience_en" value="{{ old('experience_en') }}" class="form-control" placeholder="Experience" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="date_of_birth">Date of birth <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                           <input type="date" name="date_of_birth_en" class="form-control" id="date_of_birth" max="{{ date('Y-m-d') }}" required>

                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="state">State <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                           <select name="state_en" class="form-control state" required>
                                                <option selected="selected" value="">Select State:</option>
                                                <option value="andaman_and_nicobar_islands">Andaman and Nicobar Islands
                                                </option>
                                                <option value="andhra_pradesh">Andhra Pradesh</option>
                                                <option value="arunachal_pradesh">Arunachal Pradesh</option>
                                                <option value="assam">Assam</option>
                                                <option value="bihar">Bihar</option>
                                                <option value="chandigarh">Chandigarh</option>
                                                <option value="chhattisgarh">Chhattisgarh</option>
                                                <option value="dadra_and_nagar_haveli_and_daman_and_diu">Dadra and Nagar
                                                    Haveli and Daman and Diu</option>
                                                <option value="delhi">Delhi</option>
                                                <option value="goa">Goa</option>
                                                <option value="gujarat">Gujarat</option>
                                                <option value="haryana">Haryana</option>
                                                <option value="himachal_pradesh">Himachal Pradesh</option>
                                                <option value="jammu_and_kashmir">Jammu and Kashmir</option>
                                                <option value="jharkhand">Jharkhand</option>
                                                <option value="karnataka">Karnataka</option>
                                                <option value="kerala">Kerala</option>
                                                <option value="ladakh">Ladakh</option>
                                                <option value="lakshadweep">Lakshadweep</option>
                                                <option value="madhya_pradesh">Madhya Pradesh</option>
                                                <option value="maharashtra">Maharashtra</option>
                                                <option value="manipur">Manipur</option>
                                                <option value="meghalaya">Meghalaya</option>
                                                <option value="mizoram">Mizoram</option>
                                                <option value="nagaland">Nagaland</option>
                                                <option value="odisha">Odisha</option>
                                                <option value="puducherry">Puducherry</option>
                                                <option value="punjab">Punjab</option>
                                                <option value="rajasthan">Rajasthan</option>
                                                <option value="sikkim">Sikkim</option>
                                                <option value="tamil_nadu">Tamil Nadu</option>
                                                <option value="telangana">Telangana</option>
                                                <option value="tripura">Tripura</option>
                                                <option value="uttar_pradesh">Uttar Pradesh</option>
                                                <option value="uttarakhand">Uttarakhand</option>
                                                <option value="west_bengal">West Bengal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="city">City <span
                                                    style="color:red;"><strong>*</strong></span></label>
                                            <input type="text" name="city_en" placeholder="City" class="form-control" required>

                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="address">Address <span
                                                    style="color:red;"><strong>*</strong></span></label>
<textarea name="address_en" class="form-control" id="address" placeholder="Address" required>{{ old('address_en') }}</textarea>
</div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="profile_pic">Upload Profile Picture
                                                <span style="color:red;"><strong>*</strong></span></label>
                                   <input type="file" name="profile_pic_en" class="form-control profile_pic" required>
                                            <div class="col-12 mt-2">
                                                <center>
                                                    <img style="width: 30%;border: 1px solid; border-radius: 10px;"
                                                        id="viewer_en" src="" alt="" />
                                                </center>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="documents">Upload Documents
                                                (Multiple)</label>
                                            <input type="file" name="documents[]" class="form-control" id="documents"
                                                multiple>
                                            <small class="text-muted">You can select multiple images. (JPG, PNG,
                                                etc.)</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="language">Language</label>
                                            <input type="text" name="language_en" class="form-control" value="{{ old('language_en', 'en') }}" placeholder="e.g. en, hi, fr">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="input-label qcont" for="description">Description</label>
                                            <textarea name="description_en" class="form-control" placeholder="Advisor's bio or description...">{{ old('description_en') }}</textarea>

                                        </div>
                                        <div class="col-md-4">
                                            <label class="input-label qcont" for="commission_percentage">Commission
                                                (%)</label>
                                            <input type="number" step="0.01" min="0" max="100" name="commission_percentage_en" class="form-control" value="{{ old('commission_percentage_en', '0.00') }}">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Features Section -->
                             <!-- Features Section -->
<div class="form-group">
    <div class="row">
        <div class="col-md-4">
            <label class="input-label qcont d-block" for="is_active">Activate / Deactivate Profile</label>
            <div class="form-check form-switch">
                <input type="hidden" name="is_active_en" value="0">
                <input class="form-check-input" type="checkbox" id="is_active"
                    name="is_active_en" value="1"
                    {{ old('is_active_en') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>

        <div class="col-md-8">
            <label class="input-label qcont d-block">Features</label>
            <div class="d-flex flex-column" style="gap:12px;">
                <!-- Chat Feature -->
                <div class="feature-group">
                    <div class="form-check d-flex align-items-center" style="gap:8px;">
                        <input type="hidden" name="enable_chat_en" value="0">
                        <input class="form-check-input feature-checkbox" type="checkbox" 
                            id="enable_chat" name="enable_chat_en" value="1"
                            {{ old('enable_chat_en') ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_chat">Enable Chat</label>
                    </div>
                    <div class="feature-fee-input mt-1 {{ old('enable_chat_en') ? '' : 'd-none' }}">
                        <input type="number" step="0.01" min="0"
                            name="chat_fee_en" class="form-control form-control-sm"
                            placeholder="Chat Fee (per min)"
                            value="{{ old('chat_fee_en') }}">
                    </div>
                </div>

                <!-- Audio Feature -->
                <div class="feature-group">
                    <div class="form-check d-flex align-items-center" style="gap:8px;">
                        <input type="hidden" name="enable_audio_en" value="0">
                        <input class="form-check-input feature-checkbox" type="checkbox"
                            id="enable_audio" name="enable_audio_en" value="1"
                            {{ old('enable_audio_en') ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_audio">Enable Audio</label>
                    </div>
                    <div class="feature-fee-input mt-1 {{ old('enable_audio_en') ? '' : 'd-none' }}">
                        <input type="number" step="0.01" min="0"
                            name="audio_fee_en" class="form-control form-control-sm"
                            placeholder="Audio Fee (per min)"
                            value="{{ old('audio_fee_en') }}">
                    </div>
                </div>

                <!-- Video Feature -->
                <div class="feature-group">
                    <div class="form-check d-flex align-items-center" style="gap:8px;">
                        <input type="hidden" name="enable_video_en" value="0">
                        <input class="form-check-input feature-checkbox" type="checkbox"
                            id="enable_video" name="enable_video_en" value="1"
                            {{ old('enable_video_en') ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_video">Enable Video</label>
                    </div>
                    <div class="feature-fee-input mt-1 {{ old('enable_video_en') ? '' : 'd-none' }}">
                        <input type="number" step="0.01" min="0"
                            name="video_fee_en" class="form-control form-control-sm"
                            placeholder="Video Fee (per min)"
                            value="{{ old('video_fee_en') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                                <hr>
                            </div>

                            <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
                                <!-- French form fields would go here if needed -->
                                <!-- For now, we keep it minimal or comment it out -->
                                <p class="text-muted">French version not implemented.</p>
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
        function readURL(input, viewerId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + viewerId).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Profile picture preview
            $(".profile_pic").change(function() {
                let id = $(this).attr('id');
                if (id === 'profile_pic_en') {
                    readURL(this, 'viewer_en');
                } else if (id === 'profile_pic') {
                    readURL(this, 'viewer_en');
                }
            });

            // Initialize Select2
            $(".state, .qualification, .gender, .domain_id, .speciality_id").select2();
            
            // ========== Handle Domain Other input ==========
            function handleDomainOther() {
                let domainSelect = $('select[name="domain_id_en"]');
                if (domainSelect.length === 0) {
                    console.log("Domain select not found");
                    return;
                }
                
                let selectedText = domainSelect.find('option:selected').text();
                let selectedValue = domainSelect.val();
                
                console.log("Domain - Text:", selectedText, "Value:", selectedValue);
                
                if (selectedText.toLowerCase() === 'other' || selectedValue === 'other') {
                    if ($('#domain_other_container').length === 0) {
                        let otherInput = `
                            <div id="domain_other_container" class="mt-2">
                                <input type="text" 
                                       name="custom_domain_name" 
                                       class="form-control" 
                                       placeholder="Please specify domain name"
                                       required>
                            </div>
                        `;
                        domainSelect.closest('.col-md-4').append(otherInput);
                        console.log("Domain other input added");
                    }
                } else {
                    $('#domain_other_container').remove();
                    console.log("Domain other input removed");
                }
            }
            
            // ========== Handle Specialization Other input ==========
            function handleSpecializationOther() {
                let specSelect = $('select[name="speciality_id_en"]');
                if (specSelect.length === 0) {
                    console.log("Specialization select not found");
                    return;
                }
                
                let selectedText = specSelect.find('option:selected').text();
                let selectedValue = specSelect.val();
                
                console.log("Specialization - Text:", selectedText, "Value:", selectedValue);
                
                if (selectedText.toLowerCase() === 'other' || selectedValue === 'other') {
                    if ($('#specialization_other_container').length === 0) {
                        let otherInput = `
                            <div id="specialization_other_container" class="mt-2">
                                <input type="text" 
                                       name="custom_specialization" 
                                       class="form-control" 
                                       placeholder="Please specify specialization name"
                                       required>
                            </div>
                        `;
                        specSelect.closest('.col-md-4').append(otherInput);
                        console.log("Specialization other input added");
                    }
                } else {
                    $('#specialization_other_container').remove();
                    console.log("Specialization other input removed");
                }
            }
            
            // Add "Other" to Specialization only
            function addOtherOptions() {
                let specSelect = $('select[name="speciality_id_en"]');
                if (specSelect.length > 0) {
                    let otherExists = false;
                    specSelect.find('option').each(function() {
                        if ($(this).text().toLowerCase() === 'other') {
                            otherExists = true;
                        }
                    });
                    
                    if (!otherExists) {
                        specSelect.append('<option value="other">Other</option>');
                        console.log("Added Other to Specialization");
                    }
                }
            }
            
            addOtherOptions();
            
            // Bind change events
            $(document).on('change', 'select[name="domain_id_en"]', function() {
                handleDomainOther();
            });
            
            $(document).on('change', 'select[name="speciality_id_en"]', function() {
                handleSpecializationOther();
            });
            
            $('.domain_id').on('select2:select', function() {
                handleDomainOther();
            });
            
            $('.speciality_id').on('select2:select', function() {
                handleSpecializationOther();
            });
            
            setTimeout(function() {
                handleDomainOther();
                handleSpecializationOther();
            }, 500);
            
            // Form submission
            $('form').on('submit', function(e) {
                let domainSelect = $('select[name="domain_id"]');
                let domainText = domainSelect.find('option:selected').text();
                
                if (domainText.toLowerCase() === 'other') {
                    let otherValue = $('input[name="custom_domain_name"]').val();
                    if (!otherValue || otherValue.trim() === '') {
                        e.preventDefault();
                        alert('Please specify the domain name');
                        return false;
                    }
                }
                
                let specSelect = $('select[name="speciality_id"]');
                let specText = specSelect.find('option:selected').text();
                
                if (specText.toLowerCase() === 'other') {
                    let otherValue = $('input[name="custom_specialization"]').val();
                    if (!otherValue || otherValue.trim() === '') {
                        e.preventDefault();
                        alert('Please specify the specialization name');
                        return false;
                    }
                }
            });

            // Language tab switching
            $(".lang_link").click(function(e) {
                e.preventDefault();
                $(".lang_link").removeClass('active');
                $(".lang_form").addClass('d-none');
                $(this).addClass('active');
                let lang = this.id.replace('-link', '');
                $("#" + lang + "-form").removeClass('d-none');
                $("#lang_name").val(lang);

                if (lang === 'en') {
                    $("#en-form :input:not([type='hidden']):not([type='checkbox'])").prop('required', true);
                    $("#fr-form :input:not([type='hidden']):not([type='checkbox'])").prop('required', false);
                    setTimeout(function() {
                        handleDomainOther();
                        handleSpecializationOther();
                    }, 500);
                } else {
                    $("#fr-form :input:not([type='hidden']):not([type='checkbox'])").prop('required', true);
                    $("#en-form :input:not([type='hidden']):not([type='checkbox'])").prop('required', false);
                }
            });

            // Fee input toggle
            function toggleFeeInput(checkbox) {
                let feeInput = $(checkbox).closest('.feature-group').find('.feature-fee-input');
                if ($(checkbox).is(':checked')) {
                    feeInput.removeClass('d-none');
                    feeInput.find('input').prop('required', true);
                } else {
                    feeInput.addClass('d-none');
                    feeInput.find('input').val('').prop('required', false);
                }
            }

            $('.feature-checkbox').each(function() {
                toggleFeeInput(this);
                $(this).change(function() {
                    toggleFeeInput(this);
                });
            });
        });
    </script>
@endpush

