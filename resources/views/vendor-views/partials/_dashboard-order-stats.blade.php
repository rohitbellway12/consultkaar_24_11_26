
<div class="col-sm-3 col-lg-4 mb-3 mb-lg-5">
    <!-- Card -->
    <a class="card card-hover-shadow h-100 card-hover-effect"  href="{{url('vendor/product-category/list')}}"
       style="background: #334257">
        <div class="card-body">
            <h6 class="card-subtitle"
                style="color: white!important;">Total Categories</h6>

            <div class="row align-items-center gx-2 mb-1">
                <div class="col-6">
                    <span class="card-title h2" style="color: white!important;">
                {{ $data['total_categories'] }}
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

<div class="col-sm-3 col-lg-4 mb-3 mb-lg-5">
    <!-- Card -->
    <a class="card card-hover-shadow h-100 card-hover-effect" href="{{route('vendor.order.vendorelist',['all'])}}"
       style="background: #54436B">
        <div class="card-body">
            <h6 class="card-subtitle"
                style="color: white!important;">Total Medicine Bookings</h6>
            <div class="row align-items-center gx-2 mb-1">
                <div class="col-6">
                            <span class="card-title h2" style="color: white!important;">
                                     {{ $data['total_orders'] }}
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

<div class="col-sm-3 col-lg-4 mb-3 mb-lg-5">
    <!-- Card -->
    <a class="card card-hover-shadow h-100 card-hover-effect" href="{{route('vendor.medicion.list')}}" 
       style="background: #402218">
        <div class="card-body">
            <h6 class="card-subtitle"
                style="color: white!important;">Total Medicine</h6>

            <div class="row align-items-center gx-2 mb-1">
                <div class="col-6">
                        <span class="card-title h2" style="color: white!important;">
                    {{ $data['total_products'] }}
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

<!--<div class="col-sm-3 col-lg-3 mb-3 mb-lg-5">-->
    <!-- Card -->
<!--    <a class="card card-hover-shadow h-100 card-hover-effect" href="{{route('admin.doctor.list')}}"-->
<!--       style="background: #334257">-->
<!--        <div class="card-body">-->
<!--            <h6 class="card-subtitle"-->
<!--                style="color: white!important;">Total Doctors</h6>-->

<!--            <div class="row align-items-center gx-2 mb-1">-->
<!--                <div class="col-6">-->
<!--                    <span class="card-title h2" style="color: white!important;">-->
                 
<!--                    </span>-->
<!--                </div>-->

<!--                <div class="col-6 mt-2">-->
<!--                    <i class="tio-man" style="font-size: 30px;color: white"></i>-->
                  
<!--                </div>-->
<!--            </div>-->
            <!-- End Row -->
<!--        </div>-->
<!--    </a>-->
    <!-- End Card -->
<!--</div>-->



<div class="col-12">
    <div class="card card-body" style="background: #FEF7DC!important;">
        <div class="row gx-lg-4">
            <div class="col-sm-6 col-lg-3">
                <div class="media" style="cursor: pointer"
                     onclick="location.href='{{route('admin.order.list',['delivered'])}}'">
                    <div class="media-body">
                        <h6 class="card-subtitle">Delivered</h6>
                        <span class="card-title h3">
                                            
                                            </span>
                    </div>
                    <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                          <i class="tio-checkmark-circle-outlined"></i>
                                        </span>
                </div>
                <div class="d-lg-none">
                    <hr>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 column-divider-sm">
                <div class="media" style="cursor: pointer"
                     onclick="location.href='{{route('admin.order.list',['cancelled'])}}'">
                    <div class="media-body">
                        <h6 class="card-subtitle">Cancelled</h6>
                        <span
                            class="card-title h3"></span>
                    </div>
                    <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                          <i class="tio-remove-from-trash"></i>
                                        </span>
                </div>
                <div class="d-lg-none">
                    <hr>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 column-divider-lg">
                <div class="media" style="cursor: pointer"
                     onclick="location.href='{{route('admin.order.list',['failed'])}}'">
                    <div class="media-body">
                        <h6 class="card-subtitle">{{__('messages.payment')}} {{__('messages.failed')}}</h6>
                        <span
                            class="card-title h3"></span>
                    </div>
                    <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                          <i class="tio-hand-wave"></i>
                                        </span>
                </div>
                <div class="d-lg-none">
                    <hr>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 column-divider-sm">
                <div class="media" style="cursor: pointer"
                     onclick="location.href='{{route('admin.order.list',['refunded'])}}'">
                    <div class="media-body">
                        <h6 class="card-subtitle">{{__('messages.refunded')}}</h6>
                        <span
                            class="card-title h3"></span>
                    </div>
                    <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                          <i class="tio-history"></i>
                                        </span>
                </div>
                <div class="d-lg-none">
                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>
