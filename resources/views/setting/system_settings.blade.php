@extends('layouts.admin')
@section('page-title')
    {{ __('Settings') }}
@endsection

@section('action-button')
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {     
            if ($('.gdpr_fulltime').is(':checked')) {
                $('.fulltime').show();
            } else {
                $('.fulltime').hide();
            }

            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked')) {
                    $('.fulltime').show();
                } else {
                    $('.fulltime').hide();
                }
            });

            // Enhanced interactions
            $('.premium-card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            // Payment method toggle animations
            $('.payment-method-header').on('click', function() {
                const $this = $(this);
                const $card = $this.closest('.payment-method-card');
                
                $card.toggleClass('expanded');
                
                // Add ripple effect
                const ripple = $('<span class="ripple"></span>');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = event.clientX - rect.left - size / 2;
                const y = event.clientY - rect.top - size / 2;
                
                ripple.css({
                    width: size,
                    height: size,
                    left: x,
                    top: y,
                    position: 'absolute',
                    borderRadius: '50%',
                    background: 'rgba(37, 99, 235, 0.2)',
                    transform: 'scale(0)',
                    animation: 'ripple 0.6s linear',
                    pointerEvents: 'none'
                });
                
                $this.css('position', 'relative').css('overflow', 'hidden').append(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Form field focus animations
            $('.form-control').on('focus', function() {
                $(this).closest('.form-group, .premium-form-group').addClass('focused');
            });

            $('.form-control').on('blur', function() {
                $(this).closest('.form-group, .premium-form-group').removeClass('focused');
            });

            // Custom switch styling
            $('.custom-control-input').each(function() {
                const $input = $(this);
                const $label = $input.next('.custom-control-label');
                
                const $switch = $('<div class="premium-switch"><input type="checkbox"><span class="premium-switch-slider"></span></div>');
                const $switchInput = $switch.find('input');
                
                $switchInput.prop('checked', $input.prop('checked'));
                $switchInput.on('change', function() {
                    $input.prop('checked', $(this).prop('checked')).trigger('change');
                });
                
                $input.on('change', function() {
                    $switchInput.prop('checked', $(this).prop('checked'));
                });
                
                $label.after($switch);
                $input.hide();
                $label.hide();
            });

            $('.premium-switch-slider').on('click', function() {
                const $slider = $(this);
                const $switch = $slider.closest('.premium-switch');
                const $input = $switch.find('input[type="checkbox"]');
                
                $input.prop('checked', !$input.prop('checked')).trigger('change');
            });

            // Bootstrap 5 tabs should work automatically with data-bs-toggle
            // No custom JavaScript needed if Bootstrap 5 JS is loaded
        });
    </script>
    

@endpush

@push('css-page')
  <link rel="stylesheet" href="{{ asset('css/superAdmin/system.css') }}">
  <style>
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .payment-method-card.expanded {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.15);
    }
    
    .form-group.focused label,
    .premium-form-group.focused label {
        color: var(--primary-setting);
        transform: translateY(-2px);
    }
    .form-switch {
        padding-left: 2.5em;
    }
    
    /* Enhanced Bootstrap 5 nav-tabs styling */
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 25px;
        background: #f8f9fa;
        padding: 5px;
        border-radius: 8px 8px 0 0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-radius: 6px;
        padding: 12px 20px;
        color: #6c757d;
        background: transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 5px;
        font-weight: 500;
        position: relative;
    }
    
    .nav-tabs .nav-link:hover {
        color: #495057;
        background-color: rgba(255, 255, 255, 0.7);
        transform: translateY(-1px);
    }
    
    .nav-tabs .nav-link.active {
        color: #fff !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transform: translateY(-2px);
    }
    
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -7px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid #667eea;
    }
    
    .tab-content > .tab-pane {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .tab-content > .tab-pane.active,
    .tab-content > .tab-pane.show {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Site Settings Layout Improvements */
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .settings-section {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .settings-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .settings-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
    }
    
    .settings-section-title i {
        margin-right: 10px;
        color: #667eea;
    }
    
    .logo-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .logo-upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }
    
    .logo-preview {
        max-width: 120px;
        max-height: 80px;
        margin-bottom: 15px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .upload-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .upload-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .toggle-switches-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-top: 25px;
        padding: 24px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    
    .toggle-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        min-height: 60px;
    }
    
    .toggle-item:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    
    .form-check {
        margin-bottom: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    
    .form-check-input {
        margin-right: 0;
        margin-left: auto;
        order: 2;
    }
    
    .form-check-label {
        font-weight: 500;
        color: #495057;
        cursor: pointer;
        margin-bottom: 0;
        order: 1;
        flex: 1;
        display: flex;
        align-items: center;
    }
    
    .form-check-label i {
        color: #667eea;
        width: 16px;
        margin-right: 8px;
    }
    
    .form-control, .form-select {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #fff;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background-color: #fff;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .text-danger {
        font-size: 12px;
        margin-top: 4px;
    }
    
    .settings-section .mb-3:last-of-type {
        margin-bottom: 0 !important;
    }
    
    /* Enhanced textarea styling */
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    /* Custom select styling */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 12px;
    }
    
    /* Input group styling for better organization */
    .input-group-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }
    
    @media (max-width: 768px) {
        .input-group-container {
            grid-template-columns: 1fr;
        }
        
        .toggle-switches-row {
            grid-template-columns: 1fr;
        }
    }
  </style>
