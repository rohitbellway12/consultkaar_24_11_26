@extends('layouts.admin.app')

@section('title','SMS Module Setup')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-sm-0">
                    <h1 class="page-header-title">{{__('messages.sms')}} {{__('messages.gateway')}} {{__('messages.setup')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" style="padding-bottom: 20px">
        

           

        

            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">ClickSend</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('clicksend_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.sms-module-update',['clicksend_sms']):'javascript:'}}"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label class="control-label">Status</label>
                            </div>
                            <div class="form-group mb-2 mt-2">
                                <input type="radio" name="status" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                <label style="padding-left: 10px">{{__('messages.active')}}</label>

                                <input type="radio" name="status" value="0" {{isset($config) && $config['status']==0?'checked':''}} style="margin-left: 20px">
                                <label style="padding-left: 10px">{{__('messages.inactive')}} </label>
                                <br>
                            </div>
                            <div class="form-group mb-2">
                              
                                <br>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize"
                                       style="padding-left: 10px">{{__('messages.template_id')}} to verify new account</label><br>
                                <input type="text" class="form-control" name="template_id_for_verify_new_account"
                                       value="{{env('APP_MODE')!='demo'?$config['template_id_for_verify_new_account']??"":''}}" required>
                            </div>

                            <div class="form-group mb-2">
                                <label class="text-capitalize"
                                       style="padding-left: 10px">{{__('messages.username')}}</label><br>
                                <input type="text" class="form-control" name="username"
                                       value="{{env('APP_MODE')!='demo'?$config['username']??"":''}}" required>
                            </div>

                            <div class="form-group mb-2">
                                <label class="text-capitalize"
                                       style="padding-left: 10px">{{__('messages.template_id')}} to verify account</label><br>
                                <input type="text" class="form-control" name="template_id_for_verify_account"
                                       value="{{env('APP_MODE')!='demo'?$config['template_id_for_verify_account']??"":''}}" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize"
                                       style="padding-left: 10px">API Key</label><br>
                                <input type="text" class="form-control" name="apikey"
                                       value="{{env('APP_MODE')!='demo'?$config['apikey']??"":''}}" required>
                            </div>

                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary mb-2">{{__('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">MSG91 WhatsApp</h5>
                        @php($waConfig=\App\CentralLogics\Helpers::get_business_settings('msg91_whatsapp'))
                        <form action="{{ env('APP_MODE')!='demo'?route('admin.business-settings.sms-module-update',['msg91_whatsapp']):'javascript:' }}" method="post">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="control-label">Status</label>
                            </div>
                            <div class="form-group mb-2 mt-2">
                                <input type="radio" name="status" value="1" {{ isset($waConfig) && ($waConfig['status'] ?? 0)==1 ? 'checked' : '' }}>
                                <label style="padding-left: 10px">{{__('messages.active')}}</label>

                                <input type="radio" name="status" value="0" {{ isset($waConfig) && ($waConfig['status'] ?? 0)==0 ? 'checked' : '' }} style="margin-left: 20px">
                                <label style="padding-left: 10px">{{__('messages.inactive')}} </label>
                                <br>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize" style="padding-left: 10px">MSG91 Auth Key</label><br>
                                <input type="text" class="form-control" name="authkey" value="{{ env('APP_MODE')!='demo'?$waConfig['authkey']??"":'' }}" required>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize" style="padding-left: 10px">WhatsApp Flow ID</label><br>
                                <input type="text" class="form-control" name="flow_id" value="{{ env('APP_MODE')!='demo'?$waConfig['flow_id']??"":'' }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">{{__('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
