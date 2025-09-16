@extends('layouts.admin')
@section('page-title')
    {{ __('Settings') }}
@endsection
@php
    $logo = asset(Storage::url('uploads/logo/'));
    $company_logo = Utility::getValByName('company_logo');
    $company_small_logo = Utility::getValByName('company_small_logo');
    $company_favicon = Utility::getValByName('company_favicon');
@endphp

@push('script-page')
    <script>
        $(document).on('change', '.email-template-checkbox', function() {
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {

                },
            });
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <section class="nav-tabs">
                <div class="col-lg-12 our-system">
                    <div class="row">
                        <ul class="nav nav-tabs my-4">
                            <li>
                                <a data-toggle="tab" class="active" id="contact-tab4"
                                    href="#business-setting">{{ __('Business Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="contact-tab4" href="#system-setting">{{ __('System Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="profile-tab3"
                                    href="#company-setting">{{ __('Company Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="profile-tab2"
                                    href="#email-setting">{{ __('Email Notification Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="profile-tab2"
                                    href="#ip-restrict-setting">{{ __('Ip Restrict Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="profile-tab2" href="#zoom-setting">{{ __('Zoom Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="profile-tab2" href="#slack-setting">{{ __('Slack Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="profile-tab2"
                                    href="#telegram-setting">{{ __('Telegram Setting') }}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" id="profile-tab2" href="#twilio-setting">{{ __('Twilio Setting') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="business-setting">
                        <form action="{{ route('business.setting') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                                    <h4 class="h4 font-weight-400 float-left pb-2">{{ __('Business settings') }}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-sm-6 col-md-6">
                                    <h4 class="small-title">{{ __('Logo') }}</h4>
                                    <div class="card setting-card setting-logo-box">
                                        <div class="logo-content">
                                            <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png') }}"
                                                class="big-logo">
                                        </div>
                                        <div class="choose-file mt-5">
                                            <label for="company_logo">
                                                <div>{{ __('Choose file here') }}</div>
                                                <input type="file" class="form-control" name="company_logo"
                                                    id="company_logo" data-filename="edit-logo">
                                            </label>
                                            <p class="edit-logo"></p>
                                        </div>
                                        @error('company_logo')
                                            <span class="invalid-logo" role="alert">
                                                <small class="text-danger">{{ $message }}</small>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-6 col-md-6">
                                    <h4 class="small-title">{{ __('Favicon') }}</h4>
                                    <div class="card setting-card setting-logo-box">
                                        <div class="logo-content">
                                            <img src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}"
                                                class="small-logo">
                                        </div>
                                        <div class="choose-file mt-5">
                                            <label for="company_favicon">
                                                <div>{{ __('Choose file here') }}</div>
                                                <input type="file" class="form-control" name="company_favicon"
                                                    id="company_favicon" data-filename="edit-favicon">
                                            </label>
                                            <p class="edit-favicon"></p>
                                        </div>
                                        @error('company_favicon')
                                            <span class="invalid-logo" role="alert">
                                                <small class="text-danger">{{ $message }}</small>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-6 col-md-6">
                                    <h4 class="small-title">{{ __('Settings') }}</h4>
                                    <div class="card setting-card setting-logo-box">
                                        <div class="form-group">
                                            <label for="title_text"
                                                class="form-control-label text-dark">{{ __('Title Text') }}</label>
                                            <input type="text" class="form-control" name="title_text" id="title_text"
                                                placeholder="{{ __('Title Text') }}"
                                                value="{{ $settings['title_text'] ?? '' }}">
                                            @error('title_text')
                                                <span class="invalid-title_text" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <label for="metakeyword" class="form-control-label text-dark">Meta
                                                Keywords</label>
                                            <textarea class="form-control" rows="4" name="metakeyword" id="metakeyword"
                                                style="resize: vertical; height: 100px;">{{ $settings['metakeyword'] ?? '' }}</textarea>
                                        </div>

                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <label for="metadesc" class="form-control-label text-dark">Meta
                                                Description</label>
                                            <textarea class="form-control" rows="4" name="metadesc" id="metadesc"
                                                style="resize: vertical; height: 100px;">{{ $settings['metadesc'] ?? '' }}</textarea>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-12 text-right">
                                    <input type="submit" value="{{ __('Save Change') }}"
                                        class="btn-create btn-xs radius-10px badge-blue">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="system-setting">
                        <div class="col-md-12">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                                    <h4 class="h4 font-weight-400 float-left pb-2">{{ __('System Settings') }}</h4>
                                </div>
                            </div>
                            <div class="card bg-none p-4">
                                <form action="{{ route('system.settings') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="site_currency"
                                                class="form-control-label text-dark">{{ __('Currency *') }}</label>
                                            <input type="text" class="form-control font-style" name="site_currency"
                                                id="site_currency" value="{{ $settings['site_currency'] ?? '' }}">
                                            @error('site_currency')
                                                <span class="invalid-site_currency" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="site_currency_symbol"
                                                class="form-control-label text-dark">{{ __('Currency Symbol *') }}</label>
                                            <input type="text" class="form-control" name="site_currency_symbol"
                                                id="site_currency_symbol"
                                                value="{{ $settings['site_currency_symbol'] ?? '' }}">
                                            @error('site_currency_symbol')
                                                <span class="invalid-site_currency_symbol" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="site_currency_symbol_position"
                                                    class="form-control-label text-dark">{{ __('Currency Symbol Position') }}</label>
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-12">
                                                        <div class="d-flex radio-check">
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                <input type="radio" id="pre_symbol"
                                                                    name="site_currency_symbol_position"
                                                                    class="custom-control-input" value="pre"
                                                                    @if ($settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                                                <label class="custom-control-label"
                                                                    for="pre_symbol">{{ __('Pre') }}</label>
                                                            </div>
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                <input type="radio" id="post_symbol"
                                                                    name="site_currency_symbol_position"
                                                                    class="custom-control-input" value="post"
                                                                    @if ($settings['site_currency_symbol_position'] == 'post') checked @endif>
                                                                <label class="custom-control-label"
                                                                    for="post_symbol">{{ __('Post') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="site_date_format"
                                                class="form-control-label text-dark">{{ __('Date Format') }}</label>
                                            <select name="site_date_format" class="form-control select2"
                                                id="site_date_format">
                                                <option value="M j, Y" @if ($settings['site_date_format'] == 'M j, Y') selected @endif>
                                                    Jan 1,2015</option>
                                                <option value="d-m-Y" @if ($settings['site_date_format'] == 'd-m-Y') selected @endif>
                                                    d-m-y</option>
                                                <option value="m-d-Y" @if ($settings['site_date_format'] == 'm-d-Y') selected @endif>
                                                    m-d-y</option>
                                                <option value="Y-m-d" @if ($settings['site_date_format'] == 'Y-m-d') selected @endif>
                                                    y-m-d</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="site_time_format"
                                                class="form-control-label text-dark">{{ __('Time Format') }}</label>
                                            <select name="site_time_format" class="form-control select2"
                                                id="site_time_format">
                                                <option value="g:i A" @if ($settings['site_time_format'] == 'g:i A') selected @endif>
                                                    10:30 PM</option>
                                                <option value="g:i a" @if ($settings['site_time_format'] == 'g:i a') selected @endif>
                                                    10:30 pm</option>
                                                <option value="H:i" @if ($settings['site_time_format'] == 'H:i') selected @endif>
                                                    22:30</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="employee_prefix"
                                                class="form-control-label text-dark">{{ __('Employee Prefix') }}</label>
                                            <input type="text" class="form-control" name="employee_prefix"
                                                id="employee_prefix" value="{{ $settings['employee_prefix'] ?? '' }}">
                                            @error('employee_prefix')
                                                <span class="invalid-employee_prefix" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12 text-right">
                                            <input type="submit" value="{{ __('Save Change') }}"
                                                class="btn-create badge-blue">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="company-setting">
                        <div class="col-md-12">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                                    <h4 class="h4 font-weight-400 float-left pb-2">{{ __('Company Setting') }}</h4>
                                </div>
                            </div>
                            <div class="card bg-none p-4">
                                <form action="{{ route('company.settings') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="company_name"
                                                class="form-control-label text-dark">{{ __('Company Name *') }}</label>
                                            <input type="text" class="form-control font-style" name="company_name"
                                                id="company_name" value="{{ $settings['company_name'] ?? '' }}">
                                            @error('company_name')
                                                <span class="invalid-company_name" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_address"
                                                class="form-control-label text-dark">{{ __('Address') }}</label>
                                            <input type="text" class="form-control font-style" name="company_address"
                                                id="company_address" value="{{ $settings['company_address'] ?? '' }}">
                                            @error('company_address')
                                                <span class="invalid-company_address" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_city"
                                                class="form-control-label text-dark">{{ __('City') }}</label>
                                            <input type="text" class="form-control font-style" name="company_city"
                                                id="company_city" value="{{ $settings['company_city'] ?? '' }}">
                                            @error('company_city')
                                                <span class="invalid-company_city" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_state"
                                                class="form-control-label text-dark">{{ __('State') }}</label>
                                            <input type="text" class="form-control font-style" name="company_state"
                                                id="company_state" value="{{ $settings['company_state'] ?? '' }}">
                                            @error('company_state')
                                                <span class="invalid-company_state" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_zipcode"
                                                class="form-control-label text-dark">{{ __('Zip/Post Code') }}</label>
                                            <input type="text" class="form-control" name="company_zipcode"
                                                id="company_zipcode" value="{{ $settings['company_zipcode'] ?? '' }}">
                                            @error('company_zipcode')
                                                <span class="invalid-company_zipcode" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_country"
                                                class="form-control-label text-dark">{{ __('Country') }}</label>
                                            <input type="text" class="form-control font-style" name="company_country"
                                                id="company_country" value="{{ $settings['company_country'] ?? '' }}">
                                            @error('company_country')
                                                <span class="invalid-company_country" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_telephone"
                                                class="form-control-label text-dark">{{ __('Telephone') }}</label>
                                            <input type="text" class="form-control" name="company_telephone"
                                                id="company_telephone"
                                                value="{{ $settings['company_telephone'] ?? '' }}">
                                            @error('company_telephone')
                                                <span class="invalid-company_telephone" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_email"
                                                class="form-control-label text-dark">{{ __('System Email *') }}</label>
                                            <input type="text" class="form-control" name="company_email"
                                                id="company_email" value="{{ $settings['company_email'] ?? '' }}">
                                            @error('company_email')
                                                <span class="invalid-company_email" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_email_from_name"
                                                class="form-control-label text-dark">{{ __('Email (From Name) *') }}</label>
                                            <input type="text" class="form-control font-style"
                                                name="company_email_from_name" id="company_email_from_name"
                                                value="{{ $settings['company_email_from_name'] ?? '' }}">
                                            @error('company_email_from_name')
                                                <span class="invalid-company_email_from_name" role="alert">
                                                    <small class="text-danger">{{ $message }}</small>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="company_start_time"
                                                        class="form-control-label text-dark">{{ __('Company Start Time *') }}</label>
                                                    <input type="text" class="form-control timepicker_format"
                                                        name="company_start_time" id="company_start_time"
                                                        value="{{ $settings['company_start_time'] ?? '' }}">
                                                    @error('company_start_time')
                                                        <span class="invalid-company_start_time" role="alert">
                                                            <small class="text-danger">{{ $message }}</small>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="company_end_time"
                                                        class="form-control-label text-dark">{{ __('Company End Time *') }}</label>
                                                    <input type="text" class="form-control timepicker_format"
                                                        name="company_end_time" id="company_end_time"
                                                        value="{{ $settings['company_end_time'] ?? '' }}">
                                                    @error('company_end_time')
                                                        <span class="invalid-company_end_time" role="alert">
                                                            <small class="text-danger">{{ $message }}</small>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="timezone"
                                                class="form-control-label text-dark">{{ __('Timezone') }}</label>
                                            <select name="timezone" class="form-control select2" id="timezone">
                                                <option value="">{{ __('Select Timezone') }}</option>
                                                @foreach ($timezones as $k => $timezone)
                                                    <option value="{{ $k }}"
                                                        {{ env('TIMEZONE') == $k ? 'selected' : '' }}>{{ $timezone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 py-5">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="ip_restrict"
                                                    id="ip_restrict"
                                                    {{ $settings['ip_restrict'] == 'on' ? 'checked' : '' }}>
                                                <label class="custom-control-label form-control-label"
                                                    for="ip_restrict">{{ __('Ip Restrict') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right">
                                            <input type="submit" value="{{ __('Save Change') }}"
                                                class="btn-create badge-blue">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="email-setting">
                        <div class="col-md-12">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                                    <h4 class="h4 font-weight-400 float-left pb-2">{{ __('Email Notification Setting') }}
                                    </h4>
                                </div>
                            </div>
                            <div class="card bg-none ">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0 dataTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Module') }}</th>
                                                    <th class="text-right">{{ __('On/Off') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (\App\Models\Utility::$emailStatus as $key => $email)
                                                    <tr class="font-style odd" role="row">
                                                        <td class="sorting_1">{{ $email }}</td>
                                                        <td class="action text-right">
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
                    <div class="tab-pane" id="ip-restrict-setting">
                        <div class="col-md-12">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                                    <h4 class="h4 font-weight-400 float-left pb-2">{{ __('Ip Restrict Setting') }}</h4>
                                </div>
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0 text-right">
                                    <a href="#" data-url="{{ route('create.ip') }}"
                                        class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true"
                                        data-title="{{ __('Create New IP') }}">
                                        <i class="fa fa-plus"></i> {{ __('Create') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card bg-none ">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0 dataTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('IP') }}</th>
                                                    <th class="">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ips as $ip)
                                                    <tr class="font-style odd" role="row">
                                                        <td class="sorting_1">{{ $ip->ip }}</td>
                                                        <td class="">
                                                            @can('Manage Company Settings')
                                                                <a href="#" data-url="{{ route('edit.ip', $ip->id) }}"
                                                                    data-size="lg" data-ajax-popup="true"
                                                                    data-title="{{ __('Edit IP') }}" class="edit-icon"
                                                                    data-bs-toggle="tooltip"
                                                                    title="{{ __('Edit') }}"><i
                                                                        class="fas fa-pencil-alt"></i></a>
                                                            @endcan
                                                            @can('Manage Company Settings')
                                                                <a href="#" class="delete-icon" data-bs-toggle="tooltip"
                                                                    title="{{ __('Delete') }}"
                                                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="document.getElementById('delete-form-{{ $ip->id }}').submit();"><i
                                                                        class="fas fa-trash"></i></a>
                                                                <form method="POST"
                                                                    action="{{ route('destroy.ip', $ip->id) }}"
                                                                    id="delete-form-{{ $ip->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            @endcan
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

                    <div id="zoom-setting" class="tab-pane">
                        <div class="col-md-12">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                                    <h4 class="h4 font-weight-400 float-left pb-2">{{ __('Zoom settings') }}</h4>
                                </div>
                            </div>
                            <div class="card bg-none company-setting">
                                <form action="{{ route('zoom.settings') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="zoom_apikey"
                                                class="form-control-label">{{ __('Zoom API Key') }}</label>
                                            <input type="text" class="form-control" name="zoom_apikey"
                                                id="zoom_apikey" placeholder="{{ __('Enter Zoom API Key') }}"
                                                value="{{ $settings['zoom_apikey'] ?? '' }}">
                                            @error('zoom_api_key')
                                                <span class="invalid-zoom_api_key" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="zoom_secret_key"
                                                class="form-control-label">{{ __('Zoom Secret Key') }}</label>
                                            <input type="text" class="form-control" name="zoom_secret_key"
                                                id="zoom_secret_key" placeholder="{{ __('Enter Zoom Secret Key') }}"
                                                value="{{ $settings['zoom_secret_key'] ?? '' }}">
                                            @error('zoom_secret_key')
                                                <span class="invalid-zoom_secret_key" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-right">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn-submit text-white">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="slack-setting" role="tabpanel">
                        <div class="card-header bg-transparent p-0 pb-1">
                            <div class="row mb-2">
                                <div class="col my-auto">
                                    <h5>{{ __('Slack') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white">
                            <form action="{{ route('slack.setting') }}" method="POST" id="slack-setting"
                                class="d-contents">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="small-title shadow-none">{{ __('Slack Webhook URL') }}</h4>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control w-100" name="slack_webhook"
                                                id="slack_webhook" placeholder="{{ __('Enter Slack Webhook URL') }}"
                                                value="{{ $settings['slack_webhook'] ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4 mb-2">
                                        <h4 class="small-title">{{ __('Module Setting') }}</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Monthly payslip create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="monthly_payslip_notification"
                                                        id="monthly_payslip_notification" value="1"
                                                        {{ isset($settings['monthly_payslip_notification']) && $settings['monthly_payslip_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="monthly_payslip_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Award create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="award_notificaation" id="award_notificaation"
                                                        value="1"
                                                        {{ isset($settings['award_notificaation']) && $settings['award_notificaation'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="award_notificaation"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Announcement create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="Announcement_notification" id="Announcement_notification"
                                                        value="1"
                                                        {{ isset($settings['Announcement_notification']) && $settings['Announcement_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="Announcement_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Holidays create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="Holiday_notification" id="Holiday_notification"
                                                        value="1"
                                                        {{ isset($settings['Holiday_notification']) && $settings['Holiday_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="Holiday_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Meeting create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="meeting_notification" id="meeting_notification"
                                                        value="1"
                                                        {{ isset($settings['meeting_notification']) && $settings['meeting_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="meeting_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Company policy create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="company_policy_notification"
                                                        id="company_policy_notification" value="1"
                                                        {{ isset($settings['company_policy_notification']) && $settings['company_policy_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="company_policy_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Ticket create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="ticket_notification" id="ticket_notification"
                                                        value="1"
                                                        {{ isset($settings['ticket_notification']) && $settings['ticket_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="ticket_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Event create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="event_notification" id="event_notification" value="1"
                                                        {{ isset($settings['event_notification']) && $settings['event_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="event_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-right">
                                    <input type="submit" value="{{ __('Save Changes') }}"
                                        class="btn-submit text-white">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="telegram-setting" role="tabpanel">
                        <div class="card-header bg-transparent p-0 pb-1">
                            <div class="row mb-2">
                                <div class="col my-auto">
                                    <h5>{{ __('Telegram') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body bg-white">
                            <form action="{{ route('telegram.setting') }}" method="POST" id="telegram-setting"
                                class="d-contents">
                                @csrf
                                <div class="row">
                                    <div class="card-body pd-0">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="telegram_accestoken"
                                                    class="form-control-label mb-0">{{ __('Telegram AccessToken') }}</label>
                                                <input type="text" class="form-control" name="telegram_accestoken"
                                                    id="telegram_accestoken"
                                                    placeholder="{{ __('Enter Telegram AccessToken') }}"
                                                    value="{{ $settings['telegram_accestoken'] ?? '' }}">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="telegram_chatid"
                                                    class="form-control-label mb-0">{{ __('Telegram ChatID') }}</label>
                                                <input type="text" class="form-control" name="telegram_chatid"
                                                    id="telegram_chatid" placeholder="{{ __('Enter Telegram ChatID') }}"
                                                    value="{{ $settings['telegram_chatid'] ?? '' }}">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4 mb-2">
                                        <h4 class="small-title bg-white">{{ __('Module Setting') }}</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Monthly payslip create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_monthly_payslip_notification"
                                                        id="telegram_monthly_payslip_notification" value="1"
                                                        {{ isset($settings['telegram_monthly_payslip_notification']) && $settings['telegram_monthly_payslip_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_monthly_payslip_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Award create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_award_notification"
                                                        id="telegram_award_notification" value="1"
                                                        {{ isset($settings['telegram_award_notification']) && $settings['telegram_award_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_award_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Announcement create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_Announcement_notification"
                                                        id="telegram_Announcement_notification" value="1"
                                                        {{ isset($settings['telegram_Announcement_notification']) && $settings['telegram_Announcement_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_Announcement_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Holidays create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_Holiday_notification"
                                                        id="telegram_Holiday_notification" value="1"
                                                        {{ isset($settings['telegram_Holiday_notification']) && $settings['telegram_Holiday_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_Holiday_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Meeting create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_meeting_notification"
                                                        id="telegram_meeting_notification" value="1"
                                                        {{ isset($settings['telegram_meeting_notification']) && $settings['telegram_meeting_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_meeting_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Company policy create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_company_policy_notification"
                                                        id="telegram_company_policy_notification" value="1"
                                                        {{ isset($settings['telegram_company_policy_notification']) && $settings['telegram_company_policy_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_company_policy_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Ticket create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_ticket_notification"
                                                        id="telegram_ticket_notification" value="1"
                                                        {{ isset($settings['telegram_ticket_notification']) && $settings['telegram_ticket_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_ticket_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Event create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="telegram_event_notification"
                                                        id="telegram_event_notification" value="1"
                                                        {{ isset($settings['telegram_event_notification']) && $settings['telegram_event_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="telegram_event_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-right">
                                    <input type="submit" value="{{ __('Save Changes') }}"
                                        class="btn-submit text-white">
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="twilio-setting" role="tabpanel">
                        <div class="card-header bg-transparent p-0 pb-1">
                            <div class="row mb-2">
                                <div class="col my-auto">
                                    <h5>{{ __('Twilio') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body bg-white">
                            <form action="{{ route('twilio.setting') }}" method="POST" id="twilio-setting"
                                class="d-contents">
                                @csrf
                                <div class="row">

                                    <div class="card-body pd-0">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="twilio_sid"
                                                    class="form-control-label">{{ __('Twilio SID') }}</label>
                                                <input type="text" class="form-control" name="twilio_sid"
                                                    id="twilio_sid" placeholder="{{ __('Enter Twilio SID') }}"
                                                    value="{{ $settings['twilio_sid'] ?? '' }}">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="twilio_token"
                                                    class="form-control-label mb-0">{{ __('Twilio Token') }}</label>
                                                <input type="text" class="form-control" name="twilio_token"
                                                    id="twilio_token" placeholder="{{ __('Enter Twilio Token') }}"
                                                    value="{{ $settings['twilio_token'] ?? '' }}">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="twilio_from"
                                                    class="form-control-label mb-0">{{ __('Twilio From') }}</label>
                                                <input type="text" class="form-control" name="twilio_from"
                                                    id="twilio_from" placeholder="{{ __('Enter Twilio From') }}"
                                                    value="{{ $settings['twilio_from'] ?? '' }}">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4 mb-2">
                                        <h4 class="small-title bg-white">{{ __('Module Setting') }}</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Payslip create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="twilio_payslip_notification"
                                                        id="twilio_payslip_notification" value="1"
                                                        {{ isset($settings['twilio_payslip_notification']) && $settings['twilio_payslip_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="twilio_payslip_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Leave approve/reject') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="twilio_leave_approve_notification"
                                                        id="twilio_leave_approve_notification" value="1"
                                                        {{ isset($settings['twilio_leave_approve_notification']) && $settings['twilio_leave_approve_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="twilio_leave_approve_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Award create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="twilio_award_notification" id="twilio_award_notification"
                                                        value="1"
                                                        {{ isset($settings['twilio_award_notification']) && $settings['twilio_award_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="twilio_award_notification"></label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <span>{{ __('Trip create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="twilio_trip_notification" id="twilio_trip_notification"
                                                        value="1"
                                                        {{ isset($settings['twilio_trip_notification']) && $settings['twilio_trip_notification'] == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="twilio_trip_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Announcement create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="twilio_announcement_notification"
                                                        id="twilio_announcement_notification" value="1"
                                                        {{ isset($settings['twilio_announcement_notification']) && $settings['twilio_announcement_notification'] == '1' ? 'checked' : '' }}">
                                                    <label class="custom-control-label"
                                                        for="twilio_announcement_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Ticket create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="twilio_ticket_notification" id="twilio_ticket_notification"
                                                        value="1"
                                                        {{ isset($settings['twilio_ticket_notification']) && $settings['twilio_ticket_notification'] == '1' ? 'checked' : '' }}">
                                                    <label class="custom-control-label"
                                                        for="twilio_ticket_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span>{{ __('Event create') }}</span>
                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="twilio_event_notification" id="twilio_event_notification"
                                                        value="1"
                                                        {{ isset($settings['twilio_event_notification']) && $settings['twilio_event_notification'] == '1' ? 'checked' : '' }}">
                                                    <label class="custom-control-label"
                                                        for="twilio_event_notification"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-right">
                                    <input type="submit" value="{{ __('Save Changes') }}"
                                        class="btn-submit text-white">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </div>
@endsection