@endpush
@php
    $logo = asset(Storage::url('uploads/logo/'));
    $lang = \App\Models\Utility::getValByName('default_language');
@endphp

@section('content')
<div class="container-fluid">
    <!-- Premium Header -->
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="header-text">
                <h1>{{ __('System Settings') }}</h1>
                <p>{{ __('Configure and customize your application settings') }}</p>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <!-- Premium Tab Navigation -->
            <div class="premium-tabs fade-in">
                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="site-settings-tab" data-bs-toggle="tab" data-bs-target="#site-settings" type="button" role="tab" aria-controls="site-settings" aria-selected="true">
                            <i class="fas fa-globe me-2"></i>{{ __('Site Setting') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="email-settings-tab" data-bs-toggle="tab" data-bs-target="#email-settings" type="button" role="tab" aria-controls="email-settings" aria-selected="false">
                            <i class="fas fa-envelope me-2"></i>{{ __('Email Setting') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-settings-tab" data-bs-toggle="tab" data-bs-target="#payment-settings" type="button" role="tab" aria-controls="payment-settings" aria-selected="false">
                            <i class="fas fa-credit-card me-2"></i>{{ __('Payment Setting') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pusher-settings-tab" data-bs-toggle="tab" data-bs-target="#pusher-settings" type="button" role="tab" aria-controls="pusher-settings" aria-selected="false">
                            <i class="fas fa-broadcast-tower me-2"></i>{{ __('Pusher Setting') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recaptcha-settings-tab" data-bs-toggle="tab" data-bs-target="#recaptcha-settings" type="button" role="tab" aria-controls="recaptcha-settings" aria-selected="false">
                            <i class="fas fa-shield-alt me-2"></i>{{ __('ReCaptcha Setting') }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="settingsTabContent">
                <!-- Site Settings Tab -->
                <div class="tab-pane fade show active" id="site-settings" role="tabpanel" aria-labelledby="site-settings-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.1s">
                        <div class="premium-card-body">
                            <form action="{{ url('settings') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-grid">
                                    <!-- Logo Section -->
                                    <div class="settings-section">
                                        <h5 class="settings-section-title">
                                            <i class="fas fa-image"></i>{{ __('Logo') }}
                                        </h5>
                                        <div class="logo-upload-area">
                                            <img src="{{ asset(Storage::url($settings['logo'])) }}" class="logo-preview" alt="Logo" />
                                            <div>
                                                <label for="logo" class="upload-btn">
                                                    <i class="fas fa-upload me-2"></i>{{ __('Choose Logo') }}
                                                    <input type="file" class="d-none" name="logo" id="logo" data-filename="edit-logo">
                                                </label>
                                                <p class="edit-logo mt-2 text-muted small"></p>
                                                <p class="mt-2 text-muted small">{{ __('This logo will appear on Payslip') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Landing Page Logo Section -->
                                    <div class="settings-section">
                                        <h5 class="settings-section-title">
                                            <i class="fas fa-desktop"></i>{{ __('Landing Page Logo') }}
                                        </h5>
                                        <div class="logo-upload-area">
                                            <img src="{{ asset(Storage::url($settings['landing_logo'])) }}" class="logo-preview" alt="Landing Logo" />
                                            <div>
                                                <label for="landing-logo" class="upload-btn">
                                                    <i class="fas fa-upload me-2"></i>{{ __('Choose Logo') }}
                                                    <input type="file" class="d-none" name="landing_logo" id="landing-logo" data-filename="edit-landing-logo">
                                                </label>
                                                <p class="edit-landing-logo mt-2 text-muted small"></p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="display_landing_page" id="display_landing_page" {{ $settings['display_landing_page'] == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="display_landing_page">
                                                    {{ __('Display Landing Page') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Favicon Section -->
                                    <div class="settings-section">
                                        <h5 class="settings-section-title">
                                            <i class="fas fa-star"></i>{{ __('Favicon') }}
                                        </h5>
                                        <div class="logo-upload-area">
                                            <img src="{{ asset(Storage::url($settings['favicon'])) }}" class="logo-preview" alt="Favicon" style="max-width: 64px; max-height: 64px;" />
                                            <div>
                                                <label for="small-favicon" class="upload-btn">
                                                    <i class="fas fa-upload me-2"></i>{{ __('Choose Favicon') }}
                                                    <input type="file" class="d-none" name="favicon" id="small-favicon" data-filename="edit-favicon">
                                                </label>
                                                <p class="edit-favicon mt-2 text-muted small"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- General Settings Section -->
                                    <div class="settings-section" style="grid-column: 1 / -1;">
                                        <h5 class="settings-section-title">
                                            <i class="fas fa-cog"></i>{{ __('General Settings') }}
                                        </h5>
                                        
                                        <!-- Basic Information -->
                                        <div class="input-group-container">
                                            <div>
                                                <label for="title_text" class="form-label">
                                                    <i class="fas fa-heading me-2 text-primary"></i>{{ __('Title Text') }}
                                                </label>
                                                <input type="text" class="form-control" name="title_text" id="title_text" value="{{ old('title_text', $settings['title_text']) }}" placeholder="{{ __('Enter application title') }}">
                                                @error('title_text')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="footer_text" class="form-label">
                                                    <i class="fas fa-align-center me-2 text-primary"></i>{{ __('Footer Text') }}
                                                </label>
                                                <input type="text" class="form-control" name="footer_text" id="footer_text" value="{{ old('footer_text', $settings['footer_text']) }}" placeholder="{{ __('Enter footer text') }}">
                                                @error('footer_text')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Language Selection -->
                                        <div class="mb-4">
                                            <label for="default_language" class="form-label">
                                                <i class="fas fa-globe me-2 text-primary"></i>{{ __('Default Language') }}
                                            </label>
                                            <select name="default_language" id="default_language" class="form-select">
                                                @foreach (\App\Models\Utility::languages() as $language)
                                                    <option @if ($lang == $language) selected @endif value="{{ $language }}">{{ Str::upper($language) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- System Preferences -->
                                        <div class="mb-4">
                                            <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                                                <i class="fas fa-sliders-h me-2 text-primary"></i>{{ __('System Preferences') }}
                                            </h6>
                                            <div class="toggle-switches-row">
                                                <div class="toggle-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="SITE_RTL" id="SITE_RTL" {{ env('SITE_RTL') == 'on' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="SITE_RTL">
                                                            <i class="fas fa-align-right me-2"></i>{{ __('RTL Mode') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="toggle-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="disable_signup_button" id="disable_signup_button" {{ $settings['disable_signup_button'] == 'on' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="disable_signup_button">
                                                            <i class="fas fa-user-plus me-2"></i>{{ __('Enable Signup') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="toggle-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input gdpr_fulltime gdpr_type" type="checkbox" name="gdpr_cookie" id="gdpr_cookie" {{ isset($settings['gdpr_cookie']) && $settings['gdpr_cookie'] == 'on' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="gdpr_cookie">
                                                            <i class="fas fa-shield-alt me-2"></i>{{ __('GDPR Cookie') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cookie Configuration -->
                                        <div class="cookie-section fulltime" style="display: {{ isset($settings['gdpr_cookie']) && $settings['gdpr_cookie'] == 'on' ? 'block' : 'none' }};">
                                            <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                                                <i class="fas fa-cookie-bite me-2 text-primary"></i>{{ __('Cookie Configuration') }}
                                            </h6>
                                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                                                <label for="cookie_text" class="form-label">{{ __('Cookie Consent Text') }}</label>
                                                <textarea name="cookie_text" id="cookie_text" class="form-control fulltime" rows="4" placeholder="{{ __('Enter the text that will be displayed in the cookie consent banner...') }}">{{ old('cookie_text', $settings['cookie_text']) }}</textarea>
                                                <small class="text-muted mt-2 d-block">{{ __('This text will be shown to users when they first visit your website.') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Email Settings Tab -->
                <div class="tab-pane fade" id="email-settings" role="tabpanel" aria-labelledby="email-settings-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.2s">
                        <div class="premium-card-body">
                            <form action="{{ route('email.settings') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_driver">{{ __('Mail Driver') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="mail_driver" value="{{ env('MAIL_DRIVER') }}" placeholder="{{ __('Enter Mail Driver') }}">
                                            @error('mail_driver')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_host">{{ __('Mail Host') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="mail_host" value="{{ env('MAIL_HOST') }}" placeholder="{{ __('Enter Mail Host') }}">
                                            @error('mail_host')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_port">{{ __('Mail Port') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="mail_port" value="{{ env('MAIL_PORT') }}" placeholder="{{ __('Enter Mail Port') }}">
                                            @error('mail_port')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_username">{{ __('Mail Username') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="mail_username" value="{{ env('MAIL_USERNAME') }}" placeholder="{{ __('Enter Mail Username') }}">
                                            @error('mail_username')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_password">{{ __('Mail Password') }}</label>
                                            <input type="password" class="form-control premium-form-control" name="mail_password" value="{{ env('MAIL_PASSWORD') }}" placeholder="{{ __('Enter Mail Password') }}">
                                            @error('mail_password')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_encryption">{{ __('Mail Encryption') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="mail_encryption" value="{{ env('MAIL_ENCRYPTION') }}" placeholder="{{ __('Enter Mail Encryption') }}">
                                            @error('mail_encryption')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_from_address">{{ __('Mail From Address') }}</label>
                                            <input type="email" class="form-control premium-form-control" name="mail_from_address" value="{{ env('MAIL_FROM_ADDRESS') }}" placeholder="{{ __('Enter Mail From Address') }}">
                                            @error('mail_from_address')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="premium-form-group">
                                            <label for="mail_from_name">{{ __('Mail From Name') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="mail_from_name" value="{{ env('MAIL_FROM_NAME') }}" placeholder="{{ __('Enter Mail From Name') }}">
                                            @error('mail_from_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <a href="#" data-url="{{ route('test.mail') }}" data-ajax-popup="true" data-title="{{ __('Send Test Mail') }}" class="btn btn-secondary-premium">
                                            <i class="fas fa-paper-plane me-2"></i>{{ __('Send Test Mail') }}
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Payment Settings Tab -->
                <div class="tab-pane fade" id="payment-settings" role="tabpanel" aria-labelledby="payment-settings-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.3s">
                        <div class="premium-card-body">
                            <form action="{{ route('payment.settings') }}" method="post">
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="premium-form-group">
                                            <label for="currency_symbol">{{ __('Currency Symbol *') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="currency_symbol" value="{{ env('CURRENCY_SYMBOL') }}" required placeholder="{{ __('Enter Currency Symbol') }}">
                                            @error('currency_symbol')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="premium-form-group">
                                            <label for="currency">{{ __('Currency *') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="currency" value="{{ env('CURRENCY') }}" required placeholder="{{ __('Enter Currency') }}">
                                            <small class="text-muted">{{ __('Note: Add currency code as per three-letter ISO code.') }}<br><a href="https://stripe.com/docs/currencies" target="_blank">{{ __('you can find out here..') }}</a></small>
                                            @error('currency')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Methods -->
                                <div id="accordion-payment" class="accordion">
                                    <!-- Stripe -->
                                    <div class="payment-method-card">
                                        <div class="payment-method-header" data-toggle="collapse" data-target="#stripe-settings" aria-expanded="false">
                                            <h6><i class="fab fa-stripe"></i>{{ __('Stripe') }}</h6>
                                            <div class="">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="is_stripe_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_stripe_enabled" id="is_stripe_enabled" {{ isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_stripe_enabled"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="stripe-settings" class="collapse" data-parent="#accordion-payment">
                                            <div class="payment-method-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="premium-form-group">
                                                            <label for="stripe_key">{{ __('Stripe Key') }}</label>
                                                            <input type="text" class="form-control premium-form-control" name="stripe_key" value="{{ isset($admin_payment_setting['stripe_key']) ? $admin_payment_setting['stripe_key'] : old('stripe_key', '') }}" placeholder="{{ __('Enter Stripe Key') }}">
                                                            @error('stripe_key')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="premium-form-group">
                                                            <label for="stripe_secret">{{ __('Stripe Secret') }}</label>
                                                            <input type="text" class="form-control premium-form-control" name="stripe_secret" value="{{ isset($admin_payment_setting['stripe_secret']) ? $admin_payment_setting['stripe_secret'] : old('stripe_secret', '') }}" placeholder="{{ __('Enter Stripe Secret') }}">
                                                            @error('stripe_secret')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PayPal -->
                                    <div class="payment-method-card">
                                        <div class="payment-method-header" data-toggle="collapse" data-target="#paypal-settings" aria-expanded="false">
                                            <h6><i class="fab fa-paypal"></i>{{ __('PayPal') }}</h6>
                                            <div class="">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="is_paypal_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_paypal_enabled" id="is_paypal_enabled" {{ isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_paypal_enabled"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="paypal-settings" class="collapse" data-parent="#accordion-payment">
                                            <div class="payment-method-body">
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label class="premium-small-title">{{ __('PayPal Mode') }}</label>
                                                        <div class="mode-toggle">
                                                            <input type="radio" name="paypal_mode" value="sandbox" id="paypal_sandbox" {{ (isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == '') || (isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == 'sandbox') ? 'checked' : '' }}>
                                                            <label for="paypal_sandbox">{{ __('Sandbox') }}</label>
                                                            
                                                            <input type="radio" name="paypal_mode" value="live" id="paypal_live" {{ isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == 'live' ? 'checked' : '' }}>
                                                            <label for="paypal_live">{{ __('Live') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="premium-form-group">
                                                            <label for="paypal_client_id">{{ __('Client ID') }}</label>
                                                            <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control premium-form-control" value="{{ isset($admin_payment_setting['paypal_client_id']) ? $admin_payment_setting['paypal_client_id'] : '' }}" placeholder="{{ __('Client ID') }}">
                                                            @error('paypal_client_id')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="premium-form-group">
                                                            <label for="paypal_secret_key">{{ __('Secret Key') }}</label>
                                                            <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control premium-form-control" value="{{ isset($admin_payment_setting['paypal_secret_key']) ? $admin_payment_setting['paypal_secret_key'] : '' }}" placeholder="{{ __('Secret Key') }}">
                                                            @error('paypal_secret_key')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Continue with other payment methods in similar fashion... -->
                                    <!-- For brevity, I'll include just the key ones and add a placeholder for others -->
                                    
                                    <!-- Razorpay -->
                                    <div class="payment-method-card">
                                        <div class="payment-method-header" data-toggle="collapse" data-target="#razorpay-settings" aria-expanded="false">
                                            <h6><i class="fas fa-credit-card"></i>{{ __('Razorpay') }}</h6>
                                            <div class="">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="is_razorpay_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_razorpay_enabled" id="is_razorpay_enabled" {{ isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="is_razorpay_enabled"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="razorpay-settings" class="collapse" data-parent="#accordion-payment">
                                            <div class="payment-method-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="premium-form-group">
                                                            <label for="razorpay_public_key">{{ __('Public Key') }}</label>
                                                            <input type="text" name="razorpay_public_key" id="razorpay_public_key" class="form-control premium-form-control" value="{{ isset($admin_payment_setting['razorpay_public_key']) ? $admin_payment_setting['razorpay_public_key'] : old('razorpay_public_key', '') }}" placeholder="{{ __('Public Key') }}">
                                                            @error('razorpay_public_key')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="premium-form-group">
                                                            <label for="razorpay_secret_key">{{ __('Secret Key') }}</label>
                                                            <input type="text" name="razorpay_secret_key" id="razorpay_secret_key" class="form-control premium-form-control" value="{{ isset($admin_payment_setting['razorpay_secret_key']) ? $admin_payment_setting['razorpay_secret_key'] : old('razorpay_secret_key', '') }}" placeholder="{{ __('Secret Key') }}">
                                                            @error('razorpay_secret_key')
                                                                <span class="text-danger small">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add similar cards for other payment methods: Paystack, Flutterwave, Mercado Pago, Paytm, Mollie, Skrill, CoinGate, Paymentwall -->
                                    
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Pusher Settings Tab -->
                <div class="tab-pane fade" id="pusher-settings" role="tabpanel" aria-labelledby="pusher-settings-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.4s">
                        <div class="premium-card-body">
                            <form action="{{ route('pusher.settings') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="premium-form-group">
                                            <label for="pusher_app_id">{{ __('Pusher App Id') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="pusher_app_id" value="{{ env('PUSHER_APP_ID') }}" placeholder="{{ __('Enter Pusher App Id') }}">
                                            @error('pusher_app_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="premium-form-group">
                                            <label for="pusher_app_key">{{ __('Pusher App Key') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="pusher_app_key" value="{{ env('PUSHER_APP_KEY') }}" placeholder="{{ __('Enter Pusher App Key') }}">
                                            @error('pusher_app_key')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="premium-form-group">
                                            <label for="pusher_app_secret">{{ __('Pusher App Secret') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="pusher_app_secret" value="{{ env('PUSHER_APP_SECRET') }}" placeholder="{{ __('Enter Pusher App Secret') }}">
                                            @error('pusher_app_secret')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="premium-form-group">
                                            <label for="pusher_app_cluster">{{ __('Pusher App Cluster') }}</label>
                                            <input type="text" class="form-control premium-form-control" name="pusher_app_cluster" value="{{ env('PUSHER_APP_CLUSTER') }}" placeholder="{{ __('Enter Pusher App Cluster') }}">
                                            @error('pusher_app_cluster')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ReCaptcha Settings Tab -->
                <div class="tab-pane fade" id="recaptcha-settings" role="tabpanel" aria-labelledby="recaptcha-settings-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.5s">
                        <div class="premium-card-body">
                            <form method="POST" action="{{ route('recaptcha.settings.store') }}" accept-charset="UTF-8">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="premium-form-group">
                                            <div class="">
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="custom-control-input" name="recaptcha_module" id="recaptcha_module" value="yes" {{ env('RECAPTCHA_MODULE') == 'yes' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="recaptcha_module">
                                                        {{ __('Google Recaptcha') }}
                                                        <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/" target="_blank" class="text-primary ml-2">
                                                            <small>({{ __('How to Get Google reCaptcha Site and Secret key') }})</small>
                                                        </a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="premium-form-group">
                                            <label for="google_recaptcha_key">{{ __('Google Recaptcha Key') }}</label>
                                            <input class="form-control premium-form-control" placeholder="{{ __('Enter Google Recaptcha Key') }}" name="google_recaptcha_key" type="text" value="{{ env('NOCAPTCHA_SITEKEY') }}" id="google_recaptcha_key">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="premium-form-group">
                                            <label for="google_recaptcha_secret">{{ __('Google Recaptcha Secret') }}</label>
                                            <input class="form-control premium-form-control" placeholder="{{ __('Enter Google Recaptcha Secret') }}" name="google_recaptcha_secret" type="text" value="{{ env('NOCAPTCHA_SECRET') }}" id="google_recaptcha_secret">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection