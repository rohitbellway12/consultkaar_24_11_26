@extends('layouts.admin.app')
@section('title','Add doctor')
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
   @media(max-width:375px){
   #doctor-image-modal .modal-content{
   width: 367px !important;
   margin-left: 0 !important;
   }
   }
   @media(max-width:500px){
   #doctor-image-modal .modal-content{
   width: 400px !important;
   margin-left: 0 !important;
   }
   }
   .text-uppercase{
        text-transform: uppercase;
   }
</style>
@endpush
@section('content')
<div class="content container-fluid">
<!-- Page Header -->
<div class="page-header">
   <div class="row align-items-center">
      <div class="col-sm mb-2 mb-sm-0">
         <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} Advisor</h1>
      </div>
   </div>
</div>
<!-- End Page Header -->
<!-- Content Row -->
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-body">
            <form action="{{route('admin.doctor.add-new')}}" method="post" enctype="multipart/form-data">
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
                              <label class="input-label qcont" for="name"> {{__('messages.name')}} <span class="text-uppercase"></span> <span style="color:red;"><strong>*</strong></span></label>
                              <input type="text" name="name" class="form-control" id="name"
                                 placeholder="Name" value="{{old('name')}}" required>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="phmobile_numberone">Mobile Number  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="text" name="mobile_number" value="{{old('mobile_number')}}" class="form-control" id="mobile_number"
                                 placeholder="Ex : +91017********" required>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="email">{{__('messages.email')}}  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="email" name="email" value="{{old('email')}}" class="form-control" id="email" placeholder="Ex : ex@gmail.com"  autocomplete="off">
                           </div>
                        </div>
                     </div>
                     <br>
                     <div class="form-group">
                        <div class="row">
                          
                        
                           <div class="col-md-4">
                              <label class="input-label qcont" for="gender">Gender  <span style="color:red;"><strong>*</strong></span></label>
                              <select name="gender" class="form-control gender js-select2-custom" id="gender" required>
                                 <option value="">Select Gender</option>
                                 <option value="Male">Male</option>
                                 <option value="Female">Female</option>
                              </select>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">Specialization<span style="color:red;"><strong>*</strong></span></label>
                              <select name="speciality_id"  class="form-control speciality_id" required>
                                 <option value="">Select Expert</option>
                                 <?php
                                    if(count($specialities) > 0){
                                    foreach ($specialities as $key=>$sp){
                                    
                                    ?>
                                 <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                 <?php }} ?>
                              </select>
                           </div>
                           <!--<div class="col-md-4">-->
                           <!--   <label class="input-label qcont" for="address">Degree  <span style="color:red;"><strong>*</strong></span></label>-->
                           <!--   <select name="qualification"  class="form-control qualification" required>-->
                           <!--      <option value="">Select Degree</option>-->
                           <!--      <option value="MBBS">MBBS</option>-->
                           <!--      <option value="MD">MD</option>-->
                           <!--   </select>-->
                           <!--</div>-->
                            <div class="col-md-4">
    <label class="input-label qcont" for="qualification">Degree  <span style="color:red;"><strong>*</strong></span></label>
    <select name="qualification" class="form-control qualification" required>
        <option value="">Select Degree</option>
        @foreach ($degrees as $degree)
            <option value="{{ $degree->id }}">{{ $degree->name }}</option>
        @endforeach
    </select>
