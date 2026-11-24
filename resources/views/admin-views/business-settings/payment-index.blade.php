@extends('layouts.admin.app')

@section('title','Payment Gateway Setup')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{__('messages.payment')}} {{__('messages.gateway')}} {{__('messages.setup')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row" style="padding-bottom: 20px">
            <div class="col-md-6">
               
            </div>
           
        </div>

        <div class="row digital_payment_methods" style="padding-bottom: 20px">
            
           
         {{--  <div class="col-md-12 pt-4">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">{{__('messages.stripe')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['stripe']):'javascript:'}}"
                              method="post">
                            @csrf
                                <div class="form-group mb-2">
                                    <label class="control-label">Status</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                    <label style="padding-left: 10px">{{__('messages.active')}}</label>
                                    <input type="radio" name="status" value="0" {{$config?($config['status']==0?'checked':''):''}}  style="margin-left: 20px">
                                    <label style="padding-left: 10px">{{__('messages.inactive')}} </label>
                                    <br>
                                </div>
                                
                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{__('messages.published')}} {{__('messages.key')}}</label><br>
                                    <input type="text" class="form-control" name="published_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['published_key']:''):''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{__('messages.api')}} {{__('messages.key')}}</label><br>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['api_key']:''):''}}">
                                </div>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mb-2">{{__('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div> --}}
            
              <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">{{__('messages.razorpay')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['razor_pay']):'javascript:'}}"
                            method="post">
                            @csrf
                                <div class="form-group mb-2">
                                    <label class="control-label">{{__('messages.razorpay')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                    <label style="padding-left: 10px">{{__('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config?($config['status']==0?'checked':''):''}}>
                                    <label
                                        style="padding-left: 10px">{{__('messages.inactive')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label style="padding-left: 10px">{{__('messages.razorkey')}}</label><br>
                                    <input type="text" class="form-control" name="razor_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['razor_key']:''):''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label style="padding-left: 10px">{{__('messages.razorsecret')}}</label><br>
                                    <input type="text" class="form-control" name="razor_secret"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['razor_secret']:''):''}}">
                                </div>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{__('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">HDFC</h5>
                        @php($hdfc=\App\CentralLogics\Helpers::get_business_settings('hdfc'))
                        <form action="{{ route('admin.business-settings.payment-method-update',['hdfc']) }}" method="post">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="control-label">Status</label>
                            </div>
                            <div class="form-group mb-2 mt-2">
                                <input type="radio" name="status" value="1" {{isset($hdfc) && ($hdfc['status']??0)==1?'checked':''}}>
                                <label style="padding-left: 10px">Active</label>
                                <input type="radio" name="status" value="0" {{isset($hdfc) && ($hdfc['status']??0)==0?'checked':''}} style="margin-left: 20px">
                                <label style="padding-left: 10px">Inactive</label>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize" style="padding-left: 10px">Merchant ID</label><br>
                                <input type="text" class="form-control" name="merchant_id" value="{{ $hdfc['merchant_id']??'' }}">
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize" style="padding-left: 10px">Access Code</label><br>
                                <input type="text" class="form-control" name="access_code" value="{{ $hdfc['access_code']??'' }}">
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize" style="padding-left: 10px">Working Key</label><br>
                                <input type="text" class="form-control" name="working_key" value="{{ $hdfc['working_key']??'' }}">
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-capitalize" style="padding-left: 10px">Environment</label><br>
                                <select class="form-control" name="environment">
                                    <option value="test" {{ ($hdfc['environment']??'test')=='test'?'selected':'' }}>Test</option>
                                    <option value="live" {{ ($hdfc['environment']??'test')=='live'?'selected':'' }}>Live</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Save</button>
                        </form>
                    </div>
                </div>
            </div>

         
        </div>
    </div>
@endsection

@push('script_2')
<script>
     $('.digital_payment_methods').show();
</script>
@endpush
