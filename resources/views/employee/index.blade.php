@extends('layouts.admin')

@section('page-title')
    @if(isset($_GET['type']) && $_GET['type'] == 'probation')
        {{ __('Probation Employees') }}
    @else
        {{ __('Active Employees') }}
    @endif
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
    
    .premium-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }
    
    .premium-subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-top: 0.5rem;
        position: relative;
        z-index: 1;
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
    }
    
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }
    
    .premium-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
    }
    
    .premium-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
    }
    
    .premium-table tbody tr {
        transition: all 0.3s ease;
        border: none;
    }
    
    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        transform: scale(1.001);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }
    
    .premium-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        color: #2d3748;
        font-weight: 500;
    }
    
    .premium-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #e2e8f0;
    }

    #DataTables_Table_0_wrapper{
        overflow: auto!important;
    }
    
    .employee-id-badge {
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

    .employee-id-badge:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .employee-id-badge::before {
        content: '#';
        opacity: 0.8;
    }
    
    .employee-name {
        font-weight: 700;
        color: #2d3748;
        font-size: 1.05rem;
    }
    
    .employee-email {
        color: #718096;
        font-size: 0.9rem;
    }
    
    .department-badge {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .branch-badge {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .designation-badge {
        background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
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
    
    .action-btn-edit {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }
    
    .action-btn-delete {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }
    
    .action-btn-deactivate {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }
    
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
    
    /* Compact Tags */
    .tag-stack {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-tag-compact {
        padding: 4px 12px;
        border-radius: 12px;
        font-weight: 600;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        border: 1px solid transparent;
    }

    .info-tag-compact:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    .branch-tag {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-color: #fbbf24;
    }

    .department-tag {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border-color: #22c55e;
    }

    .designation-tag {
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        color: #6b21a8;
        border-color: #8b5cf6;
    }

    /* Loading State */
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

    .swal2-confirm.swal2-styled.swal2-deactivate {
        background: linear-gradient(135deg, var(--warning), #fbbf24) !important;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3) !important;
    }

    #employeeTable_filter{
        width: fit-content;
        float: inline-end;
    }
    
    @media (max-width: 768px) {
        .premium-header {
            padding: 1.5rem;
        }
        
        .premium-title {
            font-size: 2rem;
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
        }
        .premium-table-container .dataTable {
            width: 100% !important;
            table-layout: auto;
        }
    }

    .employee-id-badge {
        background: #007bff;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        font-size: 12px;
    }

    .employee-id-badge:hover {
        background: #0056b3;
        color: white;
        text-decoration: none;
    }

    .employee-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .employee-email {
        font-size: 12px;
        color: #6c757d;
    }

    /* Tag styles */
    .tag-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .info-tag-compact {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
    }

    .branch-tag {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .department-tag {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }

    .designation-tag {
        background-color: #e8f5e8;
        color: #388e3c;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }

    .action-btn {
        border: none;
        padding: 8px 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
    }

    .action-btn-deactivate {
        background-color: #ffc107;
        color: #212529;
    }

    .action-btn-deactivate:hover {
        background-color: #e0a800;
        color: #212529;
    }

    .action-btn-edit {
        background-color: #17a2b8;
        color: white;
    }

    .action-btn-edit:hover {
        background-color: #138496;
        color: white;
        text-decoration: none;
    }

    .action-btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .action-btn-delete:hover {
        background-color: #c82333;
        color: white;
    }

    /* Responsive behavior for smaller screens */
    @media (max-width: 768px) {
        .premium-table-container {
            font-size: 14px;
        }
        
        .premium-table th,
        .premium-table td {
            padding: 8px 10px;
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
        
        .employee-id-badge {
            font-size: 10px;
            padding: 2px 6px;
        }
        
        .info-tag-compact {
            font-size: 10px;
            padding: 2px 6px;
        }
    }

    /* CSS for ripple effect */
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
</style>
@endpush

@section('content')
    <!-- Premium Header Section -->
    <div class="page-header-compact">
        <div class="header-content d-flex justify-content-between align-items-center">
            <div class="col-md-6 d-flex">
                <div class="header-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="ml-3">
                    <h1 class="page-title-compact">
                        @if(isset($_GET['type']) && $_GET['type'] == 'probation')
                            {{ __('Probation Employees') }}
                        @else
                            {{ __('Active Employees') }}
                        @endif
                    </h1>
                    <p class="page-subtitle-compact">{{ __('Manage your team with premium tools and insights') }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="premium-actions">
                    @can('Create Employee')
                        @if(isset($_GET['type']) && $_GET['type'] == 'probation')
                            <a href="{{route('employee.index')}}" class="premium-btn">
                                <i class="fas fa-users"></i> {{ __('Active Employees') }}
                            </a>
                        @else
                            <a href="{{ route('employee.index', ['type' => 'probation']) }}" class="premium-btn">
                                <i class="fas fa-user-clock"></i> {{ __('Probation Employees') }}
                            </a>
                        @endif
                        <a href="{{ route('employee.create') }}" class="premium-btn premium-btn-primary">
                            <i class="fas fa-plus"></i> {{ __('Add Employee') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row stats-compact">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ count($employees) }}</h3>
                        <p class="stat-label-compact">{{ __('Total Employees') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #c4d3f9 0%, #b4d3f5 100%); color: #3a3ded;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->where('is_active', 1)->count() }}</h3>
                        <p class="stat-label-compact">{{ __('Active') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #059669;">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->where('company_doj', '>=', now()->startOfYear())->count() }}</h3>
                        <p class="stat-label-compact">{{ __('This Year') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->unique('department_id')->count() }}</h3>
                        <p class="stat-label-compact">{{ __('Departments') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                        <i class="far fa-building"></i>
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
                        <table class="table premium-table" style="width: 100%;" id="employeeTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 120px;">{{ __('Employee ID') }}</th>
                                    <th style="min-width: 200px;">{{ __('Employee Details') }}</th>
                                    <th style="min-width: 150px;">{{ __('Department') }}</th>
                                    <th style="min-width: 120px;">{{ __('Branch') }}</th>
                                    <th style="min-width: 150px;">{{ __('Designation') }}</th>
                                    <th style="min-width: 120px;">{{ __('Joining Date') }}</th>
                                    <th style="min-width: 100px;">{{ __('Shift Time') }}</th>
                                    @if (Gate::check('Edit Employee') || Gate::check('Delete Employee'))
                                        <th class="text-center" style="min-width: 120px;">{{ __('Actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr id="employee-row-{{ $employee->id }}">
                                        <td>
                                            @can('Show Employee')
                                                <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" class="employee-id-badge">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </a>
                                            @else
                                                <span class="employee-id-badge">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td>
                                            <div class="employee-name">{{ $employee->name }}</div>
                                            <div class="employee-email">{{ $employee->email }}</div>
                                        </td>
                                        <td>
                                            <div class="tag-stack">
                                                <span class="info-tag-compact branch-tag">
                                                    {{ !empty(\Auth::user()->getDepartment($employee->department_id)) ? \Auth::user()->getDepartment($employee->department_id)->name : 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tag-stack">
                                                <span class="info-tag-compact department-tag">
                                                    {{ !empty(\Auth::user()->getBranch($employee->branch_id)) ? \Auth::user()->getBranch($employee->branch_id)->name : 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tag-stack">
                                                <span class="info-tag-compact designation-tag">
                                                    {{ !empty(\Auth::user()->getDesignation($employee->designation_id)) ? \Auth::user()->getDesignation($employee->designation_id)->name : 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>
                                                {{ !empty($employee->company_doj) ? date('d M, Y', strtotime($employee->company_doj)) : 'N/A' }}
                                            </strong>
                                        </td>
                                        <td>
                                            <strong>
                                                {{ !empty($employee->shift_start) ? date('h:i A', strtotime($employee->shift_start)) : 'N/A' }}
                                            </strong>
                                        </td>
                                        @if (Gate::check('Edit Employee') || Gate::check('Delete Employee'))
                                            <td>
                                                <div class="action-buttons">
                                                    @if ($employee->is_active == 1)
                                                        @can('Edit Employee')
                                                            <button type="button" 
                                                                    class="action-btn action-btn-deactivate deactivate-employee" 
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Deactivate User') }}"
                                                                    data-employee-id="{{ $employee->id }}"
                                                                    data-employee-name="{{ $employee->name }}">
                                                                <i class="fas fa-user-slash"></i>
                                                            </button>
                                                            
                                                            {{-- Hidden Form for Deactivate --}}
                                                            <form id="deactivate-form-{{ $employee->id }}" 
                                                                action="{{ route('employee.deactivate', $employee->id) }}" 
                                                                method="GET" 
                                                                style="display: none;">
                                                            </form>

                                                            <a href="{{ route('employee.edit', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" 
                                                            class="action-btn action-btn-edit" 
                                                            data-toggle="tooltip"
                                                            data-original-title="{{ __('Edit Employee') }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endcan
                                                        @can('Delete Employee')
                                                            <button type="button" 
                                                                    class="action-btn action-btn-delete delete-employee" 
                                                                    data-toggle="tooltip"
                                                                    data-original-title="{{ __('Delete Employee') }}"
                                                                    data-employee-id="{{ $employee->id }}"
                                                                    data-employee-name="{{ $employee->name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            
                                                            {{-- Hidden Form for Delete --}}
                                                            <form id="delete-form-{{ $employee->id }}" 
                                                                action="{{ route('employee.destroy', $employee->id) }}" 
                                                                method="POST" 
                                                                style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Handle session messages separately to avoid Blade directive conflicts --}}
<script>
// Handle session messages first
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Success!',
                text: {!! json_encode(session('success')) !!},
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
        }
    });
@endif

@if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error!',
                text: {!! json_encode(session('error')) !!},
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
        }
    });
@endif
</script>

<script>
$(document).ready(function() {
    console.log('Initializing employee table...');
    
    // Simple search and pagination for employee table
    var currentPage = 1;
    var entriesPerPage = 25;
    var searchTerm = '';
    
    // Add controls
    function addEmployeeControls() {
        var controlsHtml = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Show 
                        <select id="employeeEntriesSelect" class="form-select d-inline-block" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        entries
                    </label>
                </div>
                <div class="col-md-6 text-end">
                    <label>Search: 
                        <input type="text" id="employeeSearchInput" class="form-control d-inline-block" style="width: 200px;" placeholder="Search employees...">
                    </label>
                </div>
            </div>
        `;
        
        var paginationHtml = `
            <div class="row mt-3">
                <div class="col-md-6">
                    <div id="employeeTableInfo">Showing 1 to 25 of 0 entries</div>
                </div>
                <div class="col-md-6 text-end">
                    <button id="employeePrevBtn" class="btn btn-sm btn-outline-primary me-2">Previous</button>
                    <button id="employeeNextBtn" class="btn btn-sm btn-outline-primary">Next</button>
                </div>
            </div>
        `;
        
        $('.premium-table-container').before(controlsHtml);
        $('.premium-table-container').after(paginationHtml);
    }
    
    // Filter and paginate employees
    function updateEmployeeTable() {
        var allRows = $('#employeeTable tbody tr');
        var filteredRows = allRows;
        
        // Apply search filter
        if (searchTerm) {
            filteredRows = allRows.filter(function() {
                return $(this).text().toLowerCase().indexOf(searchTerm.toLowerCase()) > -1;
            });
        }
        
        var totalFiltered = filteredRows.length;
        var totalPages = Math.ceil(totalFiltered / entriesPerPage);
        
        // Validate current page
        if (currentPage > totalPages) currentPage = Math.max(1, totalPages);
        if (currentPage < 1) currentPage = 1;
        
        // Hide all rows
        allRows.hide();
        
        // Show current page rows
        var startIndex = (currentPage - 1) * entriesPerPage;
        var endIndex = Math.min(startIndex + entriesPerPage, totalFiltered);
        
        filteredRows.slice(startIndex, endIndex).show();
        
        // Update info
        var showingStart = totalFiltered > 0 ? startIndex + 1 : 0;
        $('#employeeTableInfo').text(`Showing ${showingStart} to ${endIndex} of ${totalFiltered} entries`);
        
        // Update buttons
        $('#employeePrevBtn').prop('disabled', currentPage <= 1);
        $('#employeeNextBtn').prop('disabled', currentPage >= totalPages);
    }
    
    // Initialize controls and event handlers
    addEmployeeControls();
    
    // Event handlers for pagination and search
    $(document).on('keyup', '#employeeSearchInput', function() {
        searchTerm = $(this).val();
        currentPage = 1;
        updateEmployeeTable();
    });
    
    $(document).on('change', '#employeeEntriesSelect', function() {
        entriesPerPage = parseInt($(this).val());
        currentPage = 1;
        updateEmployeeTable();
    });
    
    $(document).on('click', '#employeePrevBtn', function() {
        if (currentPage > 1) {
            currentPage--;
            updateEmployeeTable();
        }
    });
    
    $(document).on('click', '#employeeNextBtn', function() {
        var allRows = $('#employeeTable tbody tr');
        var filteredRows = searchTerm ? 
            allRows.filter(function() { return $(this).text().toLowerCase().indexOf(searchTerm.toLowerCase()) > -1; }) : 
            allRows;
        var totalPages = Math.ceil(filteredRows.length / entriesPerPage);
        
        if (currentPage < totalPages) {
            currentPage++;
            updateEmployeeTable();
        }
    });
    
    // Initialize table
    updateEmployeeTable();

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Enhanced delete confirmation with SweetAlert2
    $(document).on('click', '.delete-employee', function(e) {
        e.preventDefault();
        
        const employeeId = $(this).data('employee-id');
        const employeeName = $(this).data('employee-name');
        const row = $(this).closest('tr');
        
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                html: `Do you want to delete employee <strong>"${employeeName}"</strong>?<br><small class="text-muted">This action cannot be undone and will permanently remove all employee data.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash mr-2"></i>Yes, delete employee!',
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
                        title: 'Deleting Employee...',
                        text: 'Please wait while we process your request.',
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
                        const form = document.getElementById(`delete-form-${employeeId}`);
                        if (form) {
                            form.submit();
                        }
                    });
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert2 is not available
            const confirmMessage = `Are you sure you want to delete employee "${employeeName}"? This action cannot be undone and will permanently remove all employee data.`;
            if (confirm(confirmMessage)) {
                // Add loading state to the row
                row.addClass('loading-row');
                
                // Submit the form
                const form = document.getElementById(`delete-form-${employeeId}`);
                if (form) {
                    form.submit();
                }
            }
        }
    });

    // Enhanced deactivate confirmation with SweetAlert2
    $(document).on('click', '.deactivate-employee', function(e) {
        e.preventDefault();
        
        const employeeId = $(this).data('employee-id');
        const employeeName = $(this).data('employee-name');
        const row = $(this).closest('tr');
        
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Deactivate Employee?',
                html: `Do you want to deactivate employee <strong>"${employeeName}"</strong>?<br><small class="text-muted">The employee will be moved to inactive status and won't be able to access the system.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-user-slash mr-2"></i>Yes, deactivate!',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancel',
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    content: 'swal2-content',
                    confirmButton: 'swal2-confirm swal2-deactivate',
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
                        title: 'Deactivating Employee...',
                        text: 'Please wait while we process your request.',
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
                        const form = document.getElementById(`deactivate-form-${employeeId}`);
                        if (form) {
                            form.submit();
                        }
                    });
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert2 is not available
            const confirmMessage = `Are you sure you want to deactivate employee "${employeeName}"? The employee will be moved to inactive status.`;
            if (confirm(confirmMessage)) {
                // Add loading state to the row
                row.addClass('loading-row');
                
                // Submit the form
                const form = document.getElementById(`deactivate-form-${employeeId}`);
                if (form) {
                    form.submit();
                }
            }
        }
    });
    
    // Add smooth scrolling for better UX
    $('html').css('scroll-behavior', 'smooth');
    
    // Animate table rows on load
    $('.premium-table tbody tr').each(function(index) {
        $(this).css({
            'animation-delay': (index * 0.05) + 's',
            'animation-fill-mode': 'forwards'
        });
    });

    // Enhanced hover effects for action buttons
    $('.action-btn').hover(
        function() {
            $(this).css('transform', 'translateY(-2px) scale(1.05)');
        },
        function() {
            $(this).css('transform', 'translateY(0) scale(1)');
        }
    );

    // Add ripple effect to action buttons
    $(document).on('click', '.action-btn', function(e) {
        const button = $(this);
        const ripple = $('<span class="ripple"></span>');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.css({
            width: size + 'px',
            height: size + 'px',
            left: x + 'px',
            top: y + 'px'
        });
        
        button.append(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

}); // End of $(document).ready
</script>
@endsection