</div>
                        </div>
                     </div>
                     <br>
                     <div class="form-group">
                        <div class="row">
                           
                           <div class="col-md-4">
                              <label class="input-label qcont" for="experience">Total Experience (in years)  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="number" name="experience" value="{{old('experience')}}" class="form-control"  placeholder="Experience" step="0.01" min="0">
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="state">State  <span style="color:red;"><strong>*</strong></span></label>
                              <select name="state" class="form-control state ">
                                 <option selected="selected" value="">Select State:</option>
                                 <option value="arunachal_pradesh">Arunachal Pradesh</option>
                                 <option value="assam">Assam</option>
                                 <option value="bihar">Bihar</option>
                                 <option value="chhattisgarh">Chhattisgarh</option>
                                 <option value="delhi">Delhi</option>
                                 <option value="goa">Goa</option>
                                 <option value="gujarat">Gujarat</option>
                                 <option value="haryana">Haryana</option>
                                 <option value="himachal_pradesh">Himachal Pradesh</option>
                                 <option value="jammu_and_kashmir">Jammu and Kashmir</option>
                                 <option value="jharkhand">Jharkhand</option>
                                 <option value="karnataka">Karnataka</option>
                                 <option value="kerala">Kerala</option>
                                 <option value="madhya_pradesh">Madhya Pradesh</option>
                                 <option value="maharashtra">Maharashtra</option>
                                 <option value="manipur">Manipur</option>
                                 <option value="meghalaya">Meghalaya</option>
                                 <option value="mizoram">Mizoram</option>
                                 <option value="nagaland">Nagaland</option>
                                 <option value="odisha">Odisha</option>
                                 <option value="punjab">Punjab</option>
                                 <option value="rajasthan">Rajasthan</option>
                                 <option value="andhra_pradesh">Andhra Pradesh</option>
                                 <option value="sikkim">Sikkim</option>
                                 <option value="tamil_nadu">Tamil Nadu</option>
                                 <option value="telangana">Telangana</option>
                                 <option value="tripura">Tripura</option>
                                 <option value="uttar_pradesh">Uttar Pradesh</option>
                                 <option value="west_bengal">West Bengal</option>
                              </select>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">City  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="text" name="city" placeholder="City" class="form-control">
                           </div>
                        </div>
                     </div>
                     <br>
                     <div class="form-group">
                        <div class="row">
                          
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">Address <span style="color:red;"><strong>*</strong></span></label>
                              <textarea name="address" value="{{old('address')}}" class="form-control" id="address" placeholder="Address"></textarea>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="dob">Date of birth  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="date" name="dob" placeholder="Date of birth" class="form-control" max="{{date('Y-m-d')}}">
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">Upload Profile Picture  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="file" name="profile_pic"  class="form-control profile_pic">
                              <div class="col-12">
                                 <center>
                                    <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                                       src="" alt=""/>
                                 </center>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="form-group">
                        <div class="row">
                          
                           <div class="col-md-4">
                              <label class="input-label qcont" for="consultation_fee">Consultation Fee  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="number" name="consultation_fee" placeholder="Consultation Fee" class="form-control" min="0" required>
                           </div>

                           <!--<div class="col-md-4">-->
                           <!--   <label class="input-label" for="hospital_clinic_name">Hospital / Clinic name  <span style="color:red;"><strong>*</strong></span></label>-->
                           <!--   <input type="text" name="hospital_clinic_name" placeholder="Hospital / Clinic name" class="form-control"  >-->
                           <!--</div>-->


                           <!--<div class="col-md-4">-->
                           <!--   <label class="input-label" for="hospital_clinic_address">Hospital / Clinic address  <span style="color:red;"><strong>*</strong></span></label>-->
                           <!--   <textarea name="hospital_clinic_address" placeholder="Hospital / Clinic address" class="form-control" ></textarea>-->
                           <!--</div>-->
                        </div>
                     </div>

                    
                </div>
                
                <div class="card mb-1 p-4 lang_form d-none" id="fr-form">
                     
                     <div class="form-group">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="input-label qcont" for="name"> {{__('messages.name')}}  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="text" name="name" class="form-control" id="name"
                                 placeholder="Name" value="{{old('name')}}" required>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="phmobile_numberone">Mobile Number   <span style="color:red;"><strong>*</strong></span></label>
                              <input type="text" name="mobile_number" value="{{old('mobile_number')}}" class="form-control" id="mobile_number"
                                 placeholder="Ex : +91017********" required>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="email">{{__('messages.email')}}   <span style="color:red;"><strong>*</strong></span></label>
                              <input type="email" name="email" value="{{old('email')}}" class="form-control" id="email" placeholder="Ex : ex@gmail.com"  autocomplete="off">
                           </div>
                        </div>
                     </div>
                     <br>
                     <div class="form-group">
                        <div class="row">
                       
                           <div class="col-md-4">
                              <label class="input-label qcont" for="gender">Gender  <span style="color:red;"><strong>*</strong></span></label>
                              <select name="gender" class="form-control gender js-select2-custom"  required>
                                 <option value="">Select Gender</option>
                                 <option value="Male">Male</option>
                                 <option value="Female">Female</option>
                              </select>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">Speciality  <span style="color:red;"><strong>*</strong></span></label>
                              <select name="speciality_id"  class="form-control speciality_id" required>
                                 <option value="">Select Speciality</option>
                                 <?php
                                    if(count($french_specialities) > 0){
                                    foreach ($french_specialities as $key=>$sp){
                                    
                                    ?>
                                 <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                 <?php }} ?>
                              </select>
                           </div>
                           <div class="col-md-4">
    <label class="input-label qcont" for="qualification">Degree  <span style="color:red;"><strong>*</strong></span></label>
    <select name="qualification" class="form-control qualification" required>
        <option value="">Select Degree</option>
        @foreach ($degrees as $degree)
            <option value="{{ $degree->id }}">{{ $degree->name }}</option>
        @endforeach
    </select>
