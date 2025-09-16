@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Leave') }}
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

    /* Premium Header Section */
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

    /* Enhanced Statistics Cards */
    .stats-compact {
        margin-bottom: 24px;
    }

    .stat-card-compact {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stat-card-compact::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .stat-card-compact:hover::before {
        opacity: 1;
    }

    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-number-compact {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin: 0;
        line-height: 1;
    }

    .stat-label-compact {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 4px 0 0 0;
    }

    .stat-icon-compact {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .stat-card-compact:hover .stat-icon-compact {
        transform: scale(1.1);
    }

    /* Premium Card */
    .premium-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    /* Premium Table Container */
    .premium-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-height: 600px;
        overflow-y: auto;
    }

    .premium-table-container .premium-table,
    .premium-table-container .premium-table thead,
    .premium-table-container .premium-table tbody {
        width: 100% !important;
        min-width: 100% !important;
    }

    .premium-table th,
    .premium-table td {
        white-space: nowrap;
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .premium-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-top: none;
    }
    
    .premium-table {
        margin-bottom: 0 !important;
        border-collapse: collapse;
        table-layout: auto;
        background-color: #fff;
    }
    
    .premium-table thead {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8edff 100%);
    }
    
    .premium-table thead th {
        font-weight: 700;
        color: #4a5568;
        padding: 1.5rem 1rem;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        position: sticky;
        top: 0;
        z-index: 100;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(59, 130, 246, 0.95) 100%);
        color: white !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
    }
    
    .premium-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }
    
    .premium-table tbody tr {
        transition: all 0.3s ease;
        border: none;
    }
    
    .premium-table tbody tr:nth-child(even) {
        background-color: rgba(37, 99, 235, 0.02);
    }
    
    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        transform: scale(1.001);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }
    
    .premium-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        color: #2d3748;
        font-size: 0.9rem;
        font-weight: 500;
        text-align: center;
    }
    
    .premium-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #e2e8f0;
    }

    /* Employee Header Row */
    .employee-header-row {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(59, 130, 246, 0.95) 100%) !important;
        color: white !important;
        position: sticky;
        top: 60px;
        z-index: 99;
    }

    .employee-header-row td {
        padding: 1.5rem !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        text-align: left !important;
        color: white !important;
        border-bottom: 2px solid rgba(255,255,255,0.2) !important;
    }

    .employee-name-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 96%;
    }

    .employee-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .employee-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .leave-counter {
        background: rgba(255,255,255,0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .view-more-btn {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .view-more-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: scale(1.05);
    }

    /* Enhanced Tags */
    .employee-id-tag {
        background: linear-gradient(135deg, #5c85ff 0%, #5c66ff 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow);
    }

    .employee-id-tag:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .employee-id-tag::before {
        content: '#';
        opacity: 0.8;
    }

    .leave-type-tag {
        background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    /* Enhanced Status Badges */
    .status-badge {
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        text-decoration: none;
        color: white;
    }

    .status-badge.pending {
        background: linear-gradient(135deg, var(--warning) 0%, #fab1a0 100%);
        color: white;
    }

    .status-badge.approved {
        background: linear-gradient(135deg, var(--success) 0%, #00cec9 100%);
        color: white;
    }

    .status-badge.rejected {
        background: linear-gradient(135deg, var(--danger) 0%, #fd79a8 100%);
        color: white;
    }

    /* Enhanced Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: none;
        cursor: pointer;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        text-decoration: none;
    }
    
    .action-btn.btn-edit {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }

    .action-btn.btn-edit:hover {
        color: white;
    }
    
    .action-btn.btn-delete {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }

    .action-btn.btn-delete:hover {
        color: white;
    }
    
    .action-btn.btn-action {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }

    .action-btn.btn-action:hover {
        color: white;
    }

    /* Collapsible Rows */
    .collapsible-rows {
        display: none;
    }

    .collapsible-rows.show {
        display: table-row;
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

    /* Enhanced Scrollbar */
    .premium-table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .premium-table-container::-webkit-scrollbar-track {
        background: var(--light);
        border-radius: 4px;
    }

    .premium-table-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .premium-table-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .action-btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .action-btn.loading i {
        animation: spin 1s linear infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header-compact {
            padding: 2rem 1.5rem;
        }
        
        .page-title-compact {
            font-size: 1.75rem;
        }
        
        .premium-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .premium-btn {
            justify-content: center;
        }
        
        .premium-table-container {
            overflow-x: auto;
            max-height: 500px;
        }
        
        .premium-table {
            font-size: 0.8rem;
        }
        
        .premium-table thead th,
        .premium-table tbody td {
            padding: 1rem 0.5rem;
        }

        .employee-header-row td {
            padding: 1rem !important;
            font-size: 1rem !important;
        }

        .employee-name-section {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .action-buttons {
            flex-direction: column;
            gap: 4px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }
    }

    @media (max-width: 576px) {
        .premium-table th,
        .premium-table td {
            padding: 6px 8px;
            font-size: 12px;
        }
        
        .employee-id-tag {
            font-size: 10px;
            padding: 2px 6px;
        }
        
        .leave-type-tag {
            font-size: 10px;
            padding: 2px 6px;
        }
    }
</style>
@endpush

@section('content')
    <!-- Premium Header Section -->
    <div class="page-header-compact">
        <div class="header-content d-flex justify-content-between align-items-center">
            <div class="col-md-6 d-flex">
                <div class="header-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="ml-3">
                    <h1 class="page-title-compact">{{ __('Leave Management') }}</h1>
                    <p class="page-subtitle-compact">{{ __('Comprehensive leave tracking and management dashboard for enhanced workforce planning') }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="premium-actions">
                    @can('Create Leave')
                        @if($selfLeaves == 'true' || \Auth::user()->type == 'hr')
                            <a href="#" data-url="{{ route('leave.create') }}" class="premium-btn premium-btn-primary btn-create-leave"
                            data-ajax-popup="true" data-title="{{ __('Create New Leave') }}">
                                <i class="fas fa-plus"></i> {{ __('Create Leave') }}
                            </a>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row stats-compact">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $leaves->count() ?? 0 }}</h3>
                        <p class="stat-label-compact">{{ __('Total Leaves') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #c4d3f9 0%, #b4d3f5 100%); color: #3a3ded;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">
                            @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                {{ $leaves->where('status', 'Pending')->count() }}
                            @else
                                {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Pending')->count(); }) }}
                            @endif
                        </h3>
                        <p class="stat-label-compact">{{ __('Pending') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">
                            @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                {{ $leaves->where('status', 'Approve')->count() }}
                            @else
                                {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Approve')->count(); }) }}
                            @endif
                        </h3>
                        <p class="stat-label-compact">{{ __('Approved') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #059669;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">
                            @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                {{ $leaves->where('status', 'Reject')->count() }}
                            @else
                                {{ $leaves->sum(function($employee) { return $employee->employeeLeaves->where('status', 'Reject')->count(); }) }}
                            @endif
                        </h3>
                        <p class="stat-label-compact">{{ __('Rejected') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); color: #dc2626;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="premium-card">
                <div class="table-responsive">
                    <div class="premium-table-container">
                        <table class="table premium-table" style="width: 100%;" id="leaveTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 80px;"><i class="fas fa-hashtag"></i> {{ __('ID') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-tag"></i> {{ __('Leave Type') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-calendar-plus"></i> {{ __('Applied On') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-play-circle"></i> {{ __('Start Date') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-stop-circle"></i> {{ __('End Date') }}</th>
                                    <th style="min-width: 100px;"><i class="fas fa-clock"></i> {{ __('Total Days') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-adjust"></i> {{ __('Half/Full Day') }}</th>
                                    <th style="min-width: 150px;"><i class="fas fa-comment"></i> {{ __('Leave Reason') }}</th>
                                    <th style="min-width: 100px;"><i class="fas fa-info-circle"></i> {{ __('Status') }}</th>
                                    <th style="min-width: 120px;"><i class="fas fa-cogs"></i> {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                    @foreach ($leaves as $key => $leave)
                                        <tr id="leave-row-{{ $leave->id }}">
                                            <td><span class="employee-id-tag">{{ ++$i }}</span></td>
                                            <td><span class="leave-type-tag">{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</span></td>
                                            <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                            <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_start_time }}</small> @endif</td>
                                            <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_end_time }}</small> @endif</td>
                                            <td><strong>{{ $leave->total_leave_days }}</strong></td>
                                            <td>{{ ucwords($leave->leavetype) }} @if($leave->day_segment) <br><small>({{ ucwords($leave->day_segment) }})</small> @endif</td>
                                            <td>{{ \Illuminate\Support\Str::limit($leave->leave_reason, 25) }}</td>
                                            <td>
                                                @if ($leave->status == 'Pending')
                                                    <span class="status-badge pending">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                @elseif($leave->status == 'Approve')
                                                    <span class="status-badge approved">
                                                        <i class="fas fa-check"></i> Approved
                                                    </span>
                                                @else
                                                    <a href="#" data-url="{{ URL::to('leave/' . $leave->id . '/reason') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('See Reason') }}">
                                                        <span class="status-badge rejected">
                                                            <i class="fas fa-times"></i> Rejected
                                                        </span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    @if (\Auth::user()->type == 'employee' && (\Auth::user()->employee->is_team_leader == 0 ||  \Auth::user()->employee->id == $leave->employee_id))
                                                        @if ($leave->status == 'Pending')
                                                            @can('Edit Leave')
                                                                <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endcan
                                                        @endif
                                                    @else
                                                        <button class="action-btn btn-action" data-url="{{ URL::to('leave/' . $leave->id . '/action') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Leave Action') }}">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                        @can('Edit Leave')
                                                            @if ($leave->status == 'Pending')
                                                                <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endif
                                                        @endcan
                                                    @endif
                                                    @if ($leave->status == 'Pending')
                                                        @can('Delete Leave')
                                                            <button type="button" 
                                                                    class="action-btn btn-delete delete-leave" 
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Delete Leave') }}"
                                                                    data-leave-id="{{ $leave->id }}"
                                                                    data-leave-type="{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : 'Leave' }}"
                                                                    data-employee-name="{{ \Auth::user()->name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            
                                                            {{-- Hidden Form for Delete --}}
                                                            <form method="POST" action="{{ route('leave.destroy', $leave->id) }}" id="delete-form-{{ $leave->id }}" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach ($leaves as $employee)
                                        @php
                                            $employeeLeaves = $employee->employeeLeaves;
                                        @endphp
                                        
                                        @if(!count($employeeLeaves)) @continue @endif
                                    
                                        <tr class="employee-header-row">
                                            <td colspan="10" style="background: #5799f8;">
                                                <div class="employee-name-section">
                                                    <div class="employee-info">
                                                        <div class="employee-avatar">
                                                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $employee->name }}</strong>
                                                            <br>
                                                            <small>{{ $employee->email ?? 'No email' }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="employee-controls">
                                                        <span class="leave-counter">
                                                            <i class="fas fa-calendar-check"></i> {{ count($employeeLeaves) }} Leaves
                                                        </span>
                                                        @if(count($employeeLeaves) > 5)
                                                            <button class="view-more-btn" onclick="toggleEmployeeLeaves('{{ $employee->id }}')">
                                                                <span id="toggle-text-{{ $employee->id }}">View All</span>
                                                                <i class="fas fa-chevron-down" id="toggle-icon-{{ $employee->id }}"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    
                                        @foreach ($employeeLeaves as $index => $leave)
                                            <tr class="{{ $index >= 5 ? 'collapsible-rows employee-' . $employee->id : '' }}" id="leave-row-{{ $leave->id }}">
                                                <td><span class="employee-id-tag">{{ ++$i }}</span></td>
                                                <td><span class="leave-type-tag">{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</span></td>
                                                <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                                <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_start_time }}</small> @endif</td>
                                                <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') <br><small>{{ $leave->formatted_end_time }}</small> @endif</td>
                                                <td><strong>{{ $leave->total_leave_days }}</strong></td>
                                                <td>{{ ucwords($leave->leavetype) }} @if($leave->day_segment) <br><small>({{ ucwords($leave->day_segment) }})</small> @endif</td>
                                                <td>{{ \Illuminate\Support\Str::limit($leave->leave_reason, 25) }}</td>
                                                <td>
                                                    @if ($leave->status == 'Pending')
                                                        <span class="status-badge pending">
                                                            <i class="fas fa-clock"></i> Pending
                                                        </span>
                                                    @elseif($leave->status == 'Approve')
                                                        <span class="status-badge approved">
                                                            <i class="fas fa-check"></i> Approved
                                                        </span>
                                                    @else
                                                        <a href="#" data-url="{{ URL::to('leave/' . $leave->id . '/reason') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('See Reason') }}">
                                                            <span class="status-badge rejected">
                                                                <i class="fas fa-times"></i> Rejected
                                                            </span>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        @if (\Auth::user()->type == 'employee' && (\Auth::user()->employee->is_team_leader == 0 || \Auth::user()->employee->id == $leave->employee_id))
                                                            @if ($leave->status == 'Pending')
                                                                @can('Edit Leave')
                                                                    <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                @endcan
                                                            @endif
                                                        @else
                                                            <button class="action-btn btn-action" data-url="{{ URL::to('leave/' . $leave->id . '/action') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Leave Action') }}">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                            @can('Edit Leave')
                                                                @if ($leave->status == 'Pending')
                                                                    <button class="action-btn btn-edit" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                @endif
                                                            @endcan
                                                        @endif
                                                        @if ($leave->status == 'Pending')
                                                            @can('Delete Leave')
                                                                <button type="button" 
                                                                        class="action-btn btn-delete delete-leave" 
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Delete Leave') }}"
                                                                        data-leave-id="{{ $leave->id }}"
                                                                        data-leave-type="{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : 'Leave' }}"
                                                                        data-employee-name="{{ $employee->name ?? \Auth::user()->name }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                
                                                                {{-- Hidden Form for Delete --}}
                                                                <form method="POST" 
                                                                    action="{{ route('leave.destroy', $leave->id) }}" 
                                                                    id="delete-form-{{ $leave->id }}" 
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Handle duration change (Full Day / Half Day / Short Leave)
    function handleDurationChange() {
        const selectedValue = document.getElementById('is_halfday').value;
        console.log('Duration changed to:', selectedValue);
        setupFormByDurationType(selectedValue);
    }

    // Setup form based on duration type - FIXED VERSION
    function setupFormByDurationType(durationType) {
        const daySegmentContainer = document.getElementById('day_segment_container');
        const timingContainer = document.getElementById('timing-container');
        const endDateContainer = document.getElementById('end_date_container');
        
        console.log('Setting up form for duration:', durationType);
        console.log('Timing container found:', !!timingContainer);
        
        // First, hide all conditional containers
        hideElement(daySegmentContainer);
        hideElement(timingContainer);
        showElement(endDateContainer);
        
        switch(durationType) {
            case 'full':
                // Full day: Show end date only
                showElement(endDateContainer);
                break;
                
            case 'half':
                // Half day: Hide end date, show day segment
                hideElement(endDateContainer);
                showElement(daySegmentContainer);
                updateEndDateToStartDate();
                break;
                
            case 'short':
                // Short leave: Hide end date, show day segment AND timing
                console.log('Setting up short leave - showing timing container');
                hideElement(endDateContainer);
                showElement(daySegmentContainer);
                showElement(timingContainer);
                updateEndDateToStartDate();
                setDefaultStartTime();
                break;
        }
    }

    // Helper function to show element reliably
    function showElement(element) {
        if (element) {
            // Remove all possible hidden classes and set display
            element.classList.remove('hidden', 'd-none');
            element.style.display = 'block';
            element.style.visibility = 'visible';
            element.style.opacity = '1';
            
            // Also ensure child inputs are enabled
            const inputs = element.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = false;
                input.style.display = 'block';
            });
            
            console.log('Element shown:', element.id);
        }
    }

    // Helper function to hide element reliably
    function hideElement(element) {
        if (element) {
            element.classList.add('hidden');
            element.style.display = 'none';
            console.log('Element hidden:', element.id);
        }
    }

    // Update end date to start date for half/short leaves
    function updateEndDateToStartDate() {
        const startDate = document.getElementById('start_date').value;
        if (startDate) {
            document.getElementById('end_date').value = startDate;
        }
    }

    // Set default start time based on day segment
    function setDefaultStartTime() {
        const daySegment = document.getElementById('day_segment').value;
        const startTimeInput = document.getElementById('start_time');
        
        // Only set default if no existing value
        if (!startTimeInput.value) {
            // Set default times in 24-hour format (HTML5 time input format)
            if (daySegment === 'morning') {
                startTimeInput.value = '09:00';
            } else {
                startTimeInput.value = '14:00';
            }
        }
        
        // Calculate end time and add validation
        calculateEndTime();
        addValidationSuccess(startTimeInput);
    }

    // Issue #4 Fix: Calculate end time (start time + 2 hours) with proper format handling
    function calculateEndTime() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startTimeInput.value) {
            try {
                // Parse the time input (HTML5 time input provides 24-hour format)
                const [hours, minutes] = startTimeInput.value.split(':').map(Number);
                
                // Create date object for calculation
                const startTime = new Date();
                startTime.setHours(hours, minutes, 0, 0);
                
                // Add 2 hours
                const endTime = new Date(startTime.getTime() + (2 * 60 * 60 * 1000));
                
                // Format back to 24-hour format for the input field
                const endHours = endTime.getHours().toString().padStart(2, '0');
                const endMinutes = endTime.getMinutes().toString().padStart(2, '0');
                
                endTimeInput.value = endHours + ':' + endMinutes;
                
                // Add validation success indicator
                addValidationSuccess(startTimeInput);
                addValidationSuccess(endTimeInput);
                
            } catch (error) {
                console.error('Error calculating end time:', error);
                endTimeInput.value = '';
            }
        } else {
            endTimeInput.value = '';
        }
    }

    // Add validation success indicator
    function addValidationSuccess(element) {
        if (element.value) {
            element.classList.add('input-valid');
            const wrapper = element.closest('.input-wrapper');
            if (wrapper) {
                wrapper.classList.add('valid');
            }
        }
    }








    $(document).ready(function() {
        console.log('Initializing leave table...');

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Enhanced delete confirmation with SweetAlert2
        $('.delete-leave').on('click', function(e) {
            e.preventDefault();
            
            const leaveId = $(this).data('leave-id');
            const leaveType = $(this).data('leave-type');
            const employeeName = $(this).data('employee-name');
            const row = $(this).closest('tr');
            
            // Check if SweetAlert2 is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Leave Request?',
                    html: `
                        <div style="text-align: left; margin: 1rem 0;">
                            <p><strong>Leave Type:</strong> ${leaveType}</p>
                            <p><strong>Employee:</strong> ${employeeName}</p>
                            <br>
                            <p style="color: var(--danger); font-weight: 600;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                This action cannot be undone and will permanently remove this leave request.
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, Delete Leave',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        content: 'swal2-content',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add loading state to the row
                        row.addClass('loading-row');
                        
                        // Show loading toast
                        Swal.fire({
                            title: 'Deleting Leave...',
                            html: `
                                <div style="text-align: center; margin: 1rem 0;">
                                    <div class="loading-spinner" style="margin: 0 auto 1rem;"></div>
                                    <p>Please wait while we delete the leave request for <strong>${employeeName}</strong>.</p>
                                </div>
                            `,
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal2-popup'
                            }
                        }).then(() => {
                            // Submit the form
                            const form = document.getElementById(`delete-form-${leaveId}`);
                            if (form) {
                                form.submit();
                            }
                        });
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert2 is not available
                const confirmMessage = `Are you sure you want to delete this leave request for "${employeeName}"? This action cannot be undone.`;
                if (confirm(confirmMessage)) {
                    // Add loading state to the row
                    row.addClass('loading-row');
                    
                    // Submit the form
                    const form = document.getElementById(`delete-form-${leaveId}`);
                    if (form) {
                        form.submit();
                    }
                }
            }
        });

        // Function to toggle employee leaves
        window.toggleEmployeeLeaves = function(employeeId) {
            const collapsibleRows = document.querySelectorAll('.employee-' + employeeId);
            const toggleText = document.getElementById('toggle-text-' + employeeId);
            const toggleIcon = document.getElementById('toggle-icon-' + employeeId);
            
            collapsibleRows.forEach(row => {
                if (row.classList.contains('show')) {
                    row.classList.remove('show');
                    toggleText.textContent = 'View All';
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                } else {
                    row.classList.add('show');
                    toggleText.textContent = 'Show Less';
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                }
            });
        };

        // Enhanced hover effects for action buttons
        $('.action-btn').hover(
            function() {
                $(this).css('transform', 'translateY(-2px) scale(1.05)');
            },
            function() {
                $(this).css('transform', 'translateY(0) scale(1)');
            }
        );

        // Success message handling
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Great!',
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    confirmButton: 'swal2-confirm'
                },
                buttonsStyling: false
            });
        @endif

        // Error message handling
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    confirmButton: 'swal2-confirm'
                },
                buttonsStyling: false
            });
        @endif

        // Employee selection change handler for leave creation
        $(document).on('change', '#employee_id', function() {
            var employeeId = $(this).val();
            if (employeeId) {
                $.ajax({
                    url: "{{ url('/leave/get-paid-leave-balance') }}" + "/" + employeeId,
                    type: 'GET',
                    success: function(response) {
                        if (response.leavetypes) {
                            $('#leave_type_id').html("<option value='' disabled selected> Select Leave Type </option>");
                            $.each(response.leavetypes, function(index, leave) {
                                if (leave.title == "Paternity Leaves" && response.employee.gender == 'Female') return true;
                                if (leave.title == "Maternity Leaves" && response.employee.gender == 'Male') return true;
                                var optionText = leave.title;
                                if (leave.title === "Paid Leave") {
                                    optionText += ' (' + leave.days + ')';
                                } else {
                                    optionText += ' (' + leave.days + ')';
                                }

                                var isBirthdayLeave = (leave.title === "Birthday Leave" || leave.id === 8);
                                var isSameMonthAsDOB = false;

                                if (isBirthdayLeave) {
                                    var dob = new Date(response.employee.dob);
                                    var dobMonth = dob.getMonth() + 1;
                                    var currentMonth = new Date().getMonth() + 1;
                                    isSameMonthAsDOB = (dobMonth === currentMonth);
                                }

                                var option = $('<option>', {
                                    value: leave.id,
                                    text: optionText,
                                    'data-title': leave.title,
                                    disabled: leave.days === 0 || (isBirthdayLeave && !isSameMonthAsDOB)
                                });
                                $('#leave_type_id').append(option);
                            });
                            if (typeof halfDayLeave === 'function') {
                                halfDayLeave();
                            }
                        }
                    },
                    error: function() {
                        console.log('Error fetching paid leave balance');
                    }
                });
            }
        });

        // Remove model function
        window.removeModel = function() {
            const modal = document.getElementById('commonModalCustom');
            if (modal) {
                modal.remove();
            }
        };
    });

    // Add CSS for ripple effect and loading states
    const style = document.createElement('style');
    style.textContent = `
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .loading-row {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .loading-row::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    `;
    document.head.appendChild(style);
</script>

@endsection
