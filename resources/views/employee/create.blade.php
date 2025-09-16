@extends('layouts.admin')

@section('page-title')
    {{__('Create Employee')}}
@endsection


@push('css-page')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css">
<style>
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #60a5fa;
        --dark: #1f2937;
        --light: #f8fafc;
        --border: #e5e7eb;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    body {
        background: #f8f9fa;
    }

    /* Page Header */
    .page-header-compact {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.95) 0%, 
            rgba(59, 130, 246, 0.95) 50%, 
            rgba(96, 165, 250, 0.95) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 32px 40px;
        margin-bottom: 32px;
        box-shadow: 
            0 32px 64px rgba(37, 99, 235, 0.3),
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .page-header-compact::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: 
            conic-gradient(from 0deg at 50% 50%, 
                transparent 0deg, 
                rgba(255, 255, 255, 0.1) 60deg, 
                transparent 120deg, 
                rgba(255, 255, 255, 0.05) 180deg, 
                transparent 240deg, 
                rgba(255, 255, 255, 0.1) 300deg, 
                transparent 360deg);
        animation: rotateBg 25s linear infinite;
        pointer-events: none;
    }

    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .page-title-compact {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: -0.025em;
    }

    .page-subtitle-compact {
        color: rgba(255, 255, 255, 0.9);
        margin: 6px 0 0 0;
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .header-icon {
        width: 72px;
        height: 72px;
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        backdrop-filter: blur(20px);
        box-shadow: 
            0 8px 32px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    /* Premium Card */
    .premium-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    .card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
        border-bottom: none;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .card-header h6 {
        font-weight: 700;
        color: #4a5568;
        font-size: 1.1rem;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.1);
        background: white;
        transform: translateY(-2px);
    }

    .form-control-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        letter-spacing: 0.3px;
    }

    .text-danger {
        color: var(--danger) !important;
        font-weight: 700;
    }

    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .custom-control-label::before {
        border-radius: 8px;
        border: 2px solid #dee2e6;
    }

    .custom-radio .custom-control-label::before {
        border-radius: 50%;
    }

    .select2-container--default .select2-selection--single {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        height: calc(1.5em + 1.5rem + 2px);
        padding: 0.375rem 0.75rem;
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.1);
    }

    /* Premium Button */
    .premium-btn {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .premium-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        color: white;
        text-decoration: none;
    }

    .premium-btn-primary {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
    }

    .premium-btn-primary:hover {
        background: linear-gradient(45deg, #ee5a24, #ff6b6b);
        transform: translateY(-2px);
        color: white;
    }

    .btn {
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .choose-file {
        position: relative;
    }

    .choose-file label {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #5c85ff 0%, #5c66ff 100%);
        color: white;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(92, 133, 255, 0.3);
    }

    .choose-file label:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(92, 133, 255, 0.4);
    }

    .choose-file input[type="file"] {
        display: none;
    }

    textarea.form-control {
        resize: none;
    }

    .datepicker, .maxDatepicker, .timepicker {
        background-image: linear-gradient(45deg, transparent 50%, var(--primary) 50%);
        background-position: calc(100% - 20px) calc(1em + 2px), calc(100% - 15px) calc(1em + 2px);
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
    }

    input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        position: relative;
        -webkit-appearance: none;
        appearance: none;
        background: #fff;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    input[type="checkbox"]:checked {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-color: transparent;
    }

    input[type="checkbox"]:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: bold;
        font-size: 14px;
    }

    .row {
        margin-left: -0.75rem;
        margin-right: -0.75rem;
    }

    .col-md-6, .col-12 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    @media (max-width: 768px) {
        .page-header-compact {
            padding: 1.5rem;
        }
        
        .card {
            margin-bottom: 1rem;
        }
        
        .btn {
            width: 100%;
            margin-top: 1rem;
        }
    }

    /* Loading animation for select2 */
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: var(--primary) transparent transparent transparent;
    }

    /* Enhanced focus states */
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }
</style>
@endpush
@section('content')
    <!-- Premium Header Section -->
    <div class="page-header-compact">
        <div class="header-content d-flex align-items-center">
            <div class="header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="ml-3">
                <h1 class="page-title-compact">{{ __('Create Employee') }}</h1>
                <p class="page-subtitle-compact">{{ __('Add a new team member to your organization') }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Personal Detail Card -->
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Name input -->
                                <div class="form-group col-md-6">
                                    <label for="name" class="form-control-label">{{ __('Name') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required
                                        value="{{ old('name') }}">
                                </div>
                                <!-- Phone input -->
                                <div class="form-group col-md-6">
                                    <label for="phone" class="form-control-label">{{ __('Phone') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        value="{{ old('phone') }}">
                                </div>
                                <!-- DOB input -->
                                <div class="form-group col-md-6">
                                    <label for="dob" class="form-control-label">{{ __('Date of Birth') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="date" name="dob" id="dob" class="form-control"
                                        value="{{ old('dob') }}" max="{{ date('Y-m-d') }}">
                                </div>
                                <!-- Email input -->
                                <div class="form-group col-md-6">
                                    <label for="email" class="form-control-label">{{ __('Official Email') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" required
                                        value="{{ old('email') }}">
                                </div>
                                <!-- Personal Email input -->
                                <div class="form-group col-md-6">
                                    <label for="personal_email" class="form-control-label">{{ __('Personal Email') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="email" name="personal_email" id="personal_email" class="form-control" required
                                        value="{{ old('personal_email') }}">
                                </div>
                                <!-- Password input -->
                                {{-- <div class="form-group col-md-6">
                                    <label for="password" class="form-control-label">{{ __('Password') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div> --}}
                                <!-- Gender radio buttons -->
                                <div class="form-group col-md-6">
                                    <label class="form-control-label">{{ __('Gender') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <div class="d-flex radio-check">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="g_male" value="Male" name="gender"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="g_male">{{ __('Male') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="g_female" value="Female" name="gender"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="g_female">{{ __('Female') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Address input -->
                                <div class="form-group col-md-6">
                                    <label for="address" class="form-control-label">{{ __('Address') }}<span
                                            class="text-danger pl-1">*</span></label>
                                    <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Company Detail Card -->
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            <div class="row">
                                <!-- Employee ID input -->
                                <div class="form-group col-md-6">
                                    <label for="employee_id" class="form-control-label">{{ __('Employee ID') }}</label>
                                    <input type="text" name="employee_id" id="employee_id" class="form-control"
                                        value="{{ $employeesId }}" disabled>
                                </div>
                                <!-- Office select -->
                                <div class="form-group col-md-6">
                                    <label for="office_id" class="form-control-label">{{ __('Office') }}</label>
                                    <select name="office_id" id="office_id" class="form-control select2" required>
                                        <option value="">{{ __('Select Office') }}</option>
                                        @foreach ($offices as $id => $office)
                                            <option value="{{ $id }}"
                                                {{ old('office_id') == $id ? 'selected' : '' }}>{{ $office }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Branch select -->
                                <div class="form-group col-md-6">
                                    <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                                    <select name="branch_id" id="branch_id" class="form-control select2" required>
                                        <option value="">{{ __('Select Branch') }}</option>
                                        @foreach ($branches as $id => $branch)
                                            <option value="{{ $id }}"
                                                {{ old('branch_id') == $id ? 'selected' : '' }}>{{ $branch }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Department select -->
                                <div class="form-group col-md-6">
                                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                                    <select name="department_id" id="department_id" class="form-control select2"
                                        required>
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach ($departments as $id => $department)
                                            <option value="{{ $id }}"
                                                {{ old('department_id') == $id ? 'selected' : '' }}>{{ $department }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Designation select -->
                                <div class="form-group col-md-6">
                                    <label for="designation_id"
                                        class="form-control-label">{{ __('Designation') }}</label>
                                    <select name="designation_id" id="designation_id"
                                        class="form-control select2-multiple select2" data-toggle="select2"
                                        data-placeholder="Select Designation">
                                        <option value="">Select Designation</option>
                                        <!-- Designation options will be filled dynamically -->
                                    </select>
                                </div>
                                <!-- Company Date of Joining input -->
                                <div class="form-group col-md-6">
                                    <label for="company_doj"
                                        class="form-control-label">{{ __('Date Of Joining') }}</label>
                                    <input type="date" name="company_doj" id="company_doj"
                                        class="form-control" required value="{{ old('company_doj') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="shift_start"
                                        class="form-control-label">{{ __('Shift Start') }}</label>
                                    <!-- <input type="text" name="shift_start" id="shift_start"
                                        class="form-control timepicker" required value="{{-- old('shift_start') --}}" readonly> -->
                                    <input type="time" name="shift_start" id="shift_start"
                                        class="form-control timepicker" required value="{{ old('shift_start') }}" placeholder="Select time">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="salary"
                                    class="form-control-label mt-2">{{ __('Salary') }}</label>
                                    <input type="number" name="salary" id="salary" class="form-control"
                                                required value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="is_team_leader" class="form-control-label mt-2">{{ __('Team Leader') }}</label>
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" name="is_team_leader" id="is_team_leader" class="mx-2" value="{{ old('is_team_leader') }}">
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-6">
                                            <label for="team_leader" class="form-control-label">{{ __('Select Team Leader') }}</label>
                                            <select name="team_leader" id="team_leader" class="form-control select2" required disabled>
                                                <option value="">{{ __('Select Team Leader') }}</option>
                                            </select>
                                            <input type="hidden" name="team_leader" id="hidden_team_leader">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Document Card -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Document') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            @foreach ($documents as $document)
                                <div class="row">
                                    <div class="form-group col-12">
                                        <div class="float-left col-4">
                                            <label for="document_{{ $document->id }}"
                                                class="float-left pt-1 form-control-label">
                                                {{ $document->name }}
                                                @if ($document->is_required == 1)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="float-right col-8">
                                            <input type="hidden" name="emp_doc_id[{{ $document->id }}]"
                                                value="{{ $document->id }}">
                                            <div class="choose-file form-group">
                                                <label for="document_{{ $document->id }}">
                                                    <div>{{ __('Choose File') }}</div>
                                                    <input type="file" name="document[{{ $document->id }}]"
                                                        id="document_{{ $document->id }}"
                                                        class="form-control @error('document') is-invalid @enderror border-0"
                                                        @if ($document->is_required == 1) required @endif
                                                        data-filename="{{ $document->id }}_filename">
                                                </label>
                                                <p class="{{ $document->id }}_filename"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Bank Account Detail Card -->
                <div class="col-md-6">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-create-body">
                            <div class="row">
                                <!-- Account Holder Name input -->
                                <div class="form-group col-md-6">
                                    <label for="account_holder_name"
                                        class="form-control-label">{{ __('Account Holder Name') }}</label>
                                    <input type="text" name="account_holder_name" id="account_holder_name"
                                        class="form-control" value="{{ old('account_holder_name') }}">
                                </div>
                                <!-- Account Number input -->
                                <div class="form-group col-md-6">
                                    <label for="account_number"
                                        class="form-control-label">{{ __('Account Number') }}</label>
                                    <input type="number" name="account_number" id="account_number" class="form-control"
                                        value="{{ old('account_number') }}">
                                </div>
                                <!-- Bank Name input -->
                                <div class="form-group col-md-6">
                                    <label for="bank_name" class="form-control-label">{{ __('Bank Name') }}</label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control"
                                        value="{{ old('bank_name') }}">
                                </div>
                                <!-- Bank Identifier Code input -->
                                <div class="form-group col-md-6">
                                    <label for="bank_identifier_code"
                                        class="form-control-label">{{ __('Bank Identifier Code') }}</label>
                                    <input type="text" name="bank_identifier_code" id="bank_identifier_code"
                                        class="form-control" value="{{ old('bank_identifier_code') }}">
                                </div>
                                <!-- Branch Location input -->
                                <div class="form-group col-md-6">
                                    <label for="branch_location"
                                        class="form-control-label">{{ __('Branch Location') }}</label>
                                    <input type="text" name="branch_location" id="branch_location"
                                        class="form-control" value="{{ old('branch_location') }}">
                                </div>
                                <!-- Tax Payer ID input -->
                                <div class="form-group col-md-6">
                                    <label for="tax_payer_id" class="form-control-label">{{ __('Tax Payer Id') }}</label>
                                    <input type="text" name="tax_payer_id" id="tax_payer_id" class="form-control"
                                        value="{{ old('tax_payer_id') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Submit button -->
            <div class="row">
                <div class="col-12">
                    <button type="submit"
                        class="btn premium-btn-primary float-right">{{ __('Create') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script-page')
<!-- Include JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>

<script>
    $(document).ready(function() {
        // Enhanced datepicker initialization with error handling
        /* function initializeDatePickers() {
            try {
                // Initialize DOB datepicker (no future dates)
                $('.datepicker').each(function() {
                    var $this = $(this);
                    try {
                        $this.datepicker({
                            dateFormat: 'yy-mm-dd',
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '1950:2030',
                            maxDate: 0, // Prevent future dates for DOB
                            showButtonPanel: true,
                            closeText: 'Clear',
                            onClose: function(dateText, inst) {
                                if (dateText === '') {
                                    $(this).val('');
                                }
                            }
                        });
                        
                        // Add click handler for readonly inputs
                        $this.on('click', function() {
                            if ($(this).hasClass('hasDatepicker')) {
                                $(this).datepicker('show');
                            }
                        });
                    } catch (e) {
                        console.error('Error initializing datepicker for:', $this.attr('id'), e);
                        // Fallback: remove readonly and allow manual input
                        $this.removeAttr('readonly').attr('placeholder', 'YYYY-MM-DD');
                    }
                });

                // Initialize Date of Joining datepicker (allow future dates)
                $('.maxDatepicker').each(function() {
                    var $this = $(this);
                    try {
                        $this.datepicker({
                            dateFormat: 'yyyy-mm-dd',
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '2000:2050',
                            showButtonPanel: true,
                            closeText: 'Clear',
                            onClose: function(dateText, inst) {
                                if (dateText === '') {
                                    $(this).val('');
                                }
                            }
                        });
                        
                        // Add click handler for readonly inputs
                        $this.on('click', function() {
                            if ($(this).hasClass('hasDatepicker')) {
                                $(this).datepicker('show');
                            }
                        });
                    } catch (e) {
                        console.error('Error initializing maxDatepicker for:', $this.attr('id'), e);
                        // Fallback: remove readonly and allow manual input
                        $this.removeAttr('readonly').attr('placeholder', 'YYYY-MM-DD');
                    }
                });
            } catch (e) {
                console.error('Error initializing datepickers:', e);
            }
        } */

        // Simple and direct timepicker initialization
        $('.timepicker').timepicker({
            timeFormat: 'g:i A',
            interval: 15,
            minTime: '06:00',
            maxTime: '23:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });

        // Enhanced Select2 initialization with error handling
        function initializeSelect2() {
            try {
                $('.select2').each(function() {
                    var $this = $(this);
                    try {
                        $this.select2({
                            placeholder: function() {
                                return $(this).data('placeholder') || 'Select an option';
                            },
                            allowClear: true,
                            width: '100%'
                        });
                    } catch (e) {
                        console.error('Error initializing Select2 for:', $this.attr('id'), e);
                    }
                });
            } catch (e) {
                console.error('Error initializing Select2:', e);
            }
        }

        // Initialize Select2 with delay
        setTimeout(function() {
            initializeSelect2();
        }, 200);

        // Initialize designation dropdown based on existing department
        var d_id = $('#department_id').val();
        if (d_id) {
            getDesignation(d_id);
        }

        // Handle office change
        $('#office_id').on('change', function() {
            var office_id = $(this).val();
            getDepartmentsByOffice(office_id);
        });

        // Handle branch change
        $('#branch_id').on('change', function() {
            var branch_id = $(this).val();
            getDepartmentsByBranch(branch_id);
            getTeamLeadersByBranch(branch_id);
        });

        // Handle department change
        $('#department_id').on('change', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
            updateTeamLeaders();
        });

        // Handle team leader checkbox
        $('#is_team_leader').on('change', function() {
            if ($(this).is(':checked')) {
                $('#team_leader').prop('disabled', true).val('').trigger('change');
            } else {
                $('#team_leader').prop('disabled', false);
            }
        });

        // Enhanced function to get departments by office
        function getDepartmentsByOffice(office_id) {
            if (!office_id) {
                resetDependentDropdowns(['department_id', 'designation_id', 'team_leader']);
                return;
            }
            
            showLoadingState('#department_id');
            
            $.ajax({
                url: '{{ route("employee.departments.by.office") }}',
                type: 'POST',
                data: {
                    "office_id": office_id,
                    "_token": "{{ csrf_token() }}",
                },
                timeout: 10000, // 10 second timeout
                success: function(data) {
                    try {
                        populateDropdown('#department_id', data, 'Select Department');
                        resetDependentDropdowns(['designation_id', 'team_leader']);
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Departments loaded successfully');
                        }
                    } catch (e) {
                        console.error('Error processing departments data:', e);
                        handleAjaxError(null, '#department_id', 'Error processing departments data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error fetching departments by office:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    handleAjaxError(xhr, '#department_id', 'Error loading departments. Please try again.');
                },
                complete: function() {
                    hideLoadingState('#department_id');
                }
            });
        }

        // Enhanced function to get departments by branch
        function getDepartmentsByBranch(branch_id) {
            if (!branch_id) {
                resetDependentDropdowns(['department_id', 'designation_id']);
                return;
            }
            
            showLoadingState('#department_id');
            
            $.ajax({
                url: '{{ route("employee.departments.by.branch") }}',
                type: 'POST',
                data: {
                    "branch_id": branch_id,
                    "_token": "{{ csrf_token() }}",
                },
                timeout: 10000, // 10 second timeout
                success: function(data) {
                    try {
                        populateDropdown('#department_id', data, 'Select Department');
                        resetDependentDropdowns(['designation_id']);
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Departments loaded successfully');
                        }
                    } catch (e) {
                        console.error('Error processing departments data:', e);
                        handleAjaxError(null, '#department_id', 'Error processing departments data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error fetching departments by branch:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    handleAjaxError(xhr, '#department_id', 'Error loading departments. Please try again.');
                },
                complete: function() {
                    hideLoadingState('#department_id');
                }
            });
        }

        // Enhanced function to get designations by department
        function getDesignation(department_id) {
            if (!department_id) {
                resetDependentDropdowns(['designation_id']);
                return;
            }
            
            showLoadingState('#designation_id');
            
            $.ajax({
                url: '{{ route("employee.json") }}',
                type: 'POST',
                data: {
                    "department_id": department_id,
                    "_token": "{{ csrf_token() }}",
                },
                timeout: 10000, // 10 second timeout
                success: function(data) {
                    try {
                        populateDropdown('#designation_id', data, 'Select Designation');
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Designations loaded successfully');
                        }
                    } catch (e) {
                        console.error('Error processing designations data:', e);
                        handleAjaxError(null, '#designation_id', 'Error processing designations data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error fetching designations:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    handleAjaxError(xhr, '#designation_id', 'Error loading designations. Please try again.');
                },
                complete: function() {
                    hideLoadingState('#designation_id');
                }
            });
        }

        // Function to get team leaders by branch
        function getTeamLeadersByBranch(branch_id) {
            if (branch_id && !$('#is_team_leader').is(':checked')) {
                $.ajax({
                    url: '{{ route("employee.team.leaders.by.branch") }}',
                    type: 'POST',
                    data: {
                        "branch_id": branch_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        $('#team_leader').empty();
                        $('#team_leader').append('<option value="">{{ __("Select Team Leader") }}</option>');
                        $.each(data, function(index, leader) {
                            $('#team_leader').append('<option value="' + leader.id + '">' + leader.name + '</option>');
                        });
                    },
                    error: function() {
                        console.log('Error fetching team leaders');
                    }
                });
            }
        }

        // Enhanced function to update team leaders based on branch and department
        function updateTeamLeaders() {
            var branch_id = $('#branch_id').val();
            var department_id = $('#department_id').val();
            
            if (branch_id && department_id && !$('#is_team_leader').is(':checked')) {
                showLoadingState('#team_leader');
                
                $.ajax({
                    url: '{{ route("employee.getTeamLeader") }}',
                    type: 'POST',
                    data: {
                        "branchId": branch_id,
                        "departmentId": department_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    timeout: 10000,
                    success: function(data) {
                        try {
                            $('#team_leader').empty();
                            $('#team_leader').append('<option value="">Select Team Leader</option>');
                            $.each(data, function(index, leader) {
                                $('#team_leader').append('<option value="' + leader.id + '">' + leader.name + '</option>');
                            });
                            
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Team leaders loaded successfully');
                            }
                        } catch (e) {
                            console.error('Error processing team leaders data:', e);
                            handleAjaxError(null, '#team_leader', 'Error processing team leaders data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error fetching team leaders:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                        handleAjaxError(xhr, '#team_leader', 'Error loading team leaders. Please try again.');
                    },
                    complete: function() {
                        hideLoadingState('#team_leader');
                    }
                });
            }
        }

        // Utility function to handle AJAX errors
        function handleAjaxError(xhr, targetElement, defaultMessage) {
            let errorMessage = defaultMessage;
            
            if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            } else if (xhr && xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else if (xhr && xhr.status === 500) {
                errorMessage = 'Server error. Please try again later.';
            } else if (xhr && xhr.status === 422) {
                errorMessage = 'Invalid data. Please check your selection.';
            }
            
            // Show user-friendly error
            if (typeof toastr !== 'undefined') {
                toastr.error(errorMessage);
            } else {
                alert(errorMessage);
            }
            
            // Reset dropdown to safe state
            $(targetElement).empty().append('<option value="">Select an option</option>');
        }

        // Utility function to show loading state
        function showLoadingState(selector) {
            var $element = $(selector);
            $element.prop('disabled', true);
            $element.empty().append('<option value="">Loading...</option>');
        }

        // Utility function to hide loading state
        function hideLoadingState(selector) {
            var $element = $(selector);
            $element.prop('disabled', false);
        }

        // Utility function to populate dropdown
        function populateDropdown(selector, data, placeholder) {
            var $element = $(selector);
            $element.empty();
            $element.append('<option value="">' + placeholder + '</option>');
            
            if (data && typeof data === 'object') {
                $.each(data, function(key, value) {
                    $element.append('<option value="' + key + '">' + value + '</option>');
                });
            }
        }

        // Utility function to reset dependent dropdowns
        function resetDependentDropdowns(selectors) {
            var placeholders = {
                'department_id': 'Select Department',
                'designation_id': 'Select Designation', 
                'team_leader': 'Select Team Leader'
            };
            
            selectors.forEach(function(selector) {
                var placeholder = placeholders[selector] || 'Select an option';
                $('#' + selector).empty().append('<option value="">' + placeholder + '</option>');
            });
        }
    });
</script>
@endpush