</div>

                        </div>
                     </div>
                     <br>
                     <div class="form-group">
                        <div class="row">
                          
                           <div class="col-md-4">
                              <label class="input-label qcont" for="experience">Total Experience (in years)   <span style="color:red;"><strong>*</strong></span></label>
                              <input type="number" name="experience" value="{{old('experience')}}" class="form-control"  placeholder="Experience">
                           </div>

                           <div class="col-md-4">
                              <label class="input-label qcont" for="state">State  <span style="color:red;"><strong>*</strong></span></label>
                              <select name="state" class="form-control state">
                                 <option selected="selected" value="">Select State</option>
                                 <option value="arunachal_pradesh">Arunachal Pradesh</option>
                                 <option value="assam">Assam</option>
                                 <option value="bihar">Bihar</option>
                                 <option value="chhattisgarh">Chhattisgarh</option>
                                 <option value="delhi">Delhi</option>
                                 <option value="goa">Goa</option>
                                 <option value="gujarat">Gujarat</option>
                                 <option value="haryana">Haryana</option>
                                 <option value="himachal_pradesh">Himachal Pradesh</option>
                                 <option value="jammu_and_kashmir">Jammu and Kashmir</option>
                                 <option value="jharkhand">Jharkhand</option>
                                 <option value="karnataka">Karnataka</option>
                                 <option value="kerala">Kerala</option>
                                 <option value="madhya_pradesh">Madhya Pradesh</option>
                                 <option value="maharashtra">Maharashtra</option>
                                 <option value="manipur">Manipur</option>
                                 <option value="meghalaya">Meghalaya</option>
                                 <option value="mizoram">Mizoram</option>
                                 <option value="nagaland">Nagaland</option>
                                 <option value="odisha">Odisha</option>
                                 <option value="punjab">Punjab</option>
                                 <option value="rajasthan">Rajasthan</option>
                                 <option value="andhra_pradesh">Andhra Pradesh</option>
                                 <option value="sikkim">Sikkim</option>
                                 <option value="tamil_nadu">Tamil Nadu</option>
                                 <option value="telangana">Telangana</option>
                                 <option value="tripura">Tripura</option>
                                 <option value="uttar_pradesh">Uttar Pradesh</option>
                                 <option value="west_bengal">West Bengal</option>
                              </select>
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">City  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="text" name="city" placeholder="City" class="form-control">
                           </div>
                        </div>
                     </div>
                     <br>
                     <div class="form-group">
                        <div class="row">
                           
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">Address  <span style="color:red;"><strong>*</strong></span></label>
                              <textarea name="address" value="{{old('address')}}" class="form-control" id="address" placeholder="Address"></textarea>
                           </div>

                           <div class="col-md-4">
                              <label class="input-label qcont" for="dob">Date of birth  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="date" name="dob" placeholder="Date of birth" class="form-control" max="{{date('Y-m-d')}}">
                           </div>
                           <div class="col-md-4">
                              <label class="input-label qcont" for="address">Upload Profile Picture  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="file" name="profile_pic"   class="form-control profile_pic">
                              <div class="col-12">
                                 <center>
                                    <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                                       src="" alt=""/>
                                 </center>
                              </div>
                           </div>
                        </div>
                     </div>
                     <br>
                    
                     <div class="form-group">
                        <div class="row">     
                           
                           <div class="col-md-4">
                              <label class="input-label qcont" for="consultation_fee">Consultation Fee  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="number" name="consultation_fee" placeholder="Consultation Fee" class="form-control" min="0" required>
                           </div>
                           
                           <div class="col-md-4">
                              <label class="input-label" for="hospital_clinic_name">Hospital / Clinic name  <span style="color:red;"><strong>*</strong></span></label>
                              <input type="text" name="hospital_clinic_name" placeholder="Hospital / Clinic name" class="form-control" required>
                           </div>


                           <div class="col-md-4">
                              <label class="input-label" for="hospital_clinic_address">Hospital / Clinic address  <span style="color:red;"><strong>*</strong></span></label>
                              <textarea name="hospital_clinic_address" placeholder="Hospital / Clinic address" class="form-control" required></textarea>
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
   $("#en-form").find('input, select,textarea').attr('required', 'required');
   $("#fr-form").find('input, select,textarea').removeAttr('required');
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
            $("#en-form").find('input, select').attr('required', 'required');
            $("#fr-form").find('input, select').removeAttr('required');
        }
        else
        {
            $("#fr-form").find('input, select').attr('required', 'required');
            $("#en-form").find('input, select').removeAttr('required');
        }
    });
   
</script>
@endpush