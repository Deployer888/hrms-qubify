@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Employee') }}
@endsection
@push('css-page')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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

    .btn-create {
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

    .btn-create:hover {
        background: linear-gradient(45deg, #ee5a24, #ff6b6b);
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 6px 20px rgba(238, 90, 36, 0.4);
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

    /* HTML5 date inputs have native styling */
    input[type="date"] {
        cursor: pointer;
    }

    .timepicker {
        cursor: pointer;
        padding-right: 40px;
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 16px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'/%3E%3C/svg%3E");
    }

    .timepicker:focus {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%232563eb'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'/%3E%3C/svg%3E");
    }

    /* HTML5 date input styling */
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        color: var(--primary);
    }

    input[type="date"]:focus::-webkit-calendar-picker-indicator {
        color: var(--primary-dark);
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

    /* Employee info styles for read-only view */
    .employee-detail-edit-body .info {
        padding: 12px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .employee-detail-edit-body .info:hover {
        background: rgba(37, 99, 235, 0.02);
        padding-left: 8px;
        margin-left: -8px;
        margin-right: -8px;
        padding-right: 8px;
    }
    
    .employee-detail-edit-body .info:last-child {
        border-bottom: none;
    }
    
    .employee-detail-edit-body .info strong {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        color: var(--text-secondary);
    }
    
    .employee-detail-edit-body .info span {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.4;
        color: var(--text-primary);
    }
    
    .card-body.employee-detail-edit-body {
        padding: 1.5rem;
    }
    
    .employee-detail-edit-body .row {
        margin: 0;
    }
    
    .employee-detail-edit-body .col-md-6 {
        padding: 0 10px;
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
                <i class="fas fa-user-edit"></i>
            </div>
            <div class="ml-3">
                <h1 class="page-title-compact">{{ __('Edit Employee') }}</h1>
                <p class="page-subtitle-compact">{{ __('Update employee information and details') }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form action="{{ route('employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                </div>
                <div class="card-body employee-detail-edit-body">
                    <div class="row">
                        <!-- Name input -->
                        <div class="form-group col-md-6">
                            <label for="name" class="form-control-label">{{ __('Name') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required
                                value="{{ $employee->name }}">
                        </div>
                        <!-- Phone input -->
                        <div class="form-group col-md-6">
                            <label for="phone" class="form-control-label">{{ __('Phone') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ $employee->phone }}">
                        </div>
                        <!-- DOB input -->
                        <div class="form-group col-md-6">
                            <label for="dob" class="form-control-label">{{ __('Date of Birth') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="date" name="dob" id="dob" class="form-control"
                                value="{{ !empty($employee->dob) ? date('Y-m-d', strtotime($employee->dob)) : '' }}" max="{{ date('Y-m-d') }}">
                        </div>
                        @if (\Auth::user()->type != 'employee')
                        <!-- Personal Email input -->
                        <div class="form-group col-md-6">
                            <label for="personal_email" class="form-control-label">{{ __('Personal Email') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <input type="email" name="personal_email" id="personal_email" class="form-control"
                                value="{{ $employee->user->personal_email }}">
                        </div>
                        @endif
                        <!-- Gender radio buttons -->
                        <div class="form-group col-md-6">
                            <label class="form-control-label">{{ __('Gender') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <div class="d-flex radio-check">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="g_male" value="Male" name="gender"
                                        class="custom-control-input" {{ $employee->gender == 'Male' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="g_male">{{ __('Male') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="g_female" value="Female" name="gender"
                                        class="custom-control-input" {{ $employee->gender == 'Female' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="g_female">{{ __('Female') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="address" class="form-control-label">{{ __('Address') }}<span
                                    class="text-danger pl-1">*</span></label>
                            <textarea name="address" id="address" class="form-control" rows="2">{{ $employee->address }}</textarea>
                        </div>
                    </div>
         
                    <!-- Address input -->
                    {{-- @if (($allRoles && $allRoles->pluck('name')->contains('employee')) || \Auth::user()->type == 'employee') --}}
                        <input type="submit" value="Update" class="btn-create btn-xs float-right">
                    {{-- @endif --}}
                </div>
            </div>
        </div>
        @if (\Auth::user()->type != 'employee')
            <div class="col-md-6">
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        <div class="row">
                            @csrf
                            <!-- Employee ID input -->
                            <div class="form-group col-md-6">
                                <label for="employee_id" class="form-control-label">{{ __('Employee ID') }}</label>
                                <input type="text" name="employee_id" id="employee_id" class="form-control"
                                    value="{{ $employeesId }}" disabled>
                            </div>
                            <!-- Office select (Edit Mode) -->
                            <div class="form-group col-md-6">
                                <label for="office_id" class="form-control-label">{{ __('Office') }}</label>
                                <select name="office_id" id="office_id" class="form-control select2" required>
                                    <option value="">{{ __('Select Office') }}</option>
                                    @foreach ($offices as $id => $office)
                                        <option value="{{ $id }}"
                                            {{ old('office_id', $employee->office_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $office }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Branch select -->
                            <div class="form-group col-md-6">
                                <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                                <select name="branch_id" id="branch_id" class="form-control select2" required>
                                    @foreach ($branches as $id => $branch)
                                        <option value="{{ $id }}"
                                            {{ $employee->branch_id == $id ? 'selected' : '' }}>{{ $branch }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Department select -->
                            <div class="form-group col-md-6">
                                <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                                <select name="department_id" id="department_id" class="form-control select2" required>
                                    @foreach ($departments as $id => $department)
                                        <option value="{{ $id }}"
                                            {{ $employee->department_id == $id ? 'selected' : '' }}>{{ $department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Designation select -->
                            <div class="form-group col-md-6">
                                <label for="designation_id" class="form-control-label">{{ __('Designation') }}</label>
                                <select name="designation_id" id="designation_id" class="form-control select2"
                                    data-toggle="select2" data-placeholder="{{ __('Select Designation ...') }}">
                                    @foreach ($designations as $id => $designation)
                                        <option value="{{ $id }}"
                                            {{ $employee->designation_id == $id ? 'selected' : '' }}>{{ $designation }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Company Date of Joining input -->
                            <div class="form-group col-md-6">
                                <label for="company_doj"
                                    class="form-control-label">{{ __('Date Of Joining') }}</label>
                                <input type="date" name="company_doj" id="company_doj"
                                    class="form-control" required value="{{ !empty($employee->company_doj) ? date('Y-m-d', strtotime($employee->company_doj)) : '' }}">
                            </div>
                            <!-- Shift Start input -->
                            <div class="form-group col-md-6">
                                <label for="shift_start" class="form-control-label">{{ __('Shift Start') }}</label>
                                <input type="text" name="shift_start" id="shift_start"
                                    class="form-control timepicker" required value="{{ $employee->shift_start }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company_doj"
                                class="form-control-label mt-2">{{ __('Salary') }}</label>
                                <input type="number" name="salary" id="salary" class="form-control"
                                                required value="{{ $employee->salary }}">
                            </div>
                            
                            @php
                            $teamLeaderDetails = $employee->getTeamLeaderNameAndId();
                            @endphp
                            <div class="form-group col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="is_team_leader" class="form-control-label mt-2">{{ __('Team Leader') }}</label>
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" name="is_team_leader" id="is_team_leader"
                                            class="mx-2" value="1" {{ old('is_team_leader', $employee->is_team_leader) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="team_leader" class="form-control-label">{{ __('Select Team Leader') }}</label>
                                        <select name="team_leader" id="team_leader" class="form-control select2"
                                            required disabled>
                                            <option value="">{{ !empty($teamLeaderDetails) ? $teamLeaderDetails->name : '' }}</option>
                                        </select>
                                        <input type="hidden" name="team_leader" value="{{ !empty($teamLeaderDetails) ? $teamLeaderDetails->id : $employee->team_leader_id }}" id="hidden_team_leader">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="date_of_exit" class="form-control-label">{{ __('Date Of Exit') }}</label>
                                <input type="date" name="date_of_exit" id="date_of_exit" class="form-control" value="{{ !empty($employee->date_of_exit) ? date('Y-m-d', strtotime($employee->date_of_exit)) : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-6">
                <div class="employee-detail-wrap">
                    <div class="card card-fluid">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('Company Detail') }}</h6>
                        </div>
                        <div class="card-body employee-detail-edit-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info mb-3">
                                        <strong class="d-block text-muted small">{{ __('Branch') }}</strong>
                                        <span class="text-dark">{{ !empty($employee->branch) ? $employee->branch->name : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info mb-3">
                                        <strong class="d-block text-muted small">{{ __('Department') }}</strong>
                                        <span class="text-dark">{{ !empty($employee->department) ? $employee->department->name : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info mb-3">
                                        <strong class="d-block text-muted small">{{ __('Designation') }}</strong>
                                        <span class="text-dark">{{ !empty($employee->designation) ? $employee->designation->name : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info mb-3">
                                        <strong class="d-block text-muted small">{{ __('Date Of Joining') }}</strong>
                                        <span class="text-dark">{{ \Auth::user()->dateFormat($employee->company_doj) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info mb-3">
                                        <strong class="d-block text-muted small">{{ __('Shift Start') }}</strong>
                                        <span class="text-dark">{{ $employee->shift_start ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
        <div class="row">
            <div class="col-md-6">
            @if (\Auth::user()->type != 'employee')
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Document') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        @php
                            $employeedoc = $employee->documents()->pluck('document_value', __('document_id'));
                        @endphp

                        @foreach ($documents as $key => $document)
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="float-left col-4">
                                        <label for="document"
                                            class="float-left pt-1 form-control-label">{{ $document->name }} @if ($document->is_required == 1)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="float-right col-8">
                                        <input type="hidden" name="emp_doc_id[{{ $document->id }}]" id=""
                                            value="{{ $document->id }}">
                                        <div class="choose-file form-group">
                                            <label for="document[{{ $document->id }}]">
                                                <div>{{ __('Choose File') }}</div>
                                                <input
                                                    class="form-control @if (!empty($employeedoc[$document->id])) float-left @endif @error('document') is-invalid @enderror border-0"
                                                    @if ($document->is_required == 1 && empty($employeedoc[$document->id])) required @endif
                                                    name="document[{{ $document->id }}]" type="file"
                                                    id="document[{{ $document->id }}]"
                                                    data-filename="{{ $document->id . '_filename' }}">
                                            </label>
                                            <p class="{{ $document->id . '_filename' }}"></p>
                                        </div>

                                        @if (!empty($employeedoc[$document->id]))
                                            <br> <span class="text-xs"><a
                                                    href="{{ !empty($employeedoc[$document->id]) ? asset(Storage::url('uploads/document')) . '/' . $employeedoc[$document->id] : '' }}"
                                                    target="_blank">{{ !empty($employeedoc[$document->id]) ? $employeedoc[$document->id] : '' }}</a>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            </div>
            <div class="col-md-6">
                @if (($allRoles && $allRoles->pluck('name')->contains('hr')) || (($allRoles && $allRoles->pluck('name')->contains('employee')) &&
                (empty($employee->account_holder_name) || 
                 empty($employee->account_number) || 
                 empty($employee->bank_name) || 
                 empty($employee->bank_identifier_code) || 
                 empty($employee->branch_location) || 
                 empty($employee->tax_payer_id))))
                <div class="card card-fluid">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="account_holder_name" class="form-control-label">Account Holder Name</label>
                                <input type="text" name="account_holder_name" id="account_holder_name"
                                    class="form-control" value="{{ old('account_holder_name', $employee->account_holder_name) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="account_number" class="form-control-label">Account Number</label>
                                <input type="number" name="account_number" id="account_number" class="form-control"
                                    value="{{ old('account_number', $employee->account_number) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bank_name" class="form-control-label">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control"
                                    value="{{ old('bank_name', $employee->bank_name) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bank_identifier_code" class="form-control-label">Bank IFSC Code</label>
                                <input type="text" name="bank_identifier_code" id="bank_identifier_code"
                                    class="form-control" value="{{ old('bank_identifier_code', $employee->bank_identifier_code) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="branch_location" class="form-control-label">Branch Location</label>
                                <input type="text" name="branch_location" id="branch_location" class="form-control"
                                    value="{{ old('branch_location', $employee->branch_location) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tax_payer_id" class="form-control-label">PAN Number</label>
                                <input type="text" name="tax_payer_id" id="tax_payer_id" class="form-control"
                                    value="{{ old('tax_payer_id', $employee->tax_payer_id) }}">
                            </div>
                        </div>

                    </div>
                </div>
                @endif
            </div>
        </div>

    @if (($allRoles && $allRoles->pluck('name')->contains('hr')) || (($allRoles && $allRoles->pluck('name')->contains('employee')) && 
        (empty($employee->account_holder_name) || 
         empty($employee->account_number) || 
         empty($employee->bank_name) || 
         empty($employee->bank_identifier_code) || 
         empty($employee->branch_location) || 
         empty($employee->tax_payer_id))))
    <div class="row">
        <div class="col-12">
            <input type="submit" value="{{ __('Update') }}"
                class="btn-create btn-xs float-right">
        </div>
    </div>
    @endif
    </form>
@endsection

@push('script-page')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        function getDesignation(did) {
            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append(
                        '<option value="">Select Designation</option>');
                    $.each(data, function(key, value) {
                        var select = '';
                        if (key == '{{ $employee->designation_id }}') {
                            select = 'selected';
                        }

                        $('#designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }

        $(document).ready(function() {
            var d_id = $('#department_id').val();
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });
        
        $(document).ready(function() {
            console.log('Using HTML5 date inputs - no additional JavaScript needed');
            // HTML5 date inputs work natively, no complex initialization required
        });
        
        // Initialize timepicker
        $(document).ready(function(){
            $('.timepicker').timepicker({
                timeFormat: 'g:i A',
                interval: 15,
                minTime: '06:00',
                maxTime: '23:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
        });
    </script>
@endpush