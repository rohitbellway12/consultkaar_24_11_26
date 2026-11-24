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
                <a class="navbar-brand" href="{{route('lab.dashboard')}}" aria-label="Front">
                    <img class="navbar-brand-logo" style="max-height: 55px; max-width: 100%!important;"
                         onerror="this.src='{{asset($assetPrefixPath . '/assets/lab/img/160x160/logo2.png')}}'"
                         src="{{$logoPath}}"
                         alt="Logo">
                    <img class="navbar-brand-logo-mini" style="max-height: 55px; max-width: 100%!important;"
                         onerror="this.src='{{asset($assetPrefixPath . '/assets/lab/img/160x160/logo2.png')}}'"
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
                           href="{{route('lab.dashboard')}}" title="{{__('messages.dashboard')}}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{__('messages.dashboard')}}  
                            </span>
                        </a>
                    </li>
                    <!-- End Dashboards -->
             <!-- Booking Section -->
<li class="nav-item">
    <small class="nav-subtitle">{{ __('Booking Section') }}</small>
    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
</li>

<li class="navbar-vertical-aside-has-menu {{ Request::is('lab/lab-test-booking-list') ? 'active' : '' }}">
    <a class="js-navbar-vertical-aside-menu-link nav-link"
       href="{{ route('lab.lab-test-booking-list') }}" 
       title="{{ __('Lab Test Bookings') }}">
        <img src="{{ asset($assetPrefixPath . '/assets/lab/img/booking.png') }}" 
             style="width: 20px; height: auto; filter: invert(100%);" />
        &nbsp; &nbsp;
        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
            {{ __('Lab Test Bookings') }}
        </span>
    </a>
</li>

<!-- Lab Section -->
<li class="nav-item">
    <small class="nav-subtitle">{{ __('Lab Section') }}</small>
    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
</li>

<!-- Lab Test Section -->
<li class="navbar-vertical-aside-has-menu {{ Request::is('lab/test*') ? 'active' : '' }}">
    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
       href="javascript:;" 
       title="{{ __('Lab Tests') }}">
        <i class="tio-user nav-icon"></i>
        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
            {{ __('Lab Tests') }}
        </span>
    </a>

    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
        style="display: {{ Request::is('lab/test*') ? 'block' : 'none' }}">

        <!-- Add Lab Test -->
        <li class="nav-item {{ Request::is('lab/test/create') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('lab.lab-test.add-new') }}" 
               title="{{ __('Add Lab Test') }}">
                <span class="tio-circle nav-indicator-icon"></span>
                <span class="text-truncate">{{ __('Add Lab Test') }}</span>
            </a>
        </li>

        <!-- Lab Test List -->
        <li class="nav-item {{ Request::is('lab/test/list') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('lab.lab-test.list') }}" 
               title="{{ __('Lab Test List') }}">
                <span class="tio-circle nav-indicator-icon"></span>
                <span class="text-truncate">{{ __('Lab Test List') }}</span>
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