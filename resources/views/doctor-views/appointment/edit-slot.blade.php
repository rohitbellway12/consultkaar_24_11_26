@extends('layouts.doctor.app')
@section('title','Edit Slot')
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

    <div class="page-header">
        <div class="row align-items-center">
           <div class="col-sm mb-2 mb-sm-0">
              <h1 class="page-header-title"><i class="tio-edit"></i> Update Appointment Slot</h1>
           </div>
        </div>
     </div>
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
          

            <div class="card">
                <div class="card-body">
                    <form action="{{url('doctor/update-slots')}}" method="post">
                        @csrf
                        <input type="hidden" id="slot_id" name="id" value="{{$slot_record->id}}">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="input-label qcont" for="day_name"> Day Name</label>
                                    <select class="form-control js-select2-custom" name="day_name" required>
                                        <option value="">Select day name</option>
                                            <option value="">Select day name</option>
                                            <?php
                                            $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            foreach ($daysOfWeek as $day) {
                                                $selected = ($slot_record->day_name == $day) ? 'selected' : '';
                                                echo "<option value=\"$day\" $selected>$day</option>";
                                            }
                                            ?>
                                        </select>
                                </div>
                                <div class="col-md-3">                                   
                                    <label class="input-label qcont" for="start_time">Start Time</label>
                                    <input type="time" name="start_time" value="{{date("H:i",strtotime($slot_record->start_time))}}" class="form-control" id="start_time" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="input-label qcont" for="end_time">End Time</label>
                                    <input type="time" name="end_time" value="{{date("H:i",strtotime($slot_record->end_time))}}" class="form-control" id="end_time" required>
                                </div>     
                                <div class="col-md-3">
                                    <label class="input-label qcont" for="patient_limit">Patient Limit</label>
                                    <input type="text" name="patient_limit" value="{{($slot_record->patient_limit)}}" class="form-control" id="patient_limit" required>
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
