@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
  
@endphp
<style>
    .nav-sub{
        background: #019842 !important;
    }
</style>

<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-brand-wrapper justify-content-between">
                <!-- Logo -->
                @php(@$restaurant_logo=\App\Models\BusinessSetting::where(['key'=>'logo'])->first()->value)
                
                <?php
                
                     $logoPath = (env('APP_ENV') == 'local') ? asset('storage/business/' . $restaurant_logo) : asset('storage/app/public/business/' . $restaurant_logo);        
                ?>
                <a class="navbar-brand" href="{{route('doctor.dashboard')}}" aria-label="Front">
                    <img class="navbar-brand-logo" style="max-height: 55px; max-width: 100%!important;"
                         onerror="this.src='{{asset($assetPrefixPath . '/assets/doctor/img/160x160/logo2.png')}}'"
                         src="{{$logoPath}}"
                         alt="Logo">
                    <img class="navbar-brand-logo-mini" style="max-height: 55px; max-width: 100%!important;"
                         onerror="this.src='{{asset($assetPrefixPath . '/assets/doctor/img/160x160/logo2.png')}}'"
                         src="{{$logoPath}}" alt="Logo">
                </a>

                <!-- End Logo -->

                <!-- Navbar Vertical Toggle -->
                <button type="button"
                        class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                    <i class="tio-clear tio-lg"></i>
                </button>
                <!-- End Navbar Vertical Toggle -->
            </div>

            <!-- Content -->
            <div class="navbar-vertical-content" style="background-color: #2a56a7;
            ">
                <ul class="navbar-nav navbar-nav-lg nav-tabs">
                    <!-- Dashboards -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('doctor')?'show':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                           href="{{route('doctor.dashboard')}}" title="{{__('messages.dashboard')}}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{__('messages.dashboard')}}  
                            </span>
                        </a>
                    </li>
                    <!-- End Dashboards -->
                    
                    <!-- user -->

                    <li class="nav-item">
                        <!--<small class="nav-subtitle">{{__('messages.user')}} {{__('section')}}</small>-->
                         <small
                        class="nav-subtitle">Customers {{__('messages.section')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

          

                    <li class="navbar-vertical-aside-has-menu {{Request::is('doctor/patient*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                           href="javascript:"
                           title="Patients">
                            <i class="tio-user nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Customers</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('doctor/patient*')?'block':'none'}}">
                          
                            
                            <li class="nav-item {{Request::is('doctor/patient/list')?'active':''}}">
                                <a class="nav-link " href="{{route('doctor.patient.list')}}"
                                   title="Patient {{__('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{__('messages.list')}}</span>
                                </a>
                            </li>

                          
                        </ul>
                    </li>
              


                <li class="nav-item">
                    <small
                        class="nav-subtitle"> Consultant  {{__('messages.section')}}</small>
                    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                </li>

          

                <li class="navbar-vertical-aside-has-menu {{Request::is('doctor/appointment-list/*')?'active':''}}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                       href="javascript:" title="Appointments">
                        {{-- <i class="tio-shopping-cart nav-icon"></i> --}}
                        <img src="{{asset($assetPrefixPath . '/assets/doctor/img/booking.png')}}" style="width: 20px; height: auto;filter: invert(100%);" /> &nbsp; &nbsp;
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                           Consultantion
                        </span>
                    </a>
                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                        style="display: {{Request::is('doctor/appointment-list/*')?'block':'none'}}">
                        <li class="nav-item {{Request::is('doctor/appointment-list/pending') ? 'active':''}}">
                            <a class="nav-link " href="{{url('doctor/appointment-list/pending')}}"
                               title="Pending Appointments">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">
                                    Pending
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{\App\Models\Appointment::Pending()->count()}}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item {{Request::is('doctor/appointment-list/confirmed')?'active':''}}">
                            <a class="nav-link "href="{{url('doctor/appointment-list/confirmed')}}"
                               title="Confirmed Appointments">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">
                                    Confirmed
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{\App\Models\Appointment::Confirmed()->count()}}

                                    </span>
                                </span>
                            </a>
                        </li>
                         <li class="nav-item {{Request::is('doctor/appointment-list/rejected')?'active':''}}">
                            <a class="nav-link "href="{{url('doctor/appointment-list/rejected')}}"
                               title="Completed Appointments">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">
                                    Rejected
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{\App\Models\Appointment::Rejected()->count()}}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item {{Request::is('doctor/appointment-list/cancelled')?'active':''}}">
                            <a class="nav-link "href="{{url('doctor/appointment-list/cancelled')}}"
                               title="Completed Appointments">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">
                                    Cancelled
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{\App\Models\Appointment::Cancelled()->count()}}
                                    </span>
                                </span>
                            </a>
                        </li>

                        <li class="nav-item {{Request::is('doctor/appointment-list/completed')?'active':''}}">
                            <a class="nav-link "href="{{url('doctor/appointment-list/completed')}}"
                               title="Completed Appointments">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">
                                    Completed
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{\App\Models\Appointment::Completed()->count()}}
                                    </span>
                                </span>
                            </a>
                        </li>


                        <li class="nav-item {{Request::is('doctor/appointment-list/all')?'active':''}}">
                            <a class="nav-link " href="{{url('doctor/appointment-list/all')}}"
                               title="All {{__('messages.orders')}}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">
                                    All
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                      {{\App\Models\Appointment::AllAppointments()->count()}}
                                    </span>
                                </span>
                            </a>
                        </li>
                      
                    </ul>
                </li>

                <li class="navbar-vertical-aside-has-menu {{Request::is('doctor/user*')?'active':''}}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                       href="javascript:"
                       title="User">
                        <i class="tio-user nav-icon"></i>
                        <span
                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Consultantion  Slots</span>
                    </a>
                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                        style="display: {{(Request::is('doctor/add-slots') || Request::is('doctor/slot-list') )?'block':'none'}}">
                        <li class="nav-item {{Request::is('doctor/add-slots')?'active':''}}">
                            <a class="nav-link " href="{{url('doctor/add-slots')}}"
                               title="Add appointment slot">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{__('messages.add')}}</span>
                            </a>
                        </li>
                        
                        <li class="nav-item {{Request::is('doctor/slot-list')?'active':''}}">
                            <a class="nav-link " href="{{url('doctor/slot-list')}}"
                               title="Appointment slot {{__('messages.list')}}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{__('messages.list')}}</span>
                            </a>
                        </li>
                    
                      
                    </ul>
                </li>
    
                
                
                
                
                
                
                
                <li class="navbar-vertical-aside-has-menu {{Request::is('doctor/user*')?'active':''}}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                       href="javascript:"
                       title="User">
                        <i class="tio-user nav-icon"></i>
                        <span
                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Wallet</span>
                    </a>
                    <!--<ul class="js-navbar-vertical-aside-submenu nav nav-sub"-->
                    <!--    style="display: {{(Request::is('doctor/add-slots') || Request::is('doctor/slot-list') )?'block':'none'}}">-->
                    <!--    <li class="nav-item {{Request::is('doctor/add-slots')?'active':''}}">-->
                    <!--        <a class="nav-link " href="{{url('doctor/add-slots')}}"-->
                    <!--           title="Add appointment slot">-->
                    <!--            <span class="tio-circle nav-indicator-icon"></span>-->
                    <!--            <span class="text-truncate">{{__('messages.add')}}</span>-->
                    <!--        </a>-->
                    <!--    </li>-->
                        
                    <!--    <li class="nav-item {{Request::is('doctor/slot-list')?'active':''}}">-->
                    <!--        <a class="nav-link " href="{{url('doctor/slot-list')}}"-->
                    <!--           title="Appointment slot {{__('messages.list')}}">-->
                    <!--            <span class="tio-circle nav-indicator-icon"></span>-->
                    <!--            <span class="text-truncate">{{__('messages.list')}}</span>-->
                    <!--        </a>-->
                    <!--    </li>-->
                    
                      
                    <!--</ul>-->
                </li>

         

                



             


                    <li class="nav-item" style="padding-top: 100px">

                    </li>
                </ul>
            </div>
            <!-- End Content -->
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>