@extends('layouts.auth')
@section('page-title')
    {{__('Login')}}
@endsection
@php
    $logo=asset(Storage::url('uploads/logo/'));
@endphp

@push('custom-scripts')
@if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
@endif
@endpush

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    body{
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background:rgb(0, 0, 0);
    }

    .login-contain video {
        position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0.25;
    object-fit: cover;
    }


    .login-contain .login-form {
        margin: 75px auto 0 auto;
    }

    .box {
        position: relative;
        width: 460px;
        height: 540px;
        background: #efefef;
        border-radius: 8px;
        overflow: hidden;
        padding: 10px;
    }

    .box::before{
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 380px;
        height: 420px;
        background: linear-gradient(0deg, transparent, transparent, #45f3ff, #45f3ff, #45f3ff);
        z-index: 1;
        transform-origin: bottom right;
        animation: animate 6s linear infinite;
    }

    .box::after{
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 380px;
        height: 420px;
        background: linear-gradient(0deg, transparent, transparent, #45f3ff, #45f3ff, #45f3ff);
        z-index: 1;
        transform-origin: bottom right;
        animation: animate 6s linear infinite;
        animation-delay: -3s;
    }

    .borderLine::before{
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 380px;
        height: 420px;
        background: linear-gradient(0deg, transparent, transparent, #ff2770, #ff2770, #ff2770);
        z-index: 1;
        transform-origin: bottom right;
        animation: animate 6s linear infinite;
        animation-delay: -1.5s;
    }

    .borderLine::after
    {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 380px;
        height: 420px;
        background: linear-gradient(0deg, transparent, transparent, #ff2770, #ff2770, #ff2770);
        z-index: 1;
        transform-origin: bottom right;
        animation: animate 6s linear infinite;
        animation-delay: -4.5s;
    }


    @keyframes animate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
        
    }

    .box form{
        position: absolute;
        inset: 4px;
        background: #fff;
        padding: 50px 40px;
        border-radius: 8px;
        z-index: 2;
        display: flex;
        flex-direction: column;
    }

    .box form h2{
        color: #fff;
        font-weight: 500;
        text-align: center;
        letter-spacing: 0.1em;
    }

    .box form .inputBox{
        position: relative;
        width: 300px;
        margin-top: 35px;
    }

    .box form .inputBox input{
        position: relative;
        width: 100%;
        padding: 20px 10px 10px;
        background: transparent;
        outline: none;
        border: none;
        box-shadow: none;
        color: #23242a;
        font-size: 1em;
        letter-spacing: 0.05em;
        transition: 0.5s;
        z-index: 10;
    }

    .box form .inputBox span{
        position: absolute;
        left: 0;
        padding: 20px 0px 10px;
        pointer-events: none;
        color: #8f8f8f;
        font-size: 1em;
        letter-spacing: 0.05em;
        transition: 0.5s;
    }

    .box form .inputBox input:valid ~ span,
    .box form .inputBox input:focus ~ span {
        color: #fff;
        font-size: 0.75em;
        transform: translateY((-34px));
    }

    .box form .inputBox i{
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 2px;
        background: #fff;
        border-radius: 4px;
        overflow: hidden;
        transition: 0.5s;
        pointer-events: none;
    }

    .box form .inputBox input:valid ~ i,
    .box form .inputBox input:focus ~ i {
        height: 44px;
    }

    .box form .links{
        display: flex;
        justify-content: space-between;
    }

    .box form .links a{
        margin: 10px 0;
        font-size: 0.75em;
        color: #8f8f8f;
        text-decoration: none;
    }

    .box form .links a:hover,
    .box form .links a:nth-child(2){
        color: #fff;
    }

    #submit{
        border: none;
        outline: none;
        padding: 9px 25px;
        cursor: pointer;
        font-size: 0.9em;
        border-radius: 4px;
        font-weight: 600;
        width: 100px;
        margin-top: 10px;
    }

    #submit:active{
        opacity: 0.8;
    }
    .login-contain::before {
        background: none!important;
    }
</style>

    <div class="login-contain">
            <div class="bg-video-container">
                <video autoplay muted loop playsinline>
                    <source src="../../assets/images/bg-login.mp4" type="video/mp4">
                </video>
            </div>
        <div class="login-inner-contain">
            <div class="login-form box">
                <span class="borderLine"></span>
                    <form method="POST" action="{{ route('login') }}">
                        <a class="navbar-brand mb-3" href="{{ url('/') }}" style="white-space: normal!important;">
                            <div class="row text-center" style="justify-content: center;">
                                <img src="{{ asset('storage/uploads/logo/logo.png') }}" class="navbar-brand-img auth-logo" alt="logo">
                            </div>
                        </a>
                        <div class="page-title"><h5>{{__('Login')}}</h5></div>
                        @csrf
                        <div class="form-group">
                            <label for="email" class="form-control-label">{{__('Email')}}</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>

                        <div class="form-group" style="position: relative;">
                            <label for="password" class="form-control-label">{{__('Password')}}</label>
                            <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required autocomplete="current-password">
                            <span class="toggle-password">
                                <i class="fa fa-eye"></i>
                            </span>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        
                        @if(env('RECAPTCHA_MODULE') == 'yes')
                        <div class="form-group ">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                            <span class="small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        @endif

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-primary">{{ __('Forgot Your Password?') }}</a>
                        @endif

                        <button type="submit" class="btn-login">{{__('Login')}}</button>
                        
                        
                        {{-- <!-- @if(Utility::getValByName('disable_signup_button')=='on')
                        <div class="or-text">{{__('OR')}}</div>
                        <small class="text-muted">{{__("Don't have an account?")}}</small>
                        <a href="{{ route('register') }}" class="text-xs text-primary">{{__('Register')}}</a>
                        @endif --> --}}
                    </form>
            </div>
            <h5 class="copyright-text">
                {{(Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :  __('Copyright Qubify') }} {{ date('Y') }}
            </h5>
            {{-- <!-- <div class="all-select">
                <a href="#" class="monthly-btn">
                    <span class="monthly-text py-0">{{__('Change Language')}}</span>
                    <select class="select-box select2" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);" id="language">
                        @foreach(Utility::languages() as $language)
                            <option @if($lang == $language) selected @endif value="{{ route('login',$language) }}">{{Str::upper($language)}}</option>
                        @endforeach
                    </select>
                </a>
            </div> --> --}}
        </div>
    </div>

@endsection
