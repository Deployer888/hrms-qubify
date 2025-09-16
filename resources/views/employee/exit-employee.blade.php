@extends('layouts.admin')
@section('page-title')
    {{ __('Exit Employee') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        {{--<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
            <div class="all-button-box">
                <a href="{{ route('employee.export') }}" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <i class="fa fa-file-excel"></i> {{ __('Export') }}
                </a>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
            <div class="all-button-box">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto"
                    data-url="{{ route('employee.file.import') }}" data-ajax-popup="true"
                    data-title="{{ __('Import employee CSV file') }}">
                    <i class="fa fa-file-csv"></i> {{ __('Import') }}
                </a>
            </div>
        </div>--}}
    </div>
@endsection

@section('content')
    <style>
        .table-container {
            max-height: 100% !important;
        }
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

        /* body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text-primary);
        } */

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

        .page-title-compact {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 4px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: -0.025em;
        }

        .page-subtitle-compact {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            margin: 0;
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

        /* Compact Stats */
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

        /* Premium Table */
        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-count {
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .premium-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .premium-table thead th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 20px;
            border: none;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .premium-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tbody tr:hover {
            background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
            transform: scale(1.001);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        }

        .premium-table tbody td {
            padding: 16px 20px;
            border: none;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
            vertical-align: middle;
        }

        /* Employee ID Badge */
        .employee-id-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
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

        /* Employee Details */
        .employee-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .employee-name {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 0.9rem;
            margin: 0;
        }

        .employee-email {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .employee-email:hover {
            color: var(--primary-dark);
            text-decoration: none;
            transform: translateX(2px);
        }

        .employee-email::before {
            content: '✉';
            font-size: 0.7rem;
            opacity: 0.7;
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
            /*font-size: 0.65rem;*/
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

        /* Date Badges */
        .date-badge {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 1px solid #cbd5e1;
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .date-badge:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
            border-color: #94a3b8;
        }

        .exit-date-badge {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
            color: #dc2626;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .exit-date-badge:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
            border-color: #f87171;
        }

        /* Action Button */
        .action-btn {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-btn:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* No Data State */
        .no-data-compact {
            text-align: center;
            padding: 60px 40px;
            color: var(--text-muted);
        }

        .no-data-icon-compact {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--text-muted);
            margin: 0 auto 20px;
        }

        .no-data-text-compact {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .no-data-subtitle-compact {
            font-size: 0.9rem;
            color: var(--text-muted);
            max-width: 400px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header-compact {
                padding: 20px;
            }

            .page-title-compact {
                font-size: 1.5rem;
            }

            .stat-card-compact {
                padding: 16px;
                margin-bottom: 12px;
            }

            .stat-number-compact {
                font-size: 1.75rem;
            }

            .premium-table thead th,
            .premium-table tbody td {
                padding: 12px 16px;
                font-size: 0.8rem;
            }

            .tag-stack {
                gap: 2px;
            }

            .info-tag-compact {
                padding: 2px 8px;
                font-size: 0.6rem;
            }
        }

        /* Animation Delays */
        .stat-card-compact:nth-child(1) { animation-delay: 0.1s; }
        .stat-card-compact:nth-child(2) { animation-delay: 0.2s; }
        .stat-card-compact:nth-child(3) { animation-delay: 0.3s; }
        .stat-card-compact:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card-compact,
        .table-container,
        .page-header-compact {
            animation: fadeInUp 0.6s ease forwards;
        }

        /* Focus States */
        .action-btn:focus,
        .employee-id-badge:focus,
        .employee-email:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>

    <!-- Compact Page Header -->
    <div class="page-header-compact">
        <div class="header-content d-flex justify-content-between align-items-center">
            
            <div class="col-md-6 d-flex">
                <div class="header-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="ml-3">
                    <h1 class="page-title-compact">
                        {{ __('Exit Employees') }}
                    </h1>
                    <p class="page-subtitle-compact">{{ __('Manage and track employees who have left the organization') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Statistics -->
    {{-- <div class="row stats-compact">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ count($employees) }}</h3>
                        <p class="stat-label-compact">{{ __('Total Exit') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626;">
                        <i class="fas fa-user-minus"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->where('date_of_exit', '>=', now()->startOfMonth())->count() }}</h3>
                        <p class="stat-label-compact">{{ __('This Month') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card-compact">
                <div class="stat-content">
                    <div>
                        <h3 class="stat-number-compact">{{ $employees->where('date_of_exit', '>=', now()->startOfYear())->count() }}</h3>
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
                        <h3 class="stat-number-compact">{{ $employees->where('is_active', 0)->count() }}</h3>
                        <p class="stat-label-compact">{{ __('Can Reactivate') }}</p>
                    </div>
                    <div class="stat-icon-compact" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #059669;">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Premium Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">
                        <i class="fas fa-users"></i>
                        {{ __('Employee List') }}
                        <span class="table-count">{{ count($employees) }}</span>
                    </h3>
                </div>
                
                @if(count($employees) > 0)
                    <div class="table-responsive">
                        <table class="table premium-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee ID') }}</th>
                                    <th>{{ __('Employee Details') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Joining Date') }}</th>
                                    <th>{{ __('Exit Date') }}</th>
                                    @if (Gate::check('Edit Employee') || Gate::check('Delete Employee'))
                                        <th>{{ __('Actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>
                                            @can('Show Employee')
                                                <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}" 
                                                   class="employee-id-badge">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </a>
                                            @else
                                                <span class="employee-id-badge">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td>
                                            <div class="employee-details">
                                                <div class="employee-name">{{ $employee->name }}</div>
                                                <a href="mailto:{{ $employee->email }}" class="employee-email">
                                                    {{ $employee->email }}
                                                </a>
                                            </div>
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
                                                {{ \Auth::user()->dateFormat($employee->company_doj) }}
                                        </td>
                                        <td>
                                            @if($employee->date_of_exit)
                                                <span class="exit-date-badge">
                                                    {{ \Auth::user()->dateFormat($employee->date_of_exit) }}
                                                </span>
                                            @else
                                                <span class="text-muted">{{ __('Not Set') }}</span>
                                            @endif
                                        </td>
                                        @if (Gate::check('Edit Employee') || Gate::check('Delete Employee'))
                                            <td>
                                                <a href="{{ route('employee.activate', $employee->id) }}" 
                                                   class="action-btn" 
                                                   data-toggle="tooltip"
                                                   data-original-title="{{ __('Reactivate Employee') }}">
                                                    <i class="fas fa-user-check"></i>
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="no-data-compact">
                        <div class="no-data-icon-compact">
                            <i class="fas fa-users-slash"></i>
                        </div>
                        <div class="no-data-text-compact">{{ __('No exit employees found') }}</div>
                        <p class="no-data-subtitle-compact">{{ __('All employees are currently active in the system. This is a good sign for your organization!') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize DataTable if not already done
        if ($('.premium-table').length && !$('.premium-table').hasClass('dataTable')) {
            $('.premium-table').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[4, 'desc']], // Sort by exit date
                columnDefs: [
                    { orderable: false, targets: -1 } // Disable sorting on action column
                ],
                language: {
                    search: "Search employees:",
                    lengthMenu: "Show _MENU_ employees",
                    info: "Showing _START_ to _END_ of _TOTAL_ employees",
                    infoEmpty: "No employees found",
                    zeroRecords: "No matching employees found"
                }
            });
        }
        
        // Add stagger animation to table rows
        $('.premium-table tbody tr').each(function(index) {
            $(this).css({
                'animation-delay': (index * 0.05) + 's',
                'animation-fill-mode': 'forwards'
            });
        });
    });
</script>
@endpush