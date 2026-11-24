@extends('layouts.admin.app')

@section('title', __('Update  Medical Store'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <i class="tio-add-circle-outlined"></i> {{ __('Update Medical Store') }}
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.medical-store.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{$store->id}}">
                        <!-- Store Name -->
                        <div class="form-group">
                            <label class="input-label" for="name">{{ __('Store Name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter store name" required value="{{$store->name}}">
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label class="input-label" for="email">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address" value="{{$store->email}}" required>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label class="input-label" for="password">{{ __('Password') }}</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password">
                            
                            <!-- Checkbox to show/hide password -->
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="showPassword">
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label class="input-label" for="phone">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number" value="{{$store->mobile_number}}" required>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label class="input-label" for="address">{{ __('Address') }}</label>
                            <textarea name="address" id="address" class="form-control" rows="3" placeholder="Enter store address" value="" required>{{$store->address}}</textarea>
                        </div>

                        <!-- Store Image -->
                        <div class="form-group">
                            <label class="input-label" for="image">{{ __('Store Image') }}</label>
                            <input type="file" name="image" id="image" class="form-control">
                        </div>
                      <div class="form-group">
    <label class="input-label" for="image">{{ __('Store Image') }}</label>
    <img src="{{ asset('storage/app/public/' . $store->image) }}" alt="Store Image" class="img-fluid" style="max-width: 200px; height: auto;">
</div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        <a href="{{ route('admin.medical-store.list') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const showPasswordCheckbox = document.getElementById('showPassword');
        const passwordInput = document.getElementById('password');

        showPasswordCheckbox.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    });
</script>

@endsection


