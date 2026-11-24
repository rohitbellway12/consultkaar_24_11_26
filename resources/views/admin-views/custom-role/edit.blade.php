@extends('layouts.admin.app')
@section('title','Edit Role')
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid"> 
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item" aria-current="page">Role Update</li>
        </ol>
    </nav>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">Employee Role</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.custom-role.update',[$role['id']])}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="input-label qcont" for="name">{{__('messages.role_name')}}</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{$role['name']}}"
                                   placeholder="Ex : Store" required>
                        </div>

                        <label class="input-label qcont" for="name">{{__('messages.module_permission')}} : </label>
                        <hr>
<div class="row">
    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="patients" class="form-check-input"
                   id="patients" {{ in_array('patients', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="patients">Customer</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="doctors" class="form-check-input"
                   id="doctors" {{ in_array('doctors', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="doctors">Advisor</label>
        </div>
    </div>

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="medicine_bookings" class="form-check-input"-->
    <!--               id="medicine_bookings" {{ in_array('medicine_bookings', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="medicine_bookings">Medicine Bookings</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="lab_test_bookings" class="form-check-input"-->
    <!--               id="lab_test_bookings" {{ in_array('lab_test_bookings', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="lab_test_bookings">Lab Test Bookings</label>-->
    <!--    </div>-->
    <!--</div>-->

    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="appointments" class="form-check-input"
                   id="appointments" {{ in_array('appointments', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="appointments">Consultations</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="wallet_withdrawal_requests" class="form-check-input"
                   id="wallet_withdrawal_requests" {{ in_array('wallet_withdrawal_requests', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="wallet_withdrawal_requests">Wallet Withdrawal Requests</label>
        </div>
    </div>

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="product_categories" class="form-check-input"-->
    <!--               id="product_categories" {{ in_array('product_categories', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="product_categories">Product Categories</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="product_types" class="form-check-input"-->
    <!--               id="product_types" {{ in_array('product_types', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="product_types">Product Types</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="products" class="form-check-input"-->
    <!--               id="products" {{ in_array('products', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="products">Products</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="specialities" class="form-check-input"-->
    <!--               id="specialities" {{ in_array('specialities', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="specialities">Specialities</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="disease" class="form-check-input"-->
    <!--               id="disease" {{ in_array('disease', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="disease">Diseases</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="lab_categories" class="form-check-input"-->
    <!--               id="lab_categories" {{ in_array('lab_categories', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="lab_categories">Lab Test Categories</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="lab_tests" class="form-check-input"-->
    <!--               id="lab_tests" {{ in_array('lab_tests', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="lab_tests">Lab Tests</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="labs" class="form-check-input"-->
    <!--               id="labs" {{ in_array('labs', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="labs">Labs</label>-->
    <!--    </div>-->
    <!--</div>-->

    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="custom_role" class="form-check-input"
                   id="custom_role" {{ in_array('custom_role', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="custom_role">{{ __('messages.custom_role') }}</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="banners" class="form-check-input"
                   id="banners" {{ in_array('banners', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="banners">Banners</label>
        </div>
    </div>

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="allergies" class="form-check-input"-->
    <!--               id="allergies" {{ in_array('allergies', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="allergies">Allergies</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="medications" class="form-check-input"-->
    <!--               id="medications" {{ in_array('medications', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="medications">Medications</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="injuries" class="form-check-input"-->
    <!--               id="injuries" {{ in_array('injuries', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="injuries">Injuries</label>-->
    <!--    </div>-->
    <!--</div>-->

    <!--<div class="col-md-3">-->
    <!--    <div class="form-group form-check">-->
    <!--        <input type="checkbox" name="modules[]" value="faqs" class="form-check-input"-->
    <!--               id="faqs" {{ in_array('faqs', (array)json_decode($role['modules'])) ? 'checked' : '' }}>-->
    <!--        <label class="form-check-label qcont text-dark" for="faqs">FAQS</label>-->
    <!--    </div>-->
    <!--</div>-->

    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="employee" class="form-check-input"
                   id="employee" {{ in_array('employee', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="employee">{{ __('messages.Employee') }}</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group form-check">
            <input type="checkbox" name="modules[]" value="settings" class="form-check-input"
                   id="settings" {{ in_array('settings', (array)json_decode($role['modules'])) ? 'checked' : '' }}>
            <label class="form-check-label qcont text-dark" for="settings">{{ __('messages.business') }} {{ __('messages.settings') }}</label>
        </div>
    </div>
</div>

                        <button type="submit" class="btn btn-primary">{{trans('messages.update')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
