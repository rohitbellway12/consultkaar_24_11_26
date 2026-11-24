@extends('layouts.doctor.app')
@section('title','Manage Appointment Slots')
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
           @media(max-width:375px){
            #customer-image-modal .modal-content{
              width: 367px !important;
            margin-left: 0 !important;
        }
       
        }

   @media(max-width:500px){
    #customer-image-modal .modal-content{
              width: 400px !important;
            margin-left: 0 !important;
        }
      
      
   }
    </style>
@endpush

@section('content')
<div class="content container-fluid"> 

  <!-- Page Header -->
  <div class="page-header">
    <div class="row align-items-center">
       <div class="col-sm mb-2 mb-sm-0">
          <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} Consultantion Slot</h1>
       </div>
    </div>
 </div>
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
          
            <div class="card">
                <div class="card-body">
         
                    <form action="{{url('doctor/add-slots')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="input-label qcont" for="day_name"> Day Name</label>
                                    <select class="form-control js-select2-custom" name="day_name" required>
                                        <option value="">Select day name</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                     </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="input-label qcont" for="start_time">Start Time</label>
                                    <input type="time" name="start_time" value="{{old('start_time')}}" class="form-control" id="start_time" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="input-label qcont" for="end_time">End Time</label>
                                    <input type="time" name="end_time" value="{{old('end_time')}}" class="form-control" id="end_time" required>
                                </div>    
                                <div class="col-md-3">
                                    <label class="input-label qcont" for="patient_limit">Customer Limit</label>
                                    <input type="text" name="patient_limit" value="{{old('patient_limit')}}" class="form-control" id="patient_limit" required>
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

        $("#customFileUpload").change(function () {
            readURL(this);
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>
@endpush
