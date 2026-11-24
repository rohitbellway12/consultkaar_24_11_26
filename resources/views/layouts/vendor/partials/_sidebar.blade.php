@php
  $appEnv = env('APP_ENV');
  $assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
  
@endphp
@php
    $id = Auth::guard('vendor')->id();  // Correctly fetching the vendor ID
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
                    <li class="navbar-vertical-aside-has-menu {{Request::is('vendor')?'show':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                           href="{{route('vendor.dashboard')}}" title="{{__('messages.dashboard')}}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{__('messages.dashboard')}}  
                            </span>
                        </a>
                    </li>
                    <!-- End Dashboards -->
                       <li class="nav-item">
    <small class="nav-subtitle"> Categories </small>
    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
</li>

<!-- Medicion Section -->
<li class="navbar-vertical-aside-has-menu {{ Request::is('vendor/product-category*') ? 'active' : '' }}">
    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
       href="javascript:;"
       title="product-category">
        <i class="tio-user nav-icon"></i>
        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Categories</span>
    </a>

    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
        style="display: {{ Request::is('vendor/product-category*') ? 'block' : 'none' }}">


        <!-- Medicion List Section -->
        <li class="nav-item {{ Request::is('vendor/product-category/list') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('vendor.product-category.list')}}" 
               title="Medicion List">
                <span class="tio-circle nav-indicator-icon"></span>
                <span class="text-truncate">{{ __('messages.list') }}</span>
            </a>
        </li>
    </ul>
</li>
                    <!-- user -->

           <li class="nav-item">
    <small class="nav-subtitle"> Medicine {{ __('section') }}</small>
    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
</li>

<!-- Medicion Section -->
<li class="navbar-vertical-aside-has-menu {{ Request::is('vendor/medicion*') ? 'active' : '' }}">
    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
       href="javascript:;"
       title="Medicion">
        <i class="tio-user nav-icon"></i>
        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Medicine</span>
    </a>

    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
        style="display: {{ Request::is('vendor/medicion*') ? 'block' : 'none' }}">

        <!-- Add Medicion Section -->
        <li class="nav-item {{ Request::is('vendor/medicion/create') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('vendor.medicion.create')}}" 
               title="Add Medicion">
                <span class="tio-circle nav-indicator-icon"></span>
                <span class="text-truncate">{{ __('Add Medicine') }}</span>
            </a>
        </li>

        <!-- Medicion List Section -->
        <li class="nav-item {{ Request::is('vendor/medicion/list') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('vendor.medicion.list')}}" 
               title="Medicion List">
                <span class="tio-circle nav-indicator-icon"></span>
                <span class="text-truncate">{{ __('messages.list') }}</span>
            </a>
        </li>
    </ul>
</li>


              



                        <li class="nav-item">
                            <small
                                class="nav-subtitle"> {{__('messages.booking')}} {{__('messages.section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        
                        <li class="navbar-vertical-aside-has-menu {{Request::is('vendor/booking/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                               href="javascript:" title="{{__('messages.order')}}">
                                {{-- <i class="tio-shopping-cart nav-icon"></i> --}}
                                <img src="{{asset($assetPrefixPath . '/assets/admin/img/booking.png')}}" style="width: 20px; height: auto;filter: invert(100%);" /> &nbsp; &nbsp;
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Medicine  {{__('messages.bookings')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('vendor/booking/*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('vendor/booking/vendorelist/ongoing') ? 'active':''}}">
                                    <a class="nav-link "   href="{{route('vendor.order.vendorelist', ['status' => 'ongoing'])}}"
                                       title="Ongoing {{__('messages.orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
    Ongoing
    <span class="badge badge-soft-info badge-pill ml-1">
        {{ \App\Models\Order::where('vendor_id', $id)->ServiceOngoing()->count() }}
    </span>
</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('vendor/booking/vendorelist/cancelled')?'active':''}}">
                                    <a class="nav-link " href="{{route('vendor.order.vendorelist',['cancelled'])}}"
                                       title="Cancelled {{__('messages.orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Cancelled
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                0
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('vendor/booking/vendorelist/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('vendor.order.vendorelist',['delivered'])}}"
                                       title="Delivered {{__('messages.orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Delivered
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where('vendor_id', $id)->Completed()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>


                                <li class="nav-item {{Request::is('vendor/booking/vendorelist/all')?'active':''}}">
                                    <a class="nav-link " href="{{route('vendor.order.vendorelist',['all'])}}"
                                       title="All {{__('messages.orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            All
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where('vendor_id', $id)->All()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                              
                            </ul>
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