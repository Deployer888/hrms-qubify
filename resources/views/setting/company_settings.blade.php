@extends('layouts.admin')
@section('page-title')
    {{ __('Settings') }}
@endsection

@section('action-button')
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            // Email template checkbox functionality
            $(document).on('change', '.email-template-checkbox', function() {
                var url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        // Handle success response
                    },
                });
            });

            // Enhanced interactions
            $('.premium-card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            // Form field focus animations
            $('.form-control').on('focus', function() {
                $(this).closest('.form-group, .premium-form-group').addClass('focused');
            });

            $('.form-control').on('blur', function() {
                $(this).closest('.form-group, .premium-form-group').removeClass('focused');
            });

            // File upload preview
            $('input[type="file"]').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                var targetClass = $(this).data('filename');
                $('.' + targetClass).text(fileName);
            });
        });
    </script>
@endpush

@push('css-page')
<link rel="stylesheet" href="{{ asset('css/company/company-setting.css') }}">
@endpush

@php
    $logo = asset(Storage::url('uploads/logo/'));
    $company_logo = Utility::getValByName('company_logo');
    $company_small_logo = Utility::getValByName('company_small_logo');
    $company_favicon = Utility::getValByName('company_favicon');
@endphp
@section('content')
<div class="container-fluid">
    <!-- Premium Header -->
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="header-text">
                <h1 class="text-white">{{ __('Company Settings') }}</h1>
                <p>{{ __('Configure your company information and system preferences') }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Premium Tab Navigation -->
            <div class="premium-tabs fade-in">
                <ul class="nav nav-tabs" id="companySettingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="business-setting-tab" data-bs-toggle="tab" data-bs-target="#business-setting" type="button" role="tab" aria-controls="business-setting" aria-selected="true">
                            <i class="fas fa-briefcase me-2"></i>{{ __('Business Setting') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-setting-tab" data-bs-toggle="tab" data-bs-target="#system-setting" type="button" role="tab" aria-controls="system-setting" aria-selected="false">
                            <i class="fas fa-cogs me-2"></i>{{ __('System Setting') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="company-setting-tab" data-bs-toggle="tab" data-bs-target="#company-setting" type="button" role="tab" aria-controls="company-setting" aria-selected="false">
                            <i class="fas fa-building me-2"></i>{{ __('Company Setting') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="email-setting-tab" data-bs-toggle="tab" data-bs-target="#email-setting" type="button" role="tab" aria-controls="email-setting" aria-selected="false">
                            <i class="fas fa-envelope me-2"></i>{{ __('Email Notifications') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ip-restrict-setting-tab" data-bs-toggle="tab" data-bs-target="#ip-restrict-setting" type="button" role="tab" aria-controls="ip-restrict-setting" aria-selected="false">
                            <i class="fas fa-shield-alt me-2"></i>{{ __('IP Restrictions') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="zoom-setting-tab" data-bs-toggle="tab" data-bs-target="#zoom-setting" type="button" role="tab" aria-controls="zoom-setting" aria-selected="false">
                            <i class="fas fa-video me-2"></i>{{ __('Zoom') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="slack-setting-tab" data-bs-toggle="tab" data-bs-target="#slack-setting" type="button" role="tab" aria-controls="slack-setting" aria-selected="false">
                            <i class="fab fa-slack me-2"></i>{{ __('Slack') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="telegram-setting-tab" data-bs-toggle="tab" data-bs-target="#telegram-setting" type="button" role="tab" aria-controls="telegram-setting" aria-selected="false">
                            <i class="fab fa-telegram me-2"></i>{{ __('Telegram') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="twilio-setting-tab" data-bs-toggle="tab" data-bs-target="#twilio-setting" type="button" role="tab" aria-controls="twilio-setting" aria-selected="false">
                            <i class="fas fa-sms me-2"></i>{{ __('Twilio') }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="companySettingsTabContent">
                <!-- Business Settings Tab -->
                <div class="tab-pane fade show active" id="business-setting" role="tabpanel" aria-labelledby="business-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.1s">
                        <div class="premium-card-body">
                            <form action="{{ route('business.setting') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <!-- Logo Section -->
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="settings-section slide-in">
                                            <h5 class="settings-section-title">
                                                <i class="fas fa-image"></i>{{ __('Company Logo') }}
                                            </h5>
                                            <div class="logo-upload-area">
                                                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png') }}" class="logo-preview" alt="Company Logo" />
                                                <div>
                                                    <label for="company_logo" class="upload-btn">
                                                        <i class="fas fa-upload me-2"></i>{{ __('Choose Logo') }}
                                                        <input type="file" class="d-none" name="company_logo" id="company_logo" data-filename="edit-logo">
                                                    </label>
                                                    <p class="edit-logo mt-2 text-muted small"></p>
                                                    <p class="mt-2 text-muted small">{{ __('Recommended: 200x60px') }}</p>
                                                </div>
                                            </div>
                                            @error('company_logo')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Favicon Section -->
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="settings-section slide-in" style="animation-delay: 0.1s">
                                            <h5 class="settings-section-title">
                                                <i class="fas fa-star"></i>{{ __('Favicon') }}
                                            </h5>
                                            <div class="logo-upload-area">
                                                <img src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}" class="logo-preview" alt="Favicon" style="max-width: 64px; max-height: 64px;" />
                                                <div>
                                                    <label for="company_favicon" class="upload-btn">
                                                        <i class="fas fa-upload me-2"></i>{{ __('Choose Favicon') }}
                                                        <input type="file" class="d-none" name="company_favicon" id="company_favicon" data-filename="edit-favicon">
                                                    </label>
                                                    <p class="edit-favicon mt-2 text-muted small"></p>
                                                    <p class="mt-2 text-muted small">{{ __('Recommended: 32x32px') }}</p>
                                                </div>
                                            </div>
                                            @error('company_favicon')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- SEO Settings Section -->
                                    <div class="col-lg-4 col-md-12 mb-4">
                                        <div class="settings-section slide-in" style="animation-delay: 0.2s">
                                            <h5 class="settings-section-title">
                                                <i class="fas fa-search"></i>{{ __('SEO Settings') }}
                                            </h5>
                                            
                                            <div class="mb-3">
                                                <label for="title_text" class="form-label">
                                                    <i class="fas fa-heading me-2 text-primary"></i>{{ __('Title Text') }}
                                                </label>
                                                <input type="text" class="form-control" name="title_text" id="title_text" placeholder="{{ __('Enter page title') }}" value="{{ $settings['title_text'] ?? '' }}">
                                                @error('title_text')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="metakeyword" class="form-label">
                                                    <i class="fas fa-tags me-2 text-primary"></i>{{ __('Meta Keywords') }}
                                                </label>
                                                <textarea class="form-control" rows="3" name="metakeyword" id="metakeyword" placeholder="{{ __('Enter meta keywords separated by commas') }}">{{ $settings['metakeyword'] ?? '' }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="metadesc" class="form-label">
                                                    <i class="fas fa-align-left me-2 text-primary"></i>{{ __('Meta Description') }}
                                                </label>
                                                <textarea class="form-control" rows="3" name="metadesc" id="metadesc" placeholder="{{ __('Enter meta description') }}">{{ $settings['metadesc'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- System Settings Tab -->
                <div class="tab-pane fade" id="system-setting" role="tabpanel" aria-labelledby="system-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.2s">
                        <div class="premium-card-body">
                            <form action="{{ route('system.settings') }}" method="POST">
                                @csrf
                                <div class="settings-section">
                                    <h5 class="settings-section-title">
                                        <i class="fas fa-cogs"></i>{{ __('System Configuration') }}
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="site_currency" class="form-label">
                                                <i class="fas fa-coins me-2 text-primary"></i>{{ __('Currency *') }}
                                            </label>
                                            <input type="text" class="form-control" name="site_currency" id="site_currency" value="{{ $settings['site_currency'] ?? '' }}" placeholder="{{ __('Enter currency name') }}">
                                            @error('site_currency')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="site_currency_symbol" class="form-label">
                                                <i class="fas fa-dollar-sign me-2 text-primary"></i>{{ __('Currency Symbol *') }}
                                            </label>
                                            <input type="text" class="form-control" name="site_currency_symbol" id="site_currency_symbol" value="{{ $settings['site_currency_symbol'] ?? '' }}" placeholder="{{ __('Enter currency symbol') }}">
                                            @error('site_currency_symbol')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-align-left me-2 text-primary"></i>{{ __('Currency Symbol Position') }}
                                            </label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input type="radio" id="pre_symbol" name="site_currency_symbol_position" class="form-check-input" value="pre" @if ($settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                                    <label class="form-check-label" for="pre_symbol">{{ __('Before Amount') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" id="post_symbol" name="site_currency_symbol_position" class="form-check-input" value="post" @if ($settings['site_currency_symbol_position'] == 'post') checked @endif>
                                                    <label class="form-check-label" for="post_symbol">{{ __('After Amount') }}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="site_date_format" class="form-label">
                                                <i class="fas fa-calendar me-2 text-primary"></i>{{ __('Date Format') }}
                                            </label>
                                            <select name="site_date_format" class="form-select" id="site_date_format">
                                                <option value="M j, Y" @if ($settings['site_date_format'] == 'M j, Y') selected @endif>Jan 1, 2015</option>
                                                <option value="d-m-Y" @if ($settings['site_date_format'] == 'd-m-Y') selected @endif>01-01-2015</option>
                                                <option value="m-d-Y" @if ($settings['site_date_format'] == 'm-d-Y') selected @endif>01-01-2015</option>
                                                <option value="Y-m-d" @if ($settings['site_date_format'] == 'Y-m-d') selected @endif>2015-01-01</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="site_time_format" class="form-label">
                                                <i class="fas fa-clock me-2 text-primary"></i>{{ __('Time Format') }}
                                            </label>
                                            <select name="site_time_format" class="form-select" id="site_time_format">
                                                <option value="g:i A" @if ($settings['site_time_format'] == 'g:i A') selected @endif>10:30 PM</option>
                                                <option value="g:i a" @if ($settings['site_time_format'] == 'g:i a') selected @endif>10:30 pm</option>
                                                <option value="H:i" @if ($settings['site_time_format'] == 'H:i') selected @endif>22:30</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="employee_prefix" class="form-label">
                                                <i class="fas fa-user-tag me-2 text-primary"></i>{{ __('Employee Prefix') }}
                                            </label>
                                            <input type="text" class="form-control" name="employee_prefix" id="employee_prefix" value="{{ $settings['employee_prefix'] ?? '' }}" placeholder="{{ __('Enter employee prefix') }}">
                                            @error('employee_prefix')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Company Settings Tab -->
                <div class="tab-pane fade" id="company-setting" role="tabpanel" aria-labelledby="company-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.3s">
                        <div class="premium-card-body">
                            <form action="{{ route('company.settings') }}" method="POST">
                                @csrf
                                <div class="settings-section">
                                    <h5 class="settings-section-title">
                                        <i class="fas fa-building"></i>{{ __('Company Information') }}
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="company_name" class="form-label">
                                                <i class="fas fa-building me-2 text-primary"></i>{{ __('Company Name *') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_name" id="company_name" value="{{ $settings['company_name'] ?? '' }}" placeholder="{{ __('Enter company name') }}">
                                            @error('company_name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_address" class="form-label">
                                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ __('Address') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_address" id="company_address" value="{{ $settings['company_address'] ?? '' }}" placeholder="{{ __('Enter company address') }}">
                                            @error('company_address')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_city" class="form-label">
                                                <i class="fas fa-city me-2 text-primary"></i>{{ __('City') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_city" id="company_city" value="{{ $settings['company_city'] ?? '' }}" placeholder="{{ __('Enter city') }}">
                                            @error('company_city')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_state" class="form-label">
                                                <i class="fas fa-flag me-2 text-primary"></i>{{ __('State') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_state" id="company_state" value="{{ $settings['company_state'] ?? '' }}" placeholder="{{ __('Enter state') }}">
                                            @error('company_state')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_zipcode" class="form-label">
                                                <i class="fas fa-mail-bulk me-2 text-primary"></i>{{ __('Zip/Post Code') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_zipcode" id="company_zipcode" value="{{ $settings['company_zipcode'] ?? '' }}" placeholder="{{ __('Enter zip code') }}">
                                            @error('company_zipcode')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_country" class="form-label">
                                                <i class="fas fa-globe me-2 text-primary"></i>{{ __('Country') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_country" id="company_country" value="{{ $settings['company_country'] ?? '' }}" placeholder="{{ __('Enter country') }}">
                                            @error('company_country')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_telephone" class="form-label">
                                                <i class="fas fa-phone me-2 text-primary"></i>{{ __('Telephone') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_telephone" id="company_telephone" value="{{ $settings['company_telephone'] ?? '' }}" placeholder="{{ __('Enter phone number') }}">
                                            @error('company_telephone')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_email" class="form-label">
                                                <i class="fas fa-envelope me-2 text-primary"></i>{{ __('System Email *') }}
                                            </label>
                                            <input type="email" class="form-control" name="company_email" id="company_email" value="{{ $settings['company_email'] ?? '' }}" placeholder="{{ __('Enter system email') }}">
                                            @error('company_email')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="company_email_from_name" class="form-label">
                                                <i class="fas fa-user me-2 text-primary"></i>{{ __('Email From Name *') }}
                                            </label>
                                            <input type="text" class="form-control" name="company_email_from_name" id="company_email_from_name" value="{{ $settings['company_email_from_name'] ?? '' }}" placeholder="{{ __('Enter sender name') }}">
                                            @error('company_email_from_name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="timezone" class="form-label">
                                                <i class="fas fa-clock me-2 text-primary"></i>{{ __('Timezone') }}
                                            </label>
                                            <select name="timezone" class="form-select" id="timezone">
                                                <option value="">{{ __('Select Timezone') }}</option>
                                                @foreach ($timezones as $k => $timezone)
                                                    <option value="{{ $k }}" {{ env('TIMEZONE') == $k ? 'selected' : '' }}>{{ $timezone }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label for="company_start_time" class="form-label">
                                                <i class="fas fa-play me-2 text-primary"></i>{{ __('Start Time *') }}
                                            </label>
                                            <input type="time" class="form-control" name="company_start_time" id="company_start_time" value="{{ $settings['company_start_time'] ?? '' }}">
                                            @error('company_start_time')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label for="company_end_time" class="form-label">
                                                <i class="fas fa-stop me-2 text-primary"></i>{{ __('End Time *') }}
                                            </label>
                                            <input type="time" class="form-control" name="company_end_time" id="company_end_time" value="{{ $settings['company_end_time'] ?? '' }}">
                                            @error('company_end_time')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3 d-flex align-items-center">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="ip_restrict" id="ip_restrict" {{ $settings['ip_restrict'] == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ip_restrict">
                                                    <i class="fas fa-shield-alt me-2 text-primary"></i>{{ __('Enable IP Restriction') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
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
                <div class="tab-pane fade" id="email-setting" role="tabpanel" aria-labelledby="email-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.4s">
                        <div class="premium-card-body">
                            <div class="settings-section">
                                <h5 class="settings-section-title">
                                    <i class="fas fa-envelope"></i>{{ __('Email Notification Settings') }}
                                </h5>
                                
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Module') }}</th>
                                                <th class="text-center" width="150">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (\App\Models\Utility::$emailStatus as $key => $email)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-bell me-3 text-primary"></i>
                                                            <span class="fw-medium">{{ $email }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <label class="switch">
                                                            <input type="checkbox" class="email-template-checkbox"
                                                                name="{{ $key }}"
                                                                {{ \App\Models\Utility::getValByName("$key") == 1 ? 'checked' : '' }}
                                                                value="{{ \App\Models\Utility::getValByName("$key") == 1 ? '1' : '0' }}"
                                                                data-url="{{ route('company.email.setting', $key) }}">
                                                            <span class="slider1 round"></span>
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- IP Restriction Settings Tab -->
                <div class="tab-pane fade" id="ip-restrict-setting" role="tabpanel" aria-labelledby="ip-restrict-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.5s">
                        <div class="premium-card-body">
                            <div class="settings-section">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="settings-section-title mb-0">
                                        <i class="fas fa-shield-alt"></i>{{ __('IP Restriction Settings') }}
                                    </h5>
                                    <a href="#" data-url="{{ route('create.ip') }}" class="btn btn-premium" data-ajax-popup="true" data-title="{{ __('Create New IP') }}">
                                        <i class="fas fa-plus me-2"></i>{{ __('Add IP Address') }}
                                    </a>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <i class="fas fa-network-wired me-2 text-primary"></i>{{ __('IP Address') }}
                                                </th>
                                                <th class="text-center" width="150">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($ips as $ip)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-light rounded p-2 me-3">
                                                                <i class="fas fa-globe text-primary"></i>
                                                            </div>
                                                            <span class="fw-medium">{{ $ip->ip }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @can('Manage Company Settings')
                                                            <a href="#" data-url="{{ route('edit.ip', $ip->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit IP') }}" class="edit-icon me-2" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="#" class="delete-icon" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $ip->id }}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            <form method="POST" action="{{ route('destroy.ip', $ip->id) }}" id="delete-form-{{ $ip->id }}" class="d-none">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-info-circle me-2"></i>{{ __('No IP addresses configured') }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zoom Settings Tab -->
                <div class="tab-pane fade" id="zoom-setting" role="tabpanel" aria-labelledby="zoom-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.6s">
                        <div class="premium-card-body">
                            <form action="{{ route('zoom.settings') }}" method="POST">
                                @csrf
                                <div class="settings-section">
                                    <h5 class="settings-section-title">
                                        <i class="fas fa-video"></i>{{ __('Zoom Integration Settings') }}
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="zoom_apikey" class="form-label">
                                                <i class="fas fa-key me-2 text-primary"></i>{{ __('Zoom API Key') }}
                                            </label>
                                            <input type="text" class="form-control" name="zoom_apikey" id="zoom_apikey" placeholder="{{ __('Enter Zoom API Key') }}" value="{{ $settings['zoom_apikey'] ?? '' }}">
                                            @error('zoom_api_key')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="zoom_secret_key" class="form-label">
                                                <i class="fas fa-lock me-2 text-primary"></i>{{ __('Zoom Secret Key') }}
                                            </label>
                                            <input type="password" class="form-control" name="zoom_secret_key" id="zoom_secret_key" placeholder="{{ __('Enter Zoom Secret Key') }}" value="{{ $settings['zoom_secret_key'] ?? '' }}">
                                            @error('zoom_secret_key')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        {{ __('Configure your Zoom API credentials to enable video conferencing features in the system.') }}
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Slack Settings Tab -->
                <div class="tab-pane fade" id="slack-setting" role="tabpanel" aria-labelledby="slack-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.7s">
                        <div class="premium-card-body">
                            <form action="{{ route('slack.setting') }}" method="POST">
                                @csrf
                                <div class="settings-section">
                                    <h5 class="settings-section-title">
                                        <i class="fab fa-slack"></i>{{ __('Slack Integration Settings') }}
                                    </h5>
                                    
                                    <div class="mb-4">
                                        <label for="slack_webhook" class="form-label">
                                            <i class="fas fa-link me-2 text-primary"></i>{{ __('Slack Webhook URL') }}
                                        </label>
                                        <input type="url" class="form-control" name="slack_webhook" id="slack_webhook" placeholder="{{ __('Enter Slack Webhook URL') }}" value="{{ $settings['slack_webhook'] ?? '' }}" required>
                                        <small class="text-muted">{{ __('Configure your Slack webhook URL to receive notifications') }}</small>
                                    </div>

                                    <h6 class="mb-3">{{ __('Notification Modules') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="monthly_payslip_notification" id="monthly_payslip_notification" value="1" {{ isset($settings['monthly_payslip_notification']) && $settings['monthly_payslip_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="monthly_payslip_notification">{{ __('Monthly Payslip Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="award_notificaation" id="award_notificaation" value="1" {{ isset($settings['award_notificaation']) && $settings['award_notificaation'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="award_notificaation">{{ __('Award Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="Announcement_notification" id="Announcement_notification" value="1" {{ isset($settings['Announcement_notification']) && $settings['Announcement_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Announcement_notification">{{ __('Announcement Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="Holiday_notification" id="Holiday_notification" value="1" {{ isset($settings['Holiday_notification']) && $settings['Holiday_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Holiday_notification">{{ __('Holiday Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="meeting_notification" id="meeting_notification" value="1" {{ isset($settings['meeting_notification']) && $settings['meeting_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="meeting_notification">{{ __('Meeting Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="company_policy_notification" id="company_policy_notification" value="1" {{ isset($settings['company_policy_notification']) && $settings['company_policy_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="company_policy_notification">{{ __('Company Policy Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="ticket_notification" id="ticket_notification" value="1" {{ isset($settings['ticket_notification']) && $settings['ticket_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ticket_notification">{{ __('Ticket Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="event_notification" id="event_notification" value="1" {{ isset($settings['event_notification']) && $settings['event_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="event_notification">{{ __('Event Create') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Telegram Settings Tab -->
                <div class="tab-pane fade" id="telegram-setting" role="tabpanel" aria-labelledby="telegram-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.8s">
                        <div class="premium-card-body">
                            <form action="{{ route('telegram.setting') }}" method="POST">
                                @csrf
                                <div class="settings-section">
                                    <h5 class="settings-section-title">
                                        <i class="fab fa-telegram"></i>{{ __('Telegram Integration Settings') }}
                                    </h5>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="telegram_accestoken" class="form-label">
                                                <i class="fas fa-key me-2 text-primary"></i>{{ __('Telegram Access Token') }}
                                            </label>
                                            <input type="text" class="form-control" name="telegram_accestoken" id="telegram_accestoken" placeholder="{{ __('Enter Telegram Access Token') }}" value="{{ $settings['telegram_accestoken'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="telegram_chatid" class="form-label">
                                                <i class="fas fa-comments me-2 text-primary"></i>{{ __('Telegram Chat ID') }}
                                            </label>
                                            <input type="text" class="form-control" name="telegram_chatid" id="telegram_chatid" placeholder="{{ __('Enter Telegram Chat ID') }}" value="{{ $settings['telegram_chatid'] ?? '' }}">
                                        </div>
                                    </div>

                                    <h6 class="mb-3">{{ __('Notification Modules') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_monthly_payslip_notification" id="telegram_monthly_payslip_notification" value="1" {{ isset($settings['telegram_monthly_payslip_notification']) && $settings['telegram_monthly_payslip_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_monthly_payslip_notification">{{ __('Monthly Payslip Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_award_notification" id="telegram_award_notification" value="1" {{ isset($settings['telegram_award_notification']) && $settings['telegram_award_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_award_notification">{{ __('Award Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_Announcement_notification" id="telegram_Announcement_notification" value="1" {{ isset($settings['telegram_Announcement_notification']) && $settings['telegram_Announcement_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_Announcement_notification">{{ __('Announcement Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_Holiday_notification" id="telegram_Holiday_notification" value="1" {{ isset($settings['telegram_Holiday_notification']) && $settings['telegram_Holiday_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_Holiday_notification">{{ __('Holiday Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_meeting_notification" id="telegram_meeting_notification" value="1" {{ isset($settings['telegram_meeting_notification']) && $settings['telegram_meeting_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_meeting_notification">{{ __('Meeting Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_company_policy_notification" id="telegram_company_policy_notification" value="1" {{ isset($settings['telegram_company_policy_notification']) && $settings['telegram_company_policy_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_company_policy_notification">{{ __('Company Policy Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_ticket_notification" id="telegram_ticket_notification" value="1" {{ isset($settings['telegram_ticket_notification']) && $settings['telegram_ticket_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_ticket_notification">{{ __('Ticket Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="telegram_event_notification" id="telegram_event_notification" value="1" {{ isset($settings['telegram_event_notification']) && $settings['telegram_event_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="telegram_event_notification">{{ __('Event Create') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-premium">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Twilio Settings Tab -->
                <div class="tab-pane fade" id="twilio-setting" role="tabpanel" aria-labelledby="twilio-setting-tab">
                    <div class="premium-card fade-in" style="animation-delay: 0.9s">
                        <div class="premium-card-body">
                            <form action="{{ route('twilio.setting') }}" method="POST">
                                @csrf
                                <div class="settings-section">
                                    <h5 class="settings-section-title">
                                        <i class="fas fa-sms"></i>{{ __('Twilio SMS Integration Settings') }}
                                    </h5>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-4 mb-3">
                                            <label for="twilio_sid" class="form-label">
                                                <i class="fas fa-key me-2 text-primary"></i>{{ __('Twilio SID') }}
                                            </label>
                                            <input type="text" class="form-control" name="twilio_sid" id="twilio_sid" placeholder="{{ __('Enter Twilio SID') }}" value="{{ $settings['twilio_sid'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="twilio_token" class="form-label">
                                                <i class="fas fa-lock me-2 text-primary"></i>{{ __('Twilio Token') }}
                                            </label>
                                            <input type="password" class="form-control" name="twilio_token" id="twilio_token" placeholder="{{ __('Enter Twilio Token') }}" value="{{ $settings['twilio_token'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="twilio_from" class="form-label">
                                                <i class="fas fa-phone me-2 text-primary"></i>{{ __('Twilio From Number') }}
                                            </label>
                                            <input type="text" class="form-control" name="twilio_from" id="twilio_from" placeholder="{{ __('Enter Twilio From Number') }}" value="{{ $settings['twilio_from'] ?? '' }}">
                                        </div>
                                    </div>

                                    <h6 class="mb-3">{{ __('SMS Notification Modules') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="twilio_payslip_notification" id="twilio_payslip_notification" value="1" {{ isset($settings['twilio_payslip_notification']) && $settings['twilio_payslip_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="twilio_payslip_notification">{{ __('Payslip Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="twilio_leave_approve_notification" id="twilio_leave_approve_notification" value="1" {{ isset($settings['twilio_leave_approve_notification']) && $settings['twilio_leave_approve_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="twilio_leave_approve_notification">{{ __('Leave Approve/Reject') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="twilio_award_notification" id="twilio_award_notification" value="1" {{ isset($settings['twilio_award_notification']) && $settings['twilio_award_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="twilio_award_notification">{{ __('Award Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="twilio_trip_notification" id="twilio_trip_notification" value="1" {{ isset($settings['twilio_trip_notification']) && $settings['twilio_trip_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="twilio_trip_notification">{{ __('Trip Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="twilio_announcement_notification" id="twilio_announcement_notification" value="1" {{ isset($settings['twilio_announcement_notification']) && $settings['twilio_announcement_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="twilio_announcement_notification">{{ __('Announcement Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="twilio_ticket_notification" id="twilio_ticket_notification" value="1" {{ isset($settings['twilio_ticket_notification']) && $settings['twilio_ticket_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="twilio_ticket_notification">{{ __('Ticket Create') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="twilio_event_notification" id="twilio_event_notification" value="1" {{ isset($settings['twilio_event_notification']) && $settings['twilio_event_notification'] == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="twilio_event_notification">{{ __('Event Create') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
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
