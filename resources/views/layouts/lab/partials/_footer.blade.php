@php
$footerTextData = \App\Models\BusinessSetting::where(['key'=>'footer_text'])->first();
$footerText = (isset($footerTextData) && !empty($footerTextData)) ? $footerTextData->value : '';
@endphp
<div class="footer">
    <div class="row justify-content-between align-items-center">
        <div class="col">
            <p class="font-size-sm mb-0">
                @php
                 $businessData     =     \App\Models\BusinessSetting::where(['key'=>'business_name'])->first();
                @endphp
                 &copy; {{ $businessData ? $businessData->value :  ''}}
                  2024. {{ $footerTextData ? $footerTextData->value : ''  }}.  


            </p>
        </div>
        <div class="col-auto">
            <div class="d-flex justify-content-end">
                <!-- List Dot -->
                <ul class="list-inline list-separator">
                   
                    <li class="list-inline-item">
                        <a class="list-separator-link" href="{{route('doctor.settings')}}">{{__('messages.profile')}}</a>
                    </li>

                    <li class="list-inline-item">
                        <!-- Keyboard Shortcuts Toggle -->
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                               href="{{route('doctor.dashboard')}}">
                                <i class="tio-home-outlined"></i>
                            </a>
                        </div>
                        <!-- End Keyboard Shortcuts Toggle -->
                    </li>
                </ul>
                <!-- End List Dot -->
            </div>
        </div>
    </div>
</div>
