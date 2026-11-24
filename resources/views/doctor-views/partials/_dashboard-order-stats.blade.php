
<div class="col-sm-4 col-lg-4 mb-3 mb-lg-5">
    <!-- Card -->
    <a class="card card-hover-shadow h-100 card-hover-effect"  href="{{url('doctor/appointment-list/all')}}"
       style="background: #334257">
        <div class="card-body">
            <h6 class="card-subtitle"
                style="color: white!important;">Total Consultations</h6>

            <div class="row align-items-center gx-2 mb-1">
                <div class="col-6">
                    <span class="card-title h2" style="color: white!important;">
                      {{ $total_appointments }}
                    </span>
                </div>

                <div class="col-6 mt-2">
                    <i class="tio-man" style="font-size: 30px;color: white"></i>
                  
                </div>
            </div>
            <!-- End Row -->
        </div>
    </a>
    <!-- End Card -->
</div>



<div class="col-sm-4 col-lg-4 mb-3 mb-lg-5">
    <!-- Card -->
    <a class="card card-hover-shadow h-100 card-hover-effect" href="{{route('doctor.patient.list')}}"
       style="background: #402218">
        <div class="card-body">
            <h6 class="card-subtitle"
                style="color: white!important;">Total Customers</h6>

            <div class="row align-items-center gx-2 mb-1">
                <div class="col-6">
                        <span class="card-title h2" style="color: white!important;">
                            {{ $total_patients  }}
                        </span>
                </div>

                <div class="col-6 mt-2">
                    <i class="tio-man" style="font-size: 30px;color: white"></i>
               
                </div>
            </div>
            <!-- End Row -->
        </div>
    </a>
    <!-- End Card -->
</div>

<div class="col-sm-4 col-lg-4 mb-3 mb-lg-5">
    <!-- Card -->
    <a class="card card-hover-shadow h-100 card-hover-effect"  href="{{url('doctor/appointment-list/all')}}"
       style="background: #334257">
        <div class="card-body">
            <h6 class="card-subtitle"
                style="color: white!important;">Total Video Consultations</h6>

            <div class="row align-items-center gx-2 mb-1">
                <div class="col-6">
                    <span class="card-title h2" style="color: white!important;">
                        {{ $total_video_consultations  }}
                    </span>
                </div>

                <div class="col-6 mt-2">
                    <i class="tio-man" style="font-size: 30px;color: white"></i>
                  
                </div>
            </div>
            <!-- End Row -->
        </div>
    </a>
    <!-- End Card -->
</div>


