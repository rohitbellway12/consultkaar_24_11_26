@php
    $appEnv = env('APP_ENV');
    $assetPrefixPath = $appEnv == 'local' ? '' : 'public';

@endphp
<style>
    .nav-sub {
        background: #233671 !important;
    }
</style>

<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container" style="background-color: #233671;">
            <div class="navbar-brand-wrapper justify-content-between" style="background-color: #233671;">
                <!-- Logo -->
                @php(@$restaurant_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first()->value)

                <?php
                
                $logoPath = env('APP_ENV') == 'local' ? asset('storage/business/' . $restaurant_logo) : asset('storage/app/public/business/' . $restaurant_logo);
                ?>
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}" aria-label="Front">
                    <img class="navbar-brand-logo" style="max-height: 55px; max-width: 100%!important;"
                        onerror="this.src='{{ asset($assetPrefixPath . '/assets/admin/img/160x160/logo2.png') }}'"
                        src="{{ $logoPath }}" alt="Logo">
                    <img class="navbar-brand-logo-mini" style="max-height: 55px; max-width: 100%!important;"
                        onerror="this.src='{{ asset($assetPrefixPath . '/assets/admin/img/160x160/logo2.png') }}'"
                        src="{{ $logoPath }}" alt="Logo">
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
            <div class="navbar-vertical-content"
                style="background-color: #233671 ;border-bottom: 1px solid white;
            ">
                <ul class="navbar-nav navbar-nav-lg nav-tabs">
                    <!-- Dashboards -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin') ? 'show' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.dashboard') }}"
                            title="{{ __('messages.dashboard') }}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ __('messages.dashboard') }}
                            </span>
                        </a>
                    </li>
                    <!-- End Dashboards -->

                    <!-- user -->


                    @if (\App\CentralLogics\Helpers::module_permission_check('patients'))
                        <li class="nav-item">
                            <!--<small class="nav-subtitle">{{ __('messages.user') }} {{ __('section') }}</small>-->
                            <small class="nav-subtitle">Customers {{ __('section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>



                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/patient*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Patients">
                                <i class="tio-user nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Customers</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/patient*') ? 'block' : 'none' }}">


                                <li class="nav-item {{ Request::is('admin/patient/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.patient.list') }}"
                                        title="Patient {{ __('messages.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.list') }}</span>
                                    </a>
                                </li>


                            </ul>
                        </li>
                    @endif
                    <!-- End user -->


                    @if (\App\CentralLogics\Helpers::module_permission_check('doctors'))
                        <li class="nav-item">
                            <!--<small class="nav-subtitle">Doctor {{ __('section') }}</small>-->
                            <small class="nav-subtitle">Advisor {{ __('section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/doctor*') ? 'active' : '' }}">


                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Doctors">
                                <i class="tio-user nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Advisor</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/doctor*') ? 'block' : 'none' }}">

                                <li class="nav-item {{ Request::is('admin/doctor/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.doctor.add-new') }}"
                                        title="{{ __('messages.add') }} {{ __('messages.new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.add') }}
                                            {{ __('messages.new') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/doctor/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.doctor.list') }}"
                                        title="Doctor {{ __('messages.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.list') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/doctor/request-list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.doctor.request-list') }}"
                                        title="Advisor Requests">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Advisor Requests</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/blog/request-list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.blog.request-list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Advisor Blogs</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/reel/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.reel.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Knowledge Reels</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/reel/report') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.reel.report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Reel Report</span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endif

                    <!-- AI Assistant Section (GPT-6 Luna) -->
                    <li class="nav-item">
                        <small class="nav-subtitle">AI Assistant (GPT-6 Luna)</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/ai-assistant*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="AI Assistant">
                            <i class="tio-settings-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">AI Assistant</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="{{ Request::is('admin/ai-assistant*') ? 'display: block;' : 'display: none;' }}">
                            <li class="nav-item {{ Request::is('admin/ai-assistant/settings') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.ai.settings') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">AI Settings</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/ai-assistant/questions') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.ai.questions') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Trending Questions</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/ai-assistant/sessions*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.ai.sessions') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Chat History & Logs</span>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <!--medical store section-->
                    <!-----------------------------------------------------------Rifual changes-------------------------------------------->
                    {{--  @if (\App\CentralLogics\Helpers::module_permission_check('medical-store'))
    <li class="nav-item">
        <small class="nav-subtitle">Medical Store {{__('section')}}</small>
        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
    </li>

    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/medical-store*') ? 'active' : '' }}">
        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
           href="javascript:"
           title="Medical Stores">
            <i class="tio-user nav-icon"></i>
            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                Medical Stores
            </span>
        </a>
        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
            style="display: {{ Request::is('admin/medical-store*') ? 'block' : 'none' }}">
            
            <li class="nav-item {{ Request::is('admin/medical-store/add-new') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.medical-store.add-new') }}"
                   title="{{ __('messages.add') }} {{ __('messages.new') }}">
                    <span class="tio-circle nav-indicator-icon"></span>
                    <span class="text-truncate">{{ __('messages.add') }} {{ __('messages.new') }}</span>
                </a>
            </li>
    
            <li class="nav-item {{ Request::is('admin/medical-store/list') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.medical-store.list') }}"
                   title="Medical Store {{ __('messages.list') }}">
                    <span class="tio-circle nav-indicator-icon"></span>
                    <span class="text-truncate">{{ __('messages.list') }}</span>
                </a>
            </li>
        </ul>
    </li>
@endif --}}







                    <!-- Orders -->

                    @if (
                        \App\CentralLogics\Helpers::module_permission_check('medicine_bookings') ||
                            \App\CentralLogics\Helpers::module_permission_check('lab_test_bookings'))
                        <!--<li class="nav-item">-->
                        <!--    <small-->
                        <!--        class="nav-subtitle"> {{ __('messages.booking') }} {{ __('messages.section') }}</small>-->
                        <!--    <small class="tio-more-horizontal nav-subtitle-replacer"></small>-->
                        <!--</li>-->
                    @endif
                    @if (\App\CentralLogics\Helpers::module_permission_check('medicine_bookings'))
                        <!--<li class="navbar-vertical-aside-has-menu {{ Request::is('admin/booking/*') ? 'active' : '' }}">-->
                        <!--      <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"-->
                        <!--         href="javascript:" title="{{ __('messages.order') }}">-->
                        <!--          {{-- <i class="tio-shopping-cart nav-icon"></i> --}}-->
                        <!--          <img src="{{ asset($assetPrefixPath . '/assets/admin/img/booking.png') }}" style="width: 20px; height: auto;filter: invert(100%);" /> &nbsp; &nbsp;-->
                        <!--          <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">-->
                        <!--              Medicine  {{ __('messages.bookings') }}-->
                        <!--          </span>-->
                        <!--      </a>-->
                        <!--      <ul class="js-navbar-vertical-aside-submenu nav nav-sub"-->
                        <!--          style="display: {{ Request::is('admin/booking/*') ? 'block' : 'none' }}">-->
                        <!--          <li class="nav-item {{ Request::is('admin/booking/list/ongoing') ? 'active' : '' }}">-->
                        <!--              <a class="nav-link " href="{{ route('admin.order.list', ['ongoing']) }}"-->
                        <!--                 title="Ongoing {{ __('messages.orders') }}">-->
                        <!--                  <span class="tio-circle nav-indicator-icon"></span>-->
                        <!--                  <span class="text-truncate">-->
                        <!--                      Ongoing-->
                        <!--                      <span class="badge badge-soft-info badge-pill ml-1">-->
                        <!--                          {{ \App\Models\Order::ServiceOngoing()->count() }}-->
                        <!--                      </span>-->
                        <!--                  </span>-->
                        <!--              </a>-->
                        <!--          </li>-->
                        <!--          <li class="nav-item {{ Request::is('admin/booking/list/cancelled') ? 'active' : '' }}">-->
                        <!--              <a class="nav-link " href="{{ route('admin.order.list', ['cancelled']) }}"-->
                        <!--                 title="Cancelled {{ __('messages.orders') }}">-->
                        <!--                  <span class="tio-circle nav-indicator-icon"></span>-->
                        <!--                  <span class="text-truncate">-->
                        <!--                      Cancelled-->
                        <!--                      <span class="badge badge-soft-info badge-pill ml-1">-->
                        <!--                        {{ \App\Models\Order::cancelled()->count() }}-->
                        <!--                      </span>-->
                        <!--                  </span>-->
                        <!--              </a>-->
                        <!--          </li>-->
                        <!--          <li class="nav-item {{ Request::is('admin/booking/list/delivered') ? 'active' : '' }}">-->
                        <!--              <a class="nav-link " href="{{ route('admin.order.list', ['delivered']) }}"-->
                        <!--                 title="Delivered {{ __('messages.orders') }}">-->
                        <!--                  <span class="tio-circle nav-indicator-icon"></span>-->
                        <!--                  <span class="text-truncate">-->
                        <!--                      Delivered-->
                        <!--                      <span class="badge badge-soft-info badge-pill ml-1">-->
                        <!--                          {{ \App\Models\Order::Completed()->count() }}-->
                        <!--                      </span>-->
                        <!--                  </span>-->
                        <!--              </a>-->
                        <!--          </li>-->


                        <!--          <li class="nav-item {{ Request::is('admin/booking/list/all') ? 'active' : '' }}">-->
                        <!--              <a class="nav-link " href="{{ route('admin.order.list', ['all']) }}"-->
                        <!--                 title="All {{ __('messages.orders') }}">-->
                        <!--                  <span class="tio-circle nav-indicator-icon"></span>-->
                        <!--                  <span class="text-truncate">-->
                        <!--                      All-->
                        <!--                      <span class="badge badge-soft-info badge-pill ml-1">-->
                        <!--                          {{ \App\Models\Order::All()->count() }}-->
                        <!--                      </span>-->
                        <!--                  </span>-->
                        <!--              </a>-->
                        <!--          </li>-->

                        <!--      </ul>-->
                        <!--  </li> -->
                    @endif


                    {{--  @if (\App\CentralLogics\Helpers::module_permission_check('lab_test_bookings'))

                        
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/lab-test-booking-list')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.lab-test-booking-list')}}" title="Lab Test  {{__('messages.bookings')}}"
                            >
                            <img src="{{asset($assetPrefixPath . '/assets/admin/img/booking.png')}}" style="width: 20px; height: auto;filter: invert(100%);" /> &nbsp; &nbsp;
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                Lab Test  {{__('messages.bookings')}}
                            </span>
                            </a>
                        </li>
                        
                        @endif --}}



                    <!-- End Orders -->

                    @if (\App\CentralLogics\Helpers::module_permission_check('appointments'))
                        <li class="nav-item">
                            <small class="nav-subtitle"> Consultant {{ __('messages.section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/appointment-list/*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Appointments">
                                {{-- <i class="tio-shopping-cart nav-icon"></i> --}}
                                <img src="{{ asset($assetPrefixPath . '/assets/admin/img/booking.png') }}"
                                    style="width: 20px; height: auto;filter: invert(100%);" /> &nbsp; &nbsp;
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Consultants Booking
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/appointment-list/*') ? 'block' : 'none' }}">
                                <li
                                    class="nav-item {{ Request::is('admin/appointment-list/pending') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ url('admin/appointment-list/pending') }}"
                                        title="Pending Appointments">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Pending
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\CallRequest::where('call_status', 'pending')->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('admin/appointment-list/confirmed') ? 'active' : '' }}">
                                    <a class="nav-link "href="{{ url('admin/appointment-list/confirmed') }}"
                                        title="Confirmed Appointments">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Confirmed
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\CallRequest::where('call_status', 'confirmed')->count() }}

                                            </span>
                                        </span>
                                    </a>
                                </li>



                                <li
                                    class="nav-item {{ Request::is('admin/appointment-list/cancelled') ? 'active' : '' }}">
                                    <a class="nav-link "href="{{ url('admin/appointment-list/cancelled') }}"
                                        title="Completed Appointments">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Cancelled
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\CallRequest::where('call_status', 'cancelled')->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('admin/appointment-list/completed') ? 'active' : '' }}">
                                    <a class="nav-link "href="{{ url('admin/appointment-list/completed') }}"
                                        title="Completed Appointments">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Completed
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\CallRequest::where('call_status', 'complete')->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>


                                <li class="nav-item {{ Request::is('admin/appointment-list/all') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ url('admin/appointment-list/all') }}"
                                        title="All {{ __('messages.orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            All
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\CallRequest::count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                            </ul>





                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/appointment-list/*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Appointments">
                                {{-- <i class="tio-shopping-cart nav-icon"></i> --}}
                                <img src="{{ asset($assetPrefixPath . '/assets/admin/img/booking.png') }}"
                                    style="width: 20px; height: auto;filter: invert(100%);" /> &nbsp; &nbsp;
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Wallet
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/Wallet-list/') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/Wallet-list/') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ url('admin/wallet-list/') }}"
                                        title="Pending Appointments">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            customer wallet
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\User::where('role_id', 2)->where('isregister', 1)->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/wallet-list-advisor') ? 'active' : '' }}">
                                    <a class="nav-link "href="{{ url('admin/wallet-list-advisor') }}"
                                        title="Confirmed Appointments">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Advisor wallet
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\User::where('role_id', 3)->where('isregister', 1)->count() }}

                                            </span>
                                        </span>
                                    </a>
                                </li>



                                <li
                                    class="nav-item {{ Request::is('admin/appointment-list/cancelled') ? 'active' : '' }}">
                                    <a class="nav-link "href="{{ route('admin.wallet-withdrawal-requests') }}"
                                        title="Completed Appointments">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            Wallet Request for Advisor
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\WithDrawRequest::where('status', 'pending')->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>






                            </ul>




                        </li>







































                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/wallet-withdrawal-requests') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.wallet-withdrawal-requests') }}"
                                title="Wallet Withdrawal Requests">
                                <img src="{{ asset($assetPrefixPath . '/assets/admin/img/booking.png') }}"
                                    style="width: 20px; height: auto;filter: invert(100%);" /> &nbsp; &nbsp;
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Wallet Withdrawal Requests
                                </span>
                            </a>
                        </li>
                        </li>
                    @endif

                    @if (
                        \App\CentralLogics\Helpers::module_permission_check('product_categories') ||
                            \App\CentralLogics\Helpers::module_permission_check('product_types') ||
                            \App\CentralLogics\Helpers::module_permission_check('products'))
                        <li class="nav-item">
                            <small class="nav-subtitle" title="Product {{ __('messages.section') }}">service
                                Management</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                    @endif


                    <!-- Category -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('category'))
                        <li
                            class="navbar-vertical-aside-has-menu d-none {{ Request::is('admin/category*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="{{ __('messages.category') }}">
                                <i class="tio-category nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ __('messages.categories') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/category*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/category/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.category.add-new') }}"
                                        title="{{ __('messages.add') }} {{ __('messages.new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.add') }}
                                            {{ __('messages.new') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/category/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.category.list') }}"
                                        title="{{ __('messages.category') }} {{ __('messages.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.list') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    <!-- End Category -->


                    {{--  @if (\App\CentralLogics\Helpers::module_permission_check('product_categories'))

                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product-category*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                           href="javascript:" title="{{__('messages.category')}}"
                        >
                            <i class="tio-category nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> Product {{__('messages.categories')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('admin/product-category*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('admin/product-category/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product-category.add-new')}}"
                                   title="{{__('messages.add')}} {{__('messages.new')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{__('messages.add')}} {{__('messages.new')}}</span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('admin/product-category/list')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product-category.list')}}"
                                   title="{{__('messages.category')}} {{__('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{__('messages.list')}}</span>
                                </a>
                            </li>                               
                        </ul>
                    </li>
                    
                    @endif --}}


                    {{--   @if (\App\CentralLogics\Helpers::module_permission_check('product_types'))

                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product-types/*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                           href="javascript:" title="{{__('messages.category')}}"
                        >
                            <i class="tio-category nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> Product Types</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('admin/product-type/*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('admin/product-type/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product-type.add-new')}}"
                                   title="{{__('messages.add')}} {{__('messages.new')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{__('messages.add')}} {{__('messages.new')}}</span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('admin/product-type/list')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product-type.list')}}"
                                   title="Product Type {{__('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{__('messages.list')}}</span>
                                </a>
                            </li>                               
                        </ul>
                    </li>

                    @endif  --}}


                    @if (\App\CentralLogics\Helpers::module_permission_check('products'))
                        {{--   <li class="navbar-vertical-aside-has-menu {{Request::is('admin/service*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="Product"
                        >
                            <i class="tio-premium-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{__('messages.products')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('admin/product/*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('admin/service/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product.add-new')}}"
                                    title="{{__('messages.add')}} {{__('messages.new')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{__('messages.add')}} {{__('messages.new')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/product/list')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product.list')}}"
                                    title="Product {{__('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{__('messages.list')}}</span>
                                </a>
                            </li>
                            
                        </ul>
                    </li> --}}
                    @endif

                    @if (\App\CentralLogics\Helpers::module_permission_check('specialities'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/domain*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Domains">
                                <i class="tio-category nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Domains</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/domain*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/domain/add') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.domain.add-new') }}"
                                        title="{{ __('messages.add') }} {{ __('messages.new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.add') }}
                                            {{ __('messages.new') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/domain/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.domain.list') }}"
                                        title="Domain {{ __('messages.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.list') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if (\App\CentralLogics\Helpers::module_permission_check('specialities'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/speciality*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Specialities">
                                <i class="tio-category nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Specialization</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/speciality*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/speciality/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.speciality.add-new') }}"
                                        title="{{ __('messages.add') }} {{ __('messages.new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.add') }}
                                            {{ __('messages.new') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/speciality/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.speciality.list') }}"
                                        title="Speciality {{ __('messages.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.list') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif



                    <!------------------------------------------------------Rifaul changes------------------------------------------>
                    @if (\App\CentralLogics\Helpers::module_permission_check('degrees'))
                        <!--<li-->
                        <!--    class="navbar-vertical-aside-has-menu {{ Request::is('admin/degree*') ? 'active' : '' }}">-->
                        <!--    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"-->
                        <!--        title="Relevant Documents">-->
                        <!--        <i class="tio-category nav-icon"></i>-->
                        <!--        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Relevant-->
                        <!--            Documents</span>-->
                        <!--    </a>-->
                        <!--    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"-->
                        <!--        style="display: {{ Request::is('admin/degree*') ? 'block' : 'none' }}">-->
                        <!--        <li class="nav-item {{ Request::is('admin/degree/add-new') ? 'active' : '' }}">-->
                        <!--            <a class="nav-link " href="{{ route('admin.degree.add-new') }}"-->
                        <!--                title="{{ __('messages.add') }} {{ __('messages.new') }}">-->
                        <!--                <span class="tio-circle nav-indicator-icon"></span>-->
                        <!--                <span class="text-truncate">{{ __('messages.add') }}-->
                        <!--                    {{ __('messages.new') }}</span>-->
                        <!--            </a>-->
                        <!--        </li>-->

                        <!--        <li class="nav-item {{ Request::is('admin/degree/list') ? 'active' : '' }}">-->
                        <!--            <a class="nav-link " href="{{ route('admin.degree.list') }}"-->
                        <!--                title="Relevant Documents {{ __('messages.list') }}">-->
                        <!--                <span class="tio-circle nav-indicator-icon"></span>-->
                        <!--                <span class="text-truncate">{{ __('messages.list') }}</span>-->
                        <!--            </a>-->
                        <!--        </li>-->
                        <!--    </ul>-->
                        <!--</li>-->
                    @endif

                    @if (\App\CentralLogics\Helpers::module_permission_check('degrees'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/identity-proof*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Identity Proof">
                                <i class="tio-category nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Identity
                                    Proof</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/identity-proof*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/identity-proof/add') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.identity-proof.add-new') }}"
                                        title="{{ __('messages.add') }} {{ __('messages.new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.add') }}
                                            {{ __('messages.new') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/identity-proof/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.identity-proof.list') }}"
                                        title="Identity Proof {{ __('messages.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.list') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- Relevant Document Types --}}
                    @if (\App\CentralLogics\Helpers::module_permission_check('degrees'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/relevant-document-type*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Document Types">
                                <i class="tio-folder-opened nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Document
                                    Types</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/relevant-document-type*') ? 'block' : 'none' }}">
                                <li
                                    class="nav-item {{ Request::is('admin/relevant-document-type/add') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.relevant-document-type.add-new') }}"
                                        title="{{ __('messages.add') }} {{ __('messages.new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.add') }}
                                            {{ __('messages.new') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif


                    @if (\App\CentralLogics\Helpers::module_permission_check('disease'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/disease*') ? 'active' : '' }}">

                            <!--    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"-->
                            <!--        title="Diseases">-->
                            <!--        <i class="tio-category nav-icon"></i>-->
                            <!--        <span-->
                            <!--            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Counselor</span>-->
                            <!--    </a>-->
                            <!--    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"-->
                            <!--        style="display: {{ Request::is('admin/disease*') ? 'block' : 'none' }}">-->
                            <!--        <li class="nav-item {{ Request::is('admin/disease/add-new') ? 'active' : '' }}">-->
                            <!--            <a class="nav-link " href="{{ route('admin.disease.add-new') }}"-->
                            <!--                title="{{ __('messages.add') }} {{ __('messages.new') }}">-->
                            <!--                <span class="tio-circle nav-indicator-icon"></span>-->
                            <!--                <span class="text-truncate">{{ __('messages.add') }}-->
                            <!--                    {{ __('messages.new') }}</span>-->
                            <!--            </a>-->
                            <!--        </li>-->

                            <!--        <li class="nav-item {{ Request::is('admin/disease/list') ? 'active' : '' }}">-->
                            <!--            <a class="nav-link " href="{{ route('admin.disease.list') }}"-->
                            <!--                title="disease {{ __('messages.list') }}">-->
                            <!--                <span class="tio-circle nav-indicator-icon"></span>-->
                            <!--                <span class="text-truncate">{{ __('messages.list') }}</span>-->
                            <!--            </a>-->
                            <!--        </li>-->
                            <!--    </ul>-->
                            <!--</li>-->
                    @endif

                    {{--   @if (\App\CentralLogics\Helpers::module_permission_check('lab_categories'))


                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/lab-test-category*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                           href="javascript:" title="{{__('messages.category')}}"
                        >
                            <i class="tio-category nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> Lab Test {{__('messages.categories')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('admin/lab-test-category*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('admin/lab-test-category/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.lab-test-category.add-new')}}"
                                   title="{{__('messages.add')}} {{__('messages.new')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{__('messages.add')}} {{__('messages.new')}}</span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('admin/lab-test-category/list')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.lab-test-category.list')}}"
                                   title="{{__('messages.category')}} {{__('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{__('messages.list')}}</span>
                                </a>
                            </li>                               
                        </ul>
                    </li>
                    @endif     --}}

                    @if (\App\CentralLogics\Helpers::module_permission_check('lab_tests'))
                        {{--   <li class="navbar-vertical-aside-has-menu {{Request::is('admin/lab-test')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                           href="javascript:" title="{{__('messages.category')}}"
                        >
                            <i class="tio-category nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> Lab Test </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('admin/lab-test/*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('admin/lab-test/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.lab-test.add-new')}}"
                                   title="{{__('messages.add')}} {{__('messages.new')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{__('messages.add')}} {{__('messages.new')}}</span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('admin/lab-test/list')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.lab-test.list')}}"
                                   title="{{__('messages.category')}} {{__('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{__('messages.list')}}</span>
                                </a>
                            </li>                               
                        </ul>
                    </li> --}}
                    @endif

                    {{--     @if (\App\CentralLogics\Helpers::module_permission_check('labs'))

                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/lab')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                           href="javascript:" title="{{__('messages.category')}}"
                        >
                            <i class="tio-category nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> Lab </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('admin/lab/*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('admin/lab/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.lab.add-new')}}"
                                   title="{{__('messages.add')}} {{__('messages.new')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{__('messages.add')}} {{__('messages.new')}}</span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('admin/lab/list')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.lab.list')}}"
                                   title="{{__('messages.category')}} {{__('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{__('messages.list')}}</span>
                                </a>
                            </li>                               
                        </ul>
                    </li>
                    
                    @endif  --}}
                    <!-- Marketing section -->
                    <li class="nav-item">
                        <small class="nav-subtitle"
                            title="{{ __('messages.customer_handle') }}">{{ __('messages.marketing') }}
                            {{ __('messages.section') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <!-- Banner -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('banners'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/banner*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.banner.add-new') }}" title="{{ __('messages.banner') }}">
                                <i class="tio-image nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ __('messages.banners') }}</span>
                            </a>
                        </li>
                    @endif
                    <!-- End Banner -->

                    <!--    @if (\App\CentralLogics\Helpers::module_permission_check('allergies'))
-->
                    <!--    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/allergy*') ? 'active' : '' }}">-->
                    <!--        <a class="js-navbar-vertical-aside-menu-link nav-link"-->
                    <!--           href="{{ route('admin.allergy.add-new') }}" title="Allergies"-->
                    <!--        >-->
                    <!--            <i class="tio-image nav-icon"></i>-->
                    <!--            <span-->
                    <!--                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Allergies</span>-->
                    <!--        </a>-->
                    <!--    </li>     -->

                    <!--
@endif-->


                    <!--    @if (\App\CentralLogics\Helpers::module_permission_check('medications'))
-->

                    <!--    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/medication*') ? 'active' : '' }}">-->
                    <!--        <a class="js-navbar-vertical-aside-menu-link nav-link"-->
                    <!--           href="{{ route('admin.medication.add-new') }}" title="Medications"-->
                    <!--        >-->
                    <!--            <i class="tio-image nav-icon"></i>-->
                    <!--            <span-->
                    <!--                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Medications</span>-->
                    <!--        </a>-->
                    <!--    </li>    -->
                    <!--
@endif-->
                    <!--    @if (\App\CentralLogics\Helpers::module_permission_check('injuries'))
-->

                    <!--    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/injury*') ? 'active' : '' }}">-->
                    <!--        <a class="js-navbar-vertical-aside-menu-link nav-link"-->
                    <!--           href="{{ route('admin.injury.add-new') }}" title="Injuries"-->
                    <!--        >-->
                    <!--            <i class="tio-image nav-icon"></i>-->
                    <!--            <span-->
                    <!--                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Injuries</span>-->
                    <!--        </a>-->
                    <!--    </li>  -->
                    <!--
@endif-->

                    <!--    @if (\App\CentralLogics\Helpers::module_permission_check('allergies'))
-->

                    <!--    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/surgery*') ? 'active' : '' }}" >-->
                    <!--        <a class="js-navbar-vertical-aside-menu-link nav-link"-->
                    <!--           href="{{ route('admin.surgery.add-new') }}" title="Surgeries"-->
                    <!--        >-->
                    <!--            <i class="tio-image nav-icon"></i>-->
                    <!--            <span-->
                    <!--                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Surgeries</span>-->
                    <!--        </a>-->
                    <!--    </li>  -->
                    <!--
@endif-->


                    <!-- FAQ -->

                    <!--    @if (\App\CentralLogics\Helpers::module_permission_check('faqs'))
-->

                    <!--<li class="navbar-vertical-aside-has-menu {{ Request::is('admin/faq*') ? 'active' : '' }}">-->
                    <!--    <a class="js-navbar-vertical-aside-menu-link nav-link"-->
                    <!--       href="{{ route('admin.faq.faq-add-new') }}" title="FAQ"-->
                    <!--    >-->
                    <!--        <i class="tio-gift nav-icon"></i>-->
                    <!--        <span-->
                    <!--            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">FAQS</span>-->
                    <!--    </a>-->
                    <!--</li>-->
                    <!--
@endif-->
                    <!-- FAQ -->
                    <!-- Notification -->
                    {{-- } @if (\App\CentralLogics\Helpers::module_permission_check('notification'))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/notification*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.notification.add-new')}}"
                               title="{{__('messages.send')}} {{__('messages.notification')}}"
                            >
                                <i class="tio-notifications nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{__('messages.push')}} {{__('messages.notification')}}
                                </span>
                            </a>
                        </li>
                    @endif
                <!-- End Notification -->
                --}

                    <!-- End marketing section -->

                <!-- End account -->

                    <!-- provide_dm_earning -->
                   {{--  @if (\App\CentralLogics\Helpers::module_permission_check('provide_dm_earning'))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/provide-deliveryman-earnings*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.provide-deliveryman-earnings.index')}}"
                               title="{{__('messages.deliverymen_earning_provide')}}"
                            >
                                <i class="tio-send nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{__('messages.deliverymen_earning_provide')}}</span>
                            </a>
                        </li>
                    @endif --}}
                    <!-- End provide_dm_earning -->
                    <!-- Custommer -->

                    <!-- End Custommer -->

                    <!-- Report -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('report'))
                        <li class="nav-item">
                            <small class="nav-subtitle" title="Report and Analytics">Report and Analytics</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/report*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Reports">
                                <i class="tio-chart-bar-4 nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Reports</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/report*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/report/earning') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.earning') }}"
                                        title="Earning Report">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Earning Report</span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('admin/report/customer-report') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.customer-report') }}"
                                        title="Customer Report">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Customer Report</span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('admin/report/advisor-report') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.advisor-report') }}"
                                        title="Advisor Report">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Advisor Report</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <!-- Business Settings -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('settings'))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{ __('messages.business') }} {{ __('messages.settings') }}">{{ __('messages.business') }}
                                {{ __('messages.settings') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/business-setup') ? 'active' : 's' }}">
                            <a class="nav-link " href="{{ route('admin.business-settings.business-setup') }}"
                                title="{{ __('messages.business') }} {{ __('messages.setup') }}">
                                <span class="tio-settings nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.business') }}
                                    {{ __('messages.setup') }}</span>
                            </a>
                        </li>
{{--
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/payment-method') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.business-settings.payment-method') }}"
                                title="{{ __('messages.payment') }} {{ __('messages.methods') }}">
                                <span class="tio-atm nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.payment') }}
                                    {{ __('messages.methods') }}</span>
                            </a>
                        </li>
                        --}}

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/mail-config') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.business-settings.mail-config') }}"
                                title="{{ __('messages.mail') }} {{ __('messages.config') }}">
                                <span class="tio-email nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.mail') }}
                                    {{ __('messages.config') }}</span>
                            </a>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/sms-module') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.business-settings.sms-module') }}"
                                title="{{ __('messages.sms') }} {{ __('messages.module') }}">
                                <span class="tio-message nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.sms') }}
                                    {{ __('messages.module') }}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/fcm-index') ? 'active' : '' }}"
                            style="display:none">
                            <a class="nav-link " href="{{ route('admin.business-settings.fcm-index') }}"
                                title="{{ __('messages.push') }} {{ __('messages.notification') }}">
                                <span class="tio-notifications nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.notification') }}
                                    {{ __('messages.settings') }}</span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/config*') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.business-settings.config-setup') }}"
                                title="{{ __('messages.third_party_apis') }}">
                                <span class="tio-key nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.third_party_apis') }}</span>
                            </a>
                        </li>
                    @endif
                    <!-- End Business Settings -->

                    <!-- web & adpp Settings -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('settings'))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{ __('messages.business') }} {{ __('messages.settings') }}">{{ __('messages.web_and_app') }}
                                {{ __('messages.settings') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/app-settings*') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.business-settings.app-settings') }}"
                                title="{{ __('messages.app_settings') }}">
                                <span class="tio-android nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.app_settings') }}</span>
                            </a>
                        </li>
                        <li
                            class="d-none navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/landing-page-settings*') ? 'active' : '' }}">
                            <a class="nav-link "
                                href="{{ route('admin.business-settings.landing-page-settings', 'index') }}"
                                title="{{ __('messages.landing_page_settings') }}">
                                <span class="tio-website nav-icon"></span>
                                <span class="text-truncate">{{ __('messages.landing_page_settings') }}</span>
                            </a>
                        </li>


                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/pages*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="{{ __('messages.pages') }} {{ __('messages.setup') }}">
                                <i class="tio-pages nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ __('messages.pages') }}
                                    {{ __('messages.setup') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/web-app-settings/pages*') ? 'block' : 'none' }}">

                                <li
                                    class="nav-item {{ Request::is('admin/web-app-settings/pages/terms-and-conditions') ? 'active' : '' }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.business-settings.terms-and-conditions') }}"
                                        title="{{ __('messages.terms_and_condition') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.terms_and_condition') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('admin/web-app-settings/pages/privacy-policy') ? 'active' : '' }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.business-settings.privacy-policy') }}"
                                        title="{{ __('messages.privacy_policy') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.privacy_policy') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('admin/business-settings/pages/about-us') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.business-settings.about-us') }}"
                                        title="{{ __('messages.about_us') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.about_us') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li
                            class="d-none navbar-vertical-aside-has-menu {{ Request::is('admin/file-manager*') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.file-manager.index') }}"
                                title="{{ __('messages.third_party_apis') }}">
                                <span class="tio-album nav-icon"></span>
                                <span class="text-truncate text-capitalize">{{ __('messages.gallery') }}</span>
                            </a>
                        </li>
                    @endif
                    <!-- End web & adpp Settings -->



                    <!-- Employee-->
                    <li class="nav-item">
                        <small class="nav-subtitle" title="{{ __('messages.employee_handle') }}">Employee
                            Section</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    @if (\App\CentralLogics\Helpers::modules_permission_check(['custom_role', 'employee']))
                    @endIf

                    @if (\App\CentralLogics\Helpers::module_permission_check('custom_role'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/custom-role*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.custom-role.create') }}"
                                title="Employee {{ __('messages.Role') }}">
                                <i class="tio-incognito nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Employee
                                    {{ __('messages.Role') }}</span>
                            </a>
                        </li>
                    @endif

                    @if (\App\CentralLogics\Helpers::module_permission_check('employee'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/employee*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="Employee">
                                <i class="tio-user nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Employee</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/employee*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/employee/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.employee.add-new') }}"
                                        title="{{ __('messages.add') }} {{ __('messages.new') }} {{ __('messages.Employee') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.add') }}
                                            {{ __('messages.new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/employee/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.employee.list') }}"
                                        title="Employee {{ __('messages.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ __('messages.list') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endif
                    <!-- End Employee -->



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
