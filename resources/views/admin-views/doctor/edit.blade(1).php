@extends('layouts.admin.app')
@section('title','Update doctor')
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @media(max-width:375px){
         #Customer-image-modal .modal-content{
           width: 367px !important;
         margin-left: 0 !important;
     }
    
     }

@media(max-width:500px){
 #Customer-image-modal .modal-content{
           width: 400px !important;
         margin-left: 0 !important;
     }
   
   
}
 </style>
@endpush

@section('content')

    ?>
<div class="content container-fluid"> 
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.update')}}  Doctor
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
                    <form action="{{route('admin.doctor.update',[$user_record_en['id']])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="lang_name" id="lang_name" value="en">
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                               <a class="nav-link lang_link active" href="#" id="en-link">English (EN)</a>
                            </li>
                          {{--   <li class="nav-item">
                               <a class="nav-link lang_link" href="#" id="fr-link">French (FR)</a>
                            </li> --}}
                         </ul>
                         <div class="card mb-1 p-4 lang_form" id="en-form">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="name"> {{__('messages.name')}} <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="name" class="form-control" id="name"
                                               placeholder="Name" value="{{$user_record_en['name']}}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="phmobile_numberone">Mobile Number <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="mobile_number" value="{{$user_record_en['mobile_number']}}" class="form-control" id="mobile_number"
                                               placeholder="Ex : +91017********" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="email">{{__('messages.email')}} <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="email" name="email" value="{{$user_record_en['email']}}" class="form-control" id="email" placeholder="Ex : ex@gmail.com"  autocomplete="off">
                                    </div>                                
                                </div>
                            </div>                      
                             <br>
                            <div class="form-group">
                                <div class="row">                            
                                    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="gender">Gender <span style="color:red;"><strong>*</strong></span></label>
                                        <select name="gender" class="form-control gender" id="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{  ($user_record_en['gender'] == 'Male') ? "selected=selected" : ''}}>Male</option>
                                            <option value="Female" {{  ($user_record_en['gender'] == 'Female') ? "selected=selected" : ''}}>Female</option>
                                        </select>
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Speciality <span style="color:red;"><strong>*</strong></span></label>
                                        <select name="speciality_id"  class="form-control speciality_id" required>
                                            <option value="">Select Speciality</option>
                                            <?php
                                                if(count($specialities) > 0){
                                                foreach ($specialities as $key=>$sp){
                                                    if(isset($user_record_en) && !empty($user_record_en)){
                                                        $selected = ($sp->id == $user_record_en['speciality_id']) ? "selected=selected" : '';
                                                    } else {
                                                        $selected = '';
                                                    }   
                                                    
            
                                            ?>
                                                <option value="{{ $sp->id }}" {{ $selected }}>{{ $sp->name }}</option>
            
                                            <?php }} ?>
                                        </select>
                                        
                                    </div>
    
                                    <!--<div class="col-md-4">-->
                                    <!--    <label class="input-label qcont" for="qualification">Qualification <span style="color:red;"><strong>*</strong></span></label>-->
                                    <!--    <select name="qualification"  class="form-control qualification" required>-->
                                    <!--        <option value="">Select Qualification</option>                               -->
                                    <!--        <option value="MBBS" {{  ($user_record_en['qualification'] == 'MBBS') ? "selected=selected" : ''}}>MBBS</option>-->
                                    <!--        <option value="MD" {{  ($user_record_en['qualification'] == 'MD') ? "selected=selected" : ''}}>MD</option>                             -->
                                    <!--    </select>                                    -->
                                    <!--</div>-->
                  <div class="col-md-4">
    <label class="input-label qcont" for="qualification">Degree  
        <span style="color:red;"><strong>*</strong></span>
    </label>
    <select name="qualification" class="form-control qualification" required>
        <option value="">Select Degree</option>
        @foreach ($degrees as $degree)
            <option value="{{ $degree->id }}" 
                {{ isset($user_record_en) && $user_record_en->qualification == $degree->id ? 'selected' : '' }}>
                {{ $degree->name }}
            </option>
        @endforeach
    </select>
