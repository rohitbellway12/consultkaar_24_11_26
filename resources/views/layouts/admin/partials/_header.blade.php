<div id="headerMain" class="d-none">
    <header id="header"
            class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered" style="background-color: #233671;">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                <!-- Logo -->
                @php
                    $logoSetting = \App\Models\BusinessSetting::where(['key'=>'logo'])->first();
                    $logo = $logoSetting ? $logoSetting->value : '';
                @endphp
                <a class="navbar-brand" href="{{route('admin.dashboard')}}" aria-label="">
                    <?php
                    $logo = (env('APP_ENV') == 'local') ?  asset('storage/business/' . $logo) : asset('storage/app/public/business/' . $logo); 
                    ?>
                    <img class="navbar-brand-logo" style="max-height: 48px; border-radius: 8px;" src="{{$logo}}" alt="Logo">
                    <img class="navbar-brand-logo-mini" style="max-height: 48px; border-radius: 8px;" src="{{$logo}}" alt="Logo">
                </a>
                <!-- End Logo -->
            </div>

            <div class="navbar-nav-wrap-content-left">

                <!-- Navbar Vertical Toggle -->
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                       data-placement="right" title="Collapse"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                       data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                       data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
                <!-- End Navbar Vertical Toggle -->
            </div>
          

            <!-- Secondary Content -->
            <div class="navbar-nav-wrap-content-right">
                <!-- Navbar -->

                <ul class="navbar-nav align-items-center flex-row">

                    <!-- Notification -->
                    @php
                        $adminNotifications = \App\Models\Notification::where('title', 'Reel Achieved Earning Criteria')
                            ->latest()
                            ->take(10)
                            ->get();
                    @endphp
                    <li class="nav-item">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:;" id="notificationDropdown"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false"
                               style="color: #fff; font-size: 1.2rem; position: relative; padding: 0.5rem;">
                                <i class="tio-notifications"></i>
                                @if($adminNotifications->count() > 0)
                                    <span style="position: absolute; top: 0; right: 0; background: #FC6A57; color: #fff; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                        {{ $adminNotifications->count() }}
                                    </span>
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-right"
                                 aria-labelledby="notificationDropdown"
                                 style="width: 22rem; z-index: 9999; padding: 0;">
                                <div class="dropdown-item-text" style="padding: 0.75rem 1rem;">
                                    <span class="card-title h5">Notifications</span>
                                </div>
                                <div class="dropdown-divider"></div>

                                <div style="max-height: 300px; overflow-y: auto;">
                                    @forelse($adminNotifications as $notification)
                                        <a class="dropdown-item" href="javascript:;" @if(strpos($notification->description, 'Reel') !== false) onclick="location.href='{{route('admin.reel.list')}}'" @endif style="padding: 0.5rem 1rem;">
                                            <span class="text-truncate pr-2" title="{{ $notification->description }}">
                                                <strong>{{ $notification->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($notification->description, 60) }}</small>
                                            </span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @empty
                                        <div class="dropdown-item-text text-center text-muted py-3">
                                            No notifications
                                        </div>
                                    @endforelse
                                </div>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="{{ route('admin.reel.notifications') }}">
                                    View All Reel Notifications
                                </a>
                            </div>
                        </div>
                    </li>
                    <!-- End Notification -->





                    <li class="nav-item">
                        <!-- Account -->
                        <div class="hs-unfold">
                            <?php
                            $def_image = (env('APP_ENV') == 'local') ?  asset('storage/admin/' . auth('admin')->user()->image) : asset('storage/app/public/admin/' . auth('admin')->user()->image); 
                            ?>
                            <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper" href="javascript:;"
                               data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                         onerror="this.src='{{asset($assetPrefixPath . '/assets/admin/img/160x160/img1.jpg')}}'"
                                         src="{{$def_image}}"
                                         alt="Image Description">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>

                            <div id="accountNavbarDropdown"
                                 class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account"
                                 style="width: 16rem;">
                                <div class="dropdown-item-text">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-sm avatar-circle mr-2">
                                            <img class="avatar-img"
                                                 onerror="this.src='{{asset($assetPrefixPath . '/assets/admin/img/160x160/img1.jpg')}}'"
                                                 src="{{$def_image}}"
                                                 alt="Image Description">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{auth('admin')->user()->name}}</span>
                                            <span class="card-text">{{auth('admin')->user()->email}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{route('admin.settings')}}">
                                    <span class="text-truncate pr-2" title="Settings">{{__('messages.settings')}}</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="javascript:" onclick="Swal.fire({
                                    title: 'Do you want to logout?',
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#FC6A57',
                                    cancelButtonColor: '#363636',
                                    confirmButtonText: `Yes`,
                                    denyButtonText: `Don't Logout`,
                                    }).then((result) => {
                                    if (result.value) {
                                    location.href='{{route('admin.auth.logout')}}';
                                    } else{
                                    Swal.fire('Canceled', '', 'info')
                                    }
                                    })">
                                    <span class="text-truncate pr-2" title="Sign out">{{__('messages.sign_out')}}</span>
                                </a>
                            </div>
                        </div>
                        <!-- End Account -->
                    </li>
                </ul>
                <!-- End Navbar -->
            </div>
            <!-- End Secondary Content -->
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>
