@extends('layouts.admin')

@section('page-title')
{{ __('Employee Details') }}
@endsection


@push('css-page')
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
        background: var(--light);
        min-height: 100vh;
    }

    .content-wrapper {
        background: transparent;
    }

    /* Compact Header */
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

    .page-header-compact::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, 
            transparent 30%, 
            rgba(255, 255, 255, 0.05) 50%, 
            transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .page-header-compact:hover::after {
        opacity: 1;
    }

    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .page-header-compact .header-content {
        position: relative;
        z-index: 2;
        align-items: center;
    }

    .page-title-compact {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: -0.025em;
        display: inline-flex;
        align-items: center;
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

    .header-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(-45deg);
        transition: transform 0.6s ease;
    }

    .header-icon:hover::before {
        transform: rotate(-45deg) translate(100%, 100%);
    }

    .premium-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 1.5rem;
        position: relative;
        z-index: 1;
        float: inline-end;
    }
    
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
    }
    
    .premium-btn-primary:hover {
        background: linear-gradient(45deg, #ee5a24, #ff6b6b);
        transform: translateY(-2px);
        color: white;
    }

    .premium-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        margin-bottom: 24px;
    }
    
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    .premium-card-header {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
        padding: 20px 24px;
        border-bottom: 1px solid rgba(37, 99, 235, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .premium-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .premium-card-title i {
        font-size: 1rem;
        padding: 8px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: var(--shadow);
    }

    .premium-card-body {
        padding: 24px;
    }

    /* Info Styling */
    .info-item {
        margin-bottom: 20px;
    }

    .info-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.4;
    }

    /* Special Elements */
    .salary-container, .pin-container {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%);
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid rgba(37, 99, 235, 0.15);
        transition: all 0.3s ease;
        width: fit-content;
    }

    .salary-container:hover, .pin-container:hover {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, rgba(59, 130, 246, 0.12) 100%);
        border-color: rgba(37, 99, 235, 0.25);
    }

    .btn-eye {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        color: white;
        cursor: pointer;
        padding: 6px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .btn-eye:hover {
        background: linear-gradient(135deg, var(--secondary), var(--primary));
        transform: scale(1.05);
        box-shadow: var(--shadow-md);
    }

    .probation-badge {
        background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
        color: #c2410c;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow);
        border: 1px solid rgba(196, 65, 12, 0.2);
    }

    /* Premium Table Styling */
    .leave-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        margin-bottom: 0px!important;
        background-color: #fff;
    }

    .leave-table thead {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
    }

    .leave-table thead th {
        font-weight: 700;
        color: #4a5568;
        padding: 1.5rem 1rem;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        text-align: center;
    }

    .leave-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
    }

    .leave-table tbody tr {
        transition: all 0.3s ease;
        border: none;
    }

    .leave-table tbody tr:hover {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        transform: scale(1.001);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }

    .leave-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        color: #2d3748;
        font-weight: 500;
        text-align: center;
    }

    .leave-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #e2e8f0;
    }

    /* Document Preview */
    .preview-container {
        display: inline-block;
        align-items: center;
        gap: 10px;
        padding: 12px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
        border-radius: 12px;
        border: 1px solid rgba(37, 99, 235, 0.1);
        transition: all 0.3s ease;
    }

    .preview-container:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .preview-container img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(37, 99, 235, 0.2);
        box-shadow: var(--shadow);
    }

    /* Enhanced SweetAlert2 Styling */
    .swal2-popup {
        border-radius: 20px !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        padding: 2rem !important;
    }

    .swal2-title {
        color: var(--text-primary) !important;
        font-weight: 700 !important;
        font-size: 1.5rem !important;
    }

    .swal2-content {
        color: var(--text-secondary) !important;
        font-size: 1rem !important;
    }

    .swal2-confirm {
        background: linear-gradient(135deg, var(--danger), #f87171) !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        font-size: 1rem !important;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3) !important;
    }

    .swal2-cancel {
        background: linear-gradient(135deg, var(--text-secondary), #9ca3af) !important;
        border-radius: 25px !important;
        font-weight: 600 !important;
        padding: 12px 24px !important;
        font-size: 1rem !important;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3) !important;
    }

    /* Premium Modal Styling */
    .premium-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .premium-modal .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
        padding: 24px;
    }

    .premium-modal .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .premium-modal .modal-body {
        padding: 24px;
        font-size: 1rem;
        color: var(--text-primary);
        line-height: 1.6;
    }

    /* Card Heights - Uniform Height System */
    .fixed-height-card,
    .medium-height-card,
    .short-height-card {
        min-height: 450px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Ensure equal height columns */
    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .row > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }

    .premium-card {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .premium-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .premium-card-body .row {
        flex: 1;
        display: flex;
        flex-wrap: wrap;
        align-content: flex-start;
    }

    .premium-card-body .col-md-6 {
        display: flex;
        flex-direction: column;
    }

    .info-item {
        margin-bottom: 20px;
        flex: 0 0 auto;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header-compact {
            padding: 1.5rem;
        }
        
        .page-title-compact {
            font-size: 1.8rem;
        }
        
        .premium-actions {
            flex-direction: column;
            align-items: stretch;
            margin-top: 1rem;
        }
        
        .premium-btn {
            justify-content: center;
        }
        
        .premium-card {
            margin-bottom: 16px;
        }
        
        .fixed-height-card,
        .medium-height-card,
        .short-height-card {
            min-height: 350px;
        }
        
        .header-icon {
            width: 50px;
            height: 50px;
            font-size: 1.4rem;
        }

        /* Mobile alignment and spacing fixes */
        .row + .row {
            margin-top: 24px;
        }

        .row.align-stretch {
            margin-bottom: 24px;
        }

        .row > [class*='col-'] {
            margin-bottom: 16px;
        }

        .row > [class*='col-']:last-child {
            margin-bottom: 0;
        }

        .premium-card {
            margin-bottom: 16px;
        }
    }

    @media (max-width: 576px) {
        .page-header-compact {
            padding: 20px 24px;
        }
        
        .page-title-compact {
            font-size: 1.4rem;
        }
        
        .premium-card-body {
            padding: 16px;
        }
        
        .leave-table thead th,
        .leave-table tbody td {
            padding: 8px 10px;
            font-size: 12px;
        }
        
        .fixed-height-card,
        .medium-height-card,
        .short-height-card {
            min-height: 300px;
        }
        
        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        /* Enhanced mobile spacing */
        .row + .row {
            margin-top: 20px;
        }

        .row.align-stretch {
            margin-bottom: 20px;
        }

        .premium-card {
            margin-bottom: 12px;
        }
    }

    /* Special handling for table containers */
    .leave-table-container {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Content section spacing */
    .content-wrapper {
        padding-top: 0;
    }

    /* Ensure proper spacing from header */
    .page-header-compact + .row {
        margin-top: 0;
    }

    /* Additional alignment fixes */
    .row.align-stretch {
        align-items: stretch;
    }

    .col-md-6 .premium-card,
    .col-md-12 .premium-card {
        height: 100%;
        margin-bottom: 0;
    }

    /* Ensure consistent vertical spacing */
    .row + .row {
        margin-top: 32px;
    }

    /* Add spacing between card sections */
    .premium-card {
        margin-bottom: 32px;
    }

    /* Specific spacing for different screen sizes */
    @media (min-width: 769px) {
        .row + .row {
            margin-top: 40px;
        }
        
        .premium-card {
            margin-bottom: 0;
        }
    }

    /* Enhanced spacing for better visual separation */
    .row.align-stretch {
        margin-bottom: 32px;
    }

    .row.align-stretch:last-child {
        margin-bottom: 0;
    }

    /* Utility classes for consistent spacing */
    .spacing-section {
        margin-bottom: 40px;
    }

    .spacing-section:last-child {
        margin-bottom: 0;
    }

    /* Card container spacing */
    .card-container {
        padding: 0 15px;
    }

    .preview-container:hover .btn-danger {
        opacity: 1;
    }

    .preview-container .btn-danger {
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }
</style>
@endpush

@section('content')
    @if($employee)
    @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
        <!-- Premium Header Section -->
        <div class="page-header-compact">
            <div class="header-content d-flex justify-content-between align-items-center">
                <div class="col-md-6 d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="page-title-compact d-flex align-items-center">
                            {{ $employee->name }} 
                        </h1>
                        <p class="page-subtitle-compact">{{ __('Employee Profile & Details') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="premium-actions">
                        @can('Edit Employee')
                            <a href="{{route('employee.edit',\Illuminate\Support\Facades\Crypt::encrypt($employee->id))}}" class="premium-btn premium-btn-primary">
                                <i class="fa fa-edit"></i> {{ __('Edit') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endif

    <div class="row align-stretch">
        <div class="col-md-6">
            <div class="premium-card fixed-height-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-user-circle text-primary"></i>
                        {{__('Personal Details')}}
                    </h6>
                </div>
                <div class="premium-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Employee ID')}}</div>
                                <p class="info-value">{{$employeesId ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Name')}}</div>
                                <p class="info-value">{{$employee->name ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Official Email')}}</div>
                                <p class="info-value">{{$employee->email ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Personal Email')}}</div>
                                <p class="info-value">{{$employee->user->personal_email ?? 'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Date of Birth')}}</div>
                                <p class="info-value">{{ $employee && $employee->dob ? \Auth::user()->dateFormat($employee->dob) : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Phone')}}</div>
                                <p class="info-value">{{$employee->phone ?? ''}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Address')}}</div>
                                <p class="info-value">{{$employee->address ?? ''}}</p>
                            </div>
                        </div>
                        @if($employee)
                        @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Salary')}}</div>
                                <div class="salary-container">
                                    <span id="hidden-salary" class="salary-text">*****</span>
                                    <span id="actual-salary" class="salary-text" style="display: none;">₹ {{$employee->salary}}</span>
                                    <button type="button" id="toggle-salary" class="btn-eye" onclick="toggleSalary()">
                                        <i id="eye-icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="premium-card fixed-height-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-building text-success"></i>
                        {{__('Company Details')}}
                    </h6>
                    @if($employee->is_probation == 1)
                        <span class="probation-badge">On-Probation</span>
                    @endif
                </div>
                <div class="premium-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Branch')}}</div>
                                <p class="info-value">{{!empty($employee->branch)?$employee->branch->name:'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Department')}}</div>
                                <p class="info-value">{{!empty($employee->department)?$employee->department->name:'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Designation')}}</div>
                                <p class="info-value">{{!empty($employee->designation)?$employee->designation->name:'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Date Of Joining')}}</div>
                                <p class="info-value">{{ !empty($employee->company_doj) ? \Auth::user()->dateFormat($employee->company_doj) : 'N/A'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Shift Start')}}</div>
                                <p class="info-value">{{!empty($employee->shift_start)?$employee->shift_start:'N/A'}}</p>
                            </div>
                        </div>
                        @if($employee->is_team_leader == 0)
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Team Leader')}}</div>
                                <p class="info-value">{{ !empty($teamLeaderDetails) ? $teamLeaderDetails->name : 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                        @if($employee->user_id == auth::user()->id || \Auth::user()->type == 'company')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Clock In Pin')}}</div>
                                <div class="pin-container">
                                    <span id="hidden-pin" class="pin-text">******</span>
                                    <span id="actual-pin" class="pin-text" style="display: none;">{{$employee->clock_in_pin ?? 'Not Set'}}</span>
                                    <button type="button" id="toggle-pin" class="btn-eye" onclick="togglePin()">
                                        <i id="pin-eye-icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($employee->is_active == 0)
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__('Date Of Exit')}}</div>
                                <p class="info-value">{{\Auth::user()->dateFormat($employee->date_of_exit)}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{__(!empty($employee->termination) ? 'Termination Reason' : 'Resignation Reason')}}</div>
                                <p class="info-value">
                                    @php
                                        $description = !empty($employee->termination)
                                            ? $employee->termination->description??''
                                            : $employee->resignation->description??'';
                                    @endphp

                                    {{ Str::limit($description, 80) }}

                                    @if (strlen($description) > 80)
                                        <a href="javascript:void(0)" class="read-more-btn text-info text-underline" data-description="{{ $description }}"><b>Read More</b></a>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <?php use Carbon\Carbon; ?>
        @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
            <div class="col-md-6 mt-4">
                <div class="premium-card medium-height-card">
                    <div class="premium-card-header">
                        <h6 class="premium-card-title">
                            <i class="fas fa-calendar-check text-warning"></i>
                            {{__('Annual Leave Details')}}
                        </h6>
                    </div>
                    <div class="premium-card-body">
                        @php
                            $formattedDate = Carbon::today()->format('Y-m-d');
                            $employeedoc = $employee->documents()->pluck('document_value','document_id');
                            $companyDoj = Carbon::parse($employee->company_doj);
                            $currentYear = Carbon::now();
                            $totalLeaves = 0;
                        @endphp
                        <div class="leave-table-container">
                            <table class="leave-table">
                            <thead>
                                <tr>
                                    <th>{{__('Leave Types')}}</th>
                                    <th>{{__('Total Leaves')}}</th>
                                    <th>{{__('Leaves Available')}}</th>
                                    <th>{{__('Leaves Availed')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $key=>$leave)
                                    @if($employee->gender == "Male" && $leave->title == "Maternity Leaves") @continue; @endif
                                    @if($employee->gender == "Female" && $leave->title == "Paternity Leaves") @continue; @endif
                                    @if($employee->is_probation == 0 || $leave->title == 'Sick Leave')
                                    <tr>
                                        <td><strong>{{$leave->title }}</strong></td>
                                        <td>
                                            @php $totalLeaves = $leave->days; @endphp
                                            @if($employee->is_probation == 1)
                                                {{ $totalLeaves - 2 }}
                                            @else
                                                {{ $totalLeaves }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($leave->title == 'Paid Leave')
                                                @php 
                                                // For paid leave, use real-time balance calculation
                                                if ($employee->is_probation == 1) {
                                                    $availableBalance = 0;
                                                    $leavesAvailed = 0;
                                                } else {
                                                    $breakdown = $employee->getBalanceBreakdown();
                                                    $availableBalance = $breakdown['available_balance']; // Use available_balance to account for pending
                                                    $leavesAvailed = $breakdown['total_availed']; // Use total_availed to include pending leaves
                                                }
                                                @endphp
                                                {{ $availableBalance }}
                                            @else
                                                @php
                                                $leavesAvailed = \App\Helpers\Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leave->id);
                                                @endphp
                                                @if($employee->is_probation == 1)
                                                    {{ max(0, $leave->days - $leavesAvailed - 2) }}
                                                @else
                                                    {{ max(0, $leave->days - $leavesAvailed) }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($leave->title == 'Paid Leave')
                                                {{ $leavesAvailed }}
                                            @else
                                                @php
                                                $leavesAvailed = \App\Helpers\Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leave->id);
                                                @endphp
                                                {{ $leavesAvailed }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-md-6 mt-4">
                <div class="premium-card medium-height-card">
                    <div class="premium-card-header">
                        <h6 class="premium-card-title">
                            <i class="fas fa-university text-info"></i>
                            {{__('Bank Account Details')}}
                        </h6>
                    </div>
                    <div class="premium-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Account Holder Name')}}</div>
                                    <p class="info-value">{{$employee->account_holder_name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Account Number')}}</div>
                                    <p class="info-value">{{$employee->account_number}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Bank Name')}}</div>
                                    <p class="info-value">{{$employee->bank_name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Bank IFSC Code')}}</div>
                                    <p class="info-value">{{$employee->bank_identifier_code}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('Branch Location')}}</div>
                                    <p class="info-value">{{$employee->branch_location}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{__('PAN Number')}}</div>
                                    <p class="info-value">{{$employee->tax_payer_id}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <div class="row align-stretch">
        <div class="col-md-12">
            <div class="premium-card short-height-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-file-alt text-danger"></i>
                        {{__('Document Details')}}
                    </h6>
                </div>
                <div class="premium-card-body">
                    <div class="row text-center">
                        @php
                           $employeedoc = $employee->documents()->pluck('document_value','document_id');
                        @endphp
                        @foreach($documents as $key=>$document)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="info-item">
                                    <div class="info-label">{{$document->name }}</div>
                                    @if(!empty($employeedoc[$document->id]))
                                        @php
                                            $filename = $employeedoc[$document->id];
                                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                            $supportedExtensions = ['jpeg', 'png', 'jpg', 'svg', 'pdf', 'doc'];
                                        
                                            $fileUrl = asset('document/'.$filename);
                                        @endphp
                                        
                                        <div class="preview-container position-relative" style="width: fit-content;">
                                            <!-- Remove Button -->
                                            <div class="position-absolute" style="top: -8px; right: -8px; z-index: 10;">
                                                <form action="{{ route('employee.document.remove', ['employee' => $employee->id ?? request()->route('employee'), 'document' => $document->id]) }}" 
                                                    method="POST" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to remove this document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm rounded-circle p-1" 
                                                            style="width: 24px; height: 24px; line-height: 0.5;"
                                                            title="Remove document">
                                                        <i class="fas fa-times" style="font-size: 10px;"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <a href="{{ $fileUrl }}" target="_blank">
                                                @if(in_array($extension, $supportedExtensions))
                                                    @switch($extension)
                                                        @case('jpeg')
                                                        @case('jpg')
                                                        @case('png')
                                                        @case('svg')
                                                            <img src="{{ $fileUrl }}" 
                                                                alt="Image Preview" 
                                                                class="w-16 h-16 object-cover rounded  hover:shadow-lg transition-shadow cursor-pointer"
                                                                title="Click to view full size: {{ $filename }}">
                                                            @break
                                                        
                                                        @case('pdf')
                                                            <div class="w-16 h-16  rounded overflow-hidden cursor-pointer bg-red-50 d-flex align-items-center justify-content-center" 
                                                                onclick="window.open('{{ $fileUrl }}', '_blank')" 
                                                                title="Click to view PDF">
                                                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                                            </div>
                                                            @break
                                                        
                                                        @case('doc')
                                                        @case('docx')
                                                            <div class="w-16 h-16  rounded bg-blue-50 d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-file-word text-blue-500 text-xl"></i>
                                                            </div>
                                                            @break
                                                        
                                                        @default
                                                            <div class="w-16 h-16  rounded bg-gray-50 d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-file text-gray-500 text-xl"></i>
                                                            </div>
                                                    @endswitch
                                                @else
                                                    <div class="w-16 h-16 border rounded bg-gray-50 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-file text-gray-500 text-xl"></i>
                                                    </div>
                                                @endif
                                                <!-- <span class="ml-2 text-primary">{{-- Str::limit($document->name, 15) --}}</span> -->
                                                <span class="ml-2 text-primary">{{ $document->name }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <p class="info-value text-muted">Not uploaded</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Modal for Description -->
    <div class="modal fade premium-modal" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">
                        <i class="fas fa-info-circle mr-2"></i>
                        Description
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="modal-description"></p>
                </div>
            </div>
        </div>
    </div>
    
<script>
    function togglePin() {
        const hiddenPin = document.getElementById('hidden-pin');
        const actualPin = document.getElementById('actual-pin');
        const eyeIcon = document.getElementById('pin-eye-icon');
        
        if (hiddenPin.style.display === "none") {
            hiddenPin.style.display = "inline";
            actualPin.style.display = "none";
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            hiddenPin.style.display = "none";
            actualPin.style.display = "inline";
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }

    function toggleSalary() {
        const hiddenSalary = document.getElementById('hidden-salary');
        const actualSalary = document.getElementById('actual-salary');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (hiddenSalary.style.display === "none") {
            hiddenSalary.style.display = "inline";
            actualSalary.style.display = "none";
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            hiddenSalary.style.display = "none";
            actualSalary.style.display = "inline";
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }

    // Read more functionality
    $(document).ready(function() {
        $('.read-more-btn').on('click', function() {
            const description = $(this).data('description');
            $('#modal-description').text(description);
            $('#descriptionModal').modal('show');
        });
    });
</script>
@endsection