</div>

                                </div>
                            </div> 
                            <br>
                            <div class="form-group">
                                <div class="row">                            
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="experience">Total Experience (in years) <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="number" name="experience" value="{{$user_record_en['experience']}}" class="form-control" id="experience" placeholder="Experience" step="0.01" min="0">
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="state">State <span style="color:red;"><strong>*</strong></span></label>
                                        <select name="state" class="form-control state">
                                            <option value="">Select State</option>
                                            <option value="arunachal_pradesh" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'arunachal_pradesh') ? "selected=selected" : '' }}>Arunachal Pradesh</option>
                                            <option value="assam" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'assam') ? "selected=selected" : '' }}>Assam</option>
                                            <option value="bihar" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'bihar') ? "selected=selected" : '' }}>Bihar</option>
                                            <option value="chhattisgarh" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'chhattisgarh') ? "selected=selected" : '' }}>Chhattisgarh</option>
                                            <option value="delhi" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'delhi') ? "selected=selected" : '' }}>Delhi</option>
                                            <option value="goa" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'goa') ? "selected=selected" : '' }}>Goa</option>
                                            <option value="gujarat" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'gujarat') ? "selected=selected" : '' }}>Gujarat</option>
                                            <option value="haryana" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'haryana') ? "selected=selected" : '' }}>Haryana</option>
                                            <option value="himachal_pradesh" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'himachal_pradesh') ? "selected=selected" : '' }}>Himachal Pradesh</option>
                                            <option value="jammu_and_kashmir" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'jammu_and_kashmir') ? "selected=selected" : '' }}>Jammu and Kashmir</option>
                                            <option value="jharkhand" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'jharkhand') ? "selected=selected" : '' }}>Jharkhand</option>
                                            <option value="karnataka" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'karnataka') ? "selected=selected" : '' }}>Karnataka</option>
                                            <option value="kerala" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'kerala') ? "selected=selected" : '' }}>Kerala</option>
                                            <option value="madhya_pradesh" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'madhya_pradesh') ? "selected=selected" : '' }}>Madhya Pradesh</option>
                                            <option value="maharashtra" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'maharashtra') ? "selected=selected" : '' }}>Maharashtra</option>
                                            <option value="manipur" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'manipur') ? "selected=selected" : '' }}>Manipur</option>
                                            <option value="meghalaya" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'meghalaya') ? "selected=selected" : '' }}>Meghalaya</option>
                                            <option value="mizoram" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'mizoram') ? "selected=selected" : '' }}>Mizoram</option>
                                            <option value="nagaland" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'nagaland') ? "selected=selected" : '' }}>Nagaland</option>
                                            <option value="odisha" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'odisha') ? "selected=selected" : '' }}>Odisha</option>
                                            <option value="punjab" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'punjab') ? "selected=selected" : '' }}>Punjab</option>
                                            <option value="rajasthan" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'rajasthan') ? "selected=selected" : '' }}>Rajasthan</option>
                                            <option value="andhra_pradesh" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'andhra_pradesh') ? "selected=selected" : '' }}>Andhra Pradesh</option>
                                            <option value="sikkim" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'sikkim') ? "selected=selected" : '' }}>Sikkim</option>
                                            <option value="tamil_nadu" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'tamil_nadu') ? "selected=selected" : '' }}>Tamil Nadu</option>
                                            <option value="telangana" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'telangana') ? "selected=selected" : '' }}>Telangana</option>
                                            <option value="tripura" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'tripura') ? "selected=selected" : '' }}>Tripura</option>
                                            <option value="uttar_pradesh" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'uttar_pradesh') ? "selected=selected" : '' }}>Uttar Pradesh</option>
                                            <option value="west_bengal" {{ (!empty($user_record_en['state']) && $user_record_en['state'] == 'west_bengal') ? "selected=selected" : '' }}>West Bengal</option>
                                        </select>
                                        
                                     </div>
                                     <div class="col-md-4">
                                        <label class="input-label qcont" for="address">City <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="city" placeholder="City" class="form-control" value="{{$user_record_en['city']}}">
                                        
                                    </div>                             
                                 </div>
                            </div>  
                          <br>
                          <br>
                            <div class="form-group">
                                <div class="row">                            
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Address <span style="color:red;"><strong>*</strong></span></label>
                                        <textarea name="address"  class="form-control" id="address" placeholder="Address">{{$user_record_en['address']}}</textarea>
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Date of birth <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="date" name="dob" placeholder="Date of birth" class="form-control" value="{{$user_record_en['date_of_birth']}}" max="{{date('Y-m-d')}}">
                                        
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Upload Profile Picture <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="file" name="profile_pic"   class="form-control profile_pic">
                                        <div class="col-12">
                                            <center>
                                                @php
                                                   $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $user_record_en['image']) : asset('storage/app/public/users/' . $user_record_en['image']);
                
                                                   @endphp
                                                <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                                                     src="{{$imagePath}}" alt=""/>
                                            </center>                                    
                                        </div>                                    
                                    </div>  
                                   
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="consultation_fee">Consultation Fee <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="number" name="consultation_fee" placeholder="Consultation Fee" class="form-control" min="0" value="{{$user_record_en['consultation_fee']}}" required>
                                        
                                    </div>

                                    <div class="col-md-4">
                                        <label class="input-label" for="hospital_clinic_name">Hospital / Clinic name  <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="hospital_clinic_name" placeholder="Hospital / Clinic name" class="form-control"  value="{{$user_record_en['hospital_clinic_name']}}"   required>
                                     </div>
          
          
                                     <div class="col-md-4">
                                        <label class="input-label" for="hospital_clinic_address">Hospital / Clinic address  <span style="color:red;"><strong>*</strong></span></label>
                                        <textarea name="hospital_clinic_address" placeholder="Hospital / Clinic address" class="form-control" required>{{$user_record_en['hospital_clinic_address']}}</textarea>
                                     </div>
                                  </div>
                                    
                                    
                                </div>
                            </div> 
                            
                            
                          
                            <div class="form-group">
                               <div class="row">                         
                                  
                                  
                            </div>

                        </div>

                        <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="name"> {{__('messages.name')}} <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="name" class="form-control" id="name"
                                               placeholder="Name" value="{{!empty($user_record_fr) ?? $user_record_fr['name'] ?? ''}}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="phmobile_numberone">Mobile Number <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="mobile_number" value="{{!empty($user_record_fr) ?? $user_record_fr['mobile_number'] ?? ''}}" class="form-control" id="mobile_number"
                                               placeholder="Ex : +91017********" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="email">{{__('messages.email')}} <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="email" name="email" value="{{!empty($user_record_fr) ?? $user_record_fr['email'] ?? ''}}" class="form-control" id="email" placeholder="Ex : ex@gmail.com"  autocomplete="off">
                                    </div>                                
                                </div>
                            </div>                      
                             <br>
                            <div class="form-group">
                                <div class="row">                            
                                    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="gender">Gender <span style="color:red;"><strong>*</strong></span></label>
                                        <select name="gender" class="form-control gender" id="gender" required>
                                            <option value="">Select Gender</option>
                                            <<option value="Male" {{  (!empty($user_record_fr['gender']) && $user_record_fr['gender'] == 'Male') ? "selected=selected" : ''}}>Male</option>
                                            <option value="Female" {{  (!empty($user_record_fr['gender']) && $user_record_fr['gender'] == 'Female') ? "selected=selected" : ''}}>Female</option>
                                            
                                        </select>
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Speciality <span style="color:red;"><strong>*</strong></span></label>
                                        <select name="speciality_id"  class="form-control speciality_id" required>
                                            <option value="">Select Speciality</option>
                                            <?php



                                            if(count($french_specialities) > 0){
                                                foreach ($french_specialities as $key=>$sp){
                                                    $selected = (!empty($user_record_fr['speciality_id']) && $sp->id == $user_record_fr['speciality_id']) ? "selected=selected" : '';
                                            ?>
                                                    <option value="{{ $sp->id }}" {{ $selected }}>{{ $sp->name }}</option>
                                            <?php 
                                                }
                                            }
                                            ?>

                                        </select>
                                        
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="qualification">Qualification <span style="color:red;"><strong>*</strong></span></label>
                                        <select name="qualification"  class="form-control qualification" required>
                                            <option value="">Select Qualification</option>                               
                                            <option value="MBBS" {{ (!empty($user_record_fr['qualification']) && $user_record_fr['qualification'] == 'MBBS') ? "selected=selected" : '' }}>MBBS</option>
                                            <option value="MD" {{ (!empty($user_record_fr['qualification']) && $user_record_fr['qualification'] == 'MD') ? "selected=selected" : '' }}>MD</option>                          
                                        </select>                                    
                                    </div>
                                </div>
                            </div> 
                            <br>
                            <div class="form-group">
                                <div class="row">                            
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="experience">Total Experience (in years) <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="number" name="experience" value="{{!empty($user_record_fr) ?? $user_record_fr['experience'] ?? ''}}" class="form-control" id="experience" placeholder="Experience">
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="state">State <span style="color:red;"><strong>*</strong></span></label>
                                        <select name="state" class="form-control state">
                                            <option value="">Select State</option>
                                            <option value="arunachal_pradesh" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'arunachal_pradesh') ? "selected=selected" : '' }}>Arunachal Pradesh</option>
                                            <option value="assam" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'assam') ? "selected=selected" : '' }}>Assam</option>
                                            <option value="bihar" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'bihar') ? "selected=selected" : '' }}>Bihar</option>
                                            <option value="chhattisgarh" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'chhattisgarh') ? "selected=selected" : '' }}>Chhattisgarh</option>
                                            <option value="delhi" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'delhi') ? "selected=selected" : '' }}>Delhi</option>
                                            <option value="goa" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'goa') ? "selected=selected" : '' }}>Goa</option>
                                            <option value="gujarat" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'gujarat') ? "selected=selected" : '' }}>Gujarat</option>
                                            <option value="haryana" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'haryana') ? "selected=selected" : '' }}>Haryana</option>
                                            <option value="himachal_pradesh" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'himachal_pradesh') ? "selected=selected" : '' }}>Himachal Pradesh</option>
                                            <option value="jammu_and_kashmir" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'jammu_and_kashmir') ? "selected=selected" : '' }}>Jammu and Kashmir</option>
                                            <option value="jharkhand" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'jharkhand') ? "selected=selected" : '' }}>Jharkhand</option>
                                            <option value="karnataka" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'karnataka') ? "selected=selected" : '' }}>Karnataka</option>
                                            <option value="kerala" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'kerala') ? "selected=selected" : '' }}>Kerala</option>
                                            <option value="madhya_pradesh" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'madhya_pradesh') ? "selected=selected" : '' }}>Madhya Pradesh</option>
                                            <option value="maharashtra" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'maharashtra') ? "selected=selected" : '' }}>Maharashtra</option>
                                            <option value="manipur" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'manipur') ? "selected=selected" : '' }}>Manipur</option>
                                            <option value="meghalaya" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'meghalaya') ? "selected=selected" : '' }}>Meghalaya</option>
                                            <option value="mizoram" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'mizoram') ? "selected=selected" : '' }}>Mizoram</option>
                                            <option value="nagaland" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'nagaland') ? "selected=selected" : '' }}>Nagaland</option>
                                            <option value="odisha" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'odisha') ? "selected=selected" : '' }}>Odisha</option>
                                            <option value="punjab" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'punjab') ? "selected=selected" : '' }}>Punjab</option>
                                            <option value="rajasthan" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'rajasthan') ? "selected=selected" : '' }}>Rajasthan</option>
                                            <option value="andhra_pradesh" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'andhra_pradesh') ? "selected=selected" : '' }}>Andhra Pradesh</option>
                                            <option value="sikkim" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'sikkim') ? "selected=selected" : '' }}>Sikkim</option>
                                            <option value="tamil_nadu" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'tamil_nadu') ? "selected=selected" : '' }}>Tamil Nadu</option>
                                            <option value="telangana" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'telangana') ? "selected=selected" : '' }}>Telangana</option>
                                            <option value="tripura" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'tripura') ? "selected=selected" : '' }}>Tripura</option>
                                            <option value="uttar_pradesh" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'uttar_pradesh') ? "selected=selected" : '' }}>Uttar Pradesh</option>
                                            <option value="west_bengal" {{ (!empty($user_record_fr['state']) && $user_record_fr['state'] == 'west_bengal') ? "selected=selected" : '' }}>West Bengal</option>
                                        </select>
                                        
                                        
                                   </div>
                                     <div class="col-md-4">
                                        <label class="input-label qcont" for="address">City <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="city" placeholder="City" class="form-control" value="{{!empty($user_record_fr) ?? $user_record_fr['city'] ?? ''}}">
                                        
                                    </div>                             
                                 </div>
                            </div>  
                         
                            <div class="form-group">
                                <div class="row">                            
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Address <span style="color:red;"><strong>*</strong></span></label>
                                        <textarea name="address"  class="form-control" id="address" placeholder="Address">{{!empty($user_record_fr) ?? $user_record_fr['address'] ?? ''}}</textarea>
                                    </div>
    
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Date of birth <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="date" name="dob" placeholder="Date of birth" class="form-control" value="{{!empty($user_record_fr) ?? $user_record_fr['date_of_birth'] ?? ''}}" max="{{date('Y-m-d')}}">
                                        
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="address">Upload Profile Picture <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="file" name="profile_pic"  class="form-control profile_pic" >
                                        <div class="col-12">
                                            <center>
                                                <?php
                                                   $imagePath = '';
                                                  if(!empty($user_record_fr['image'])){
                                                   $imagePath = (env('APP_ENV') == 'local') ? asset('storage/users/' . $user_record_fr['image']) : asset('storage/app/public/users/' . $user_record_fr['image']);
                                                  }
                                                 
                                                   ?>

                                                <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                                                     src="{{$imagePath}}" alt=""/>
                                            </center>                                    
                                        </div>                                    
                                    </div>  
                              
                                    <br>
                                    <br>
                                    <div class="col-md-4">
                                        <label class="input-label qcont" for="consultation_fee">Consultation Fee <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="number" name="consultation_fee" placeholder="Consultation Fee" class="form-control" min="0" value="{{!empty($user_record_fr) ??$user_record_fr['consultation_fee'] ?? ''}}" required>
                                        
                                    </div>
                                    
                                   
                                    <div class="col-md-4">
                                        <label class="input-label" for="hospital_clinic_name">Hospital / Clinic name  <span style="color:red;"><strong>*</strong></span></label>
                                        <input type="text" name="hospital_clinic_name" placeholder="Hospital / Clinic name" class="form-control" value="{{!empty($user_record_fr) ??$user_record_fr['hospital_clinic_name']}}" required>
                                     </div>
          
          
                                     <div class="col-md-4">
                                        <label class="input-label" for="hospital_clinic_address">Hospital / Clinic address  <span style="color:red;"><strong>*</strong></span></label>
                                        <textarea name="hospital_clinic_address" placeholder="Hospital / Clinic address" class="form-control" required> {{!empty($user_record_fr) ?? $user_record_fr['hospital_clinic_address']}}</textarea>
                                     </div>
                                </div>
                            </div>  


                            
                      
                        </div>
                        
                        <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".profile_pic").change(function () {
            readURL(this);
        });

        
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
        $(".state").select2();
        $(".speciality_id").select2();
        $(".qualification").select2();
        $(".gender").select2();
        $("#location_id").select2();
        //$("#en-form").find('input, select,textarea').attr('required', 'required');
      //  $("#fr-form").find('input, select,textarea').removeAttr('required');

        $("#en-form").find('input, select,textarea').each(function() {
            var currentName = $(this).attr('name');
            if (currentName !== undefined) {
                $(this).attr('name', currentName + '_en');
            }
        });

        $("#fr-form").find('input, select,textarea').each(function() {
            var currentName = $(this).attr('name');
            if (currentName !== undefined) {
                $(this).attr('name', currentName + '_fr');
            }
        });

        var lang = "<?=$user_record->lang_name?>";
       if (lang == 'en') {
    $("#en-form").find('input, select').not('input[type="file"]').attr('required', 'required');
    $("#fr-form").find('input, select').not('input[type="file"]').removeAttr('required');
} else {
    $("#fr-form").find('input, select').not('input[type="file"]').attr('required', 'required');
    $("#en-form").find('input, select').not('input[type="file"]').removeAttr('required');
}


    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');
        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        $("#"+lang+"-form").removeClass('d-none');
        $("#lang_name").val(lang);

        if(lang == 'en')
        {
            $("#en-form").find('input, select,textarea').attr('required', 'required');
           $("#fr-form").find('input, select,textarea').removeAttr('required');
        }
        else
        {
            
            $("#fr-form").find('input, select,textarea').attr('required', 'required');
           $("#en-form").find('input, select,textarea').removeAttr('required');
        }
    });
    </script>
@endpush
