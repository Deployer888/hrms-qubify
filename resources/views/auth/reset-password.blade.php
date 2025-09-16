@extends('layouts.auth')
@php
    $logo = asset(Storage::url('uploads/logo/'));
@endphp
@section('page-title')
    {{ __('Forgot Password') }}
@endsection
@section('content')
    <div class="login-contain">
        <div class="login-inner-contain">
            <a class="navbar-brand" href="#">
                <img src="{{ $logo . '/logo.png' }}" class="navbar-brand-img auth-logo" alt="logo">
            </a>
            <div class="login-form">
                <div class="page-title">
                    <h5>{{ __('Reset Password') }}</h5>
                </div>
                <form action="{{ route('password.update') }}" method="post" id="loginForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="form-group">
                        <label for="email" class="form-control-label">{{ __('E-Mail Address') }}</label>
                        <input type="text" name="email" id="email" class="form-control"
                            value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-control-label">{{ __('Password') }}</label>
                        <input type="password" name="password" id="password" class="form-control">
                        @error('password')
                            <span class="invalid-password text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation"
                            class="form-control-label">{{ __('Password Confirmation') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        @error('password_confirmation')
                            <span class="invalid-password_confirmation text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn-login" id="resetBtn">{{ __('Reset Password') }}</button>
                </form>
            </div>
            <h5 class="copyright-text">
                {{ Utility::getValByName('footer_text') ? Utility::getValByName('footer_text') : __('Copyright HRMGo') }}
                {{ date('Y') }}
            </h5>
        </div>
    </div>
@endsection
