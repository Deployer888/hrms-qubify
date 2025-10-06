@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Plan Request') }}
@endsection
@push('css-page')
<style>
    :root {
        --primary: #2563eb;
        --secondary: #3b82f6;
        --accent: #60a5fa;
        --info: #93c5fd;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        --shadow-md: 0 8px 25px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 25px 50px rgba(0, 0, 0, 0.15);
        --text-primary: #2d3748;
        --text-secondary: #6b7280;
        --glass-bg: rgba(255, 255, 255, 0.25);
        --glass-border: rgba(255, 255, 255, 0.18);
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --card-shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
    }

    /* Premium Page Header */
    .page-header-premium {
        background: var(--primary);
        border-radius: 20px;
        padding: 30px 40px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header-premium::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .header-left {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .header-text {
        flex: 1;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 25px;
        backdrop-filter: blur(10px);
    }

    .header-icon i {
        font-size: 32px;
        color: white;
    }

    .header-stats {
        display: flex;
        align-items: center;
        gap: 30px;
        flex-shrink: 0;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 800;
        color: white;
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        margin: 5px 0 0 0;
        font-weight: 500;
        color: white;
    }

    /* Premium Table Container */
    .premium-table-container {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255,255,255,0.8);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .premium-table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary);
        opacity: 1;
    }

    .premium-table-container::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.03) 0%, transparent 50%);
        pointer-events: none;
        opacity: 1;
    }

    /* Table Header */
    .table-header {
        padding: 32px 32px 24px;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        position: relative;
        z-index: 2;
    }

    .table-title {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .table-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
    }

    /* Premium Table */
    .table-responsive {
        border-radius: 0 0 var(--border-radius) var(--border-radius);
        overflow: hidden;
        position: relative;
        z-index: 2;
    }

    .premium-table {
        width: 100%;
        margin: 0;
        background: transparent;
    }

    .premium-table thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: none;
        padding: 20px 24px;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        position: relative;
    }

    .premium-table thead th:first-child {
        border-top-left-radius: 0;
    }

    .premium-table thead th:last-child {
        border-top-right-radius: 0;
    }

    .premium-table tbody tr {
        border: none;
        transition: var(--transition);
        position: relative;
    }

    .premium-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
        transform: scale(1.005);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.08);
    }

    .premium-table tbody tr:not(:last-child) {
        border-bottom: 1px solid rgba(102, 126, 234, 0.05);
    }

    .premium-table tbody td {
        padding: 24px;
        vertical-align: middle;
        border: none;
        font-size: 14px;
        color: #374151;
        position: relative;
    }

    /* User Info Styling */
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .user-avatar::before {
        content: '';
        position: absolute;
        inset: -2px;
        background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #f5576c);
        border-radius: 14px;
        z-index: -1;
        animation: rotate 3s linear infinite;
        opacity: 0;
        transition: var(--transition);
    }

    .premium-table tbody tr:hover .user-avatar::before {
        opacity: 1;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .user-email {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    /* Plan Badge */
    .plan-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .plan-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .premium-table tbody tr:hover .plan-badge::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .plan-badge i {
        font-size: 12px;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
    }

    /* Metric Values */
    .metric-value {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .metric-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    /* Duration Badge */
    .duration-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
        color: var(--primary);
        border: 1px solid rgba(37, 99, 235, 0.2);
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        transition: var(--transition);
    }

    .premium-table tbody tr:hover .duration-badge {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(59, 130, 246, 0.15) 100%);
        border-color: rgba(37, 99, 235, 0.3);
        transform: translateY(-1px);
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .status-pending {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .status-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .premium-table tbody tr:hover .status-badge::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .status-badge i {
        font-size: 10px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Action Buttons */
    .premium-btn {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        margin-right: 8px;
        margin-bottom: 4px;
    }

    .premium-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .premium-btn:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .btn-approve {
        background: linear-gradient(135deg, var(--success), #34d399);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-approve:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-reject {
        background: linear-gradient(135deg, var(--danger), #dc2626);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .btn-reject:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: #6b7280;
        position: relative;
        z-index: 2;
    }

    .empty-state i {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 24px;
    }

    .empty-state h3 {
        font-size: 24px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 16px;
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto;
    }

    /* Loading States */
    .loading-row {
        opacity: 0.5;
        pointer-events: none;
        position: relative;
    }

    .loading-row::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #e5e7eb;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 10;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Fade In Animation */
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .premium-table tbody td {
            padding: 20px;
        }
    }

    @media (max-width: 992px) {
        .header-content {
            flex-direction: column;
            gap: 25px;
            text-align: center;
        }

        .header-left {
            justify-content: center;
        }

        .header-stats {
            justify-content: center;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 15px;
        }

        .page-header-premium {
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 16px;
        }

        .table-header {
            padding: 24px 20px 16px;
        }

        .table-title {
            font-size: 20px;
        }

        .table-subtitle {
            font-size: 13px;
        }

        .premium-table {
            font-size: 13px;
        }

        .premium-table thead {
            display: none;
        }

        .premium-table tbody tr {
            display: block;
            margin-bottom: 16px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .premium-table tbody tr:hover {
            transform: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .premium-table tbody td {
            display: block;
            padding: 8px 0;
            border: none;
            text-align: left;
        }

        .premium-table tbody td::before {
            content: attr(data-label) ": ";
            font-weight: 700;
            color: #374151;
            display: inline-block;
            margin-right: 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .premium-table tbody td.action::before {
            display: none;
        }

        .user-info {
            margin-bottom: 8px;
        }

        .premium-btn {
            margin-right: 6px;
            margin-bottom: 6px;
            padding: 6px 12px;
            font-size: 11px;
        }
    }

    @media (max-width: 576px) {
        .page-header-premium {
            padding: 18px;
            margin-bottom: 20px;
        }

        .header-left {
            flex-direction: column;
            gap: 12px;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            margin-right: 0;
            margin-bottom: 8px;
        }

        .header-icon i {
            font-size: 24px;
        }

        .header-stats {
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .stat-item {
            min-width: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-number {
            font-size: 22px;
        }

        .stat-label {
            font-size: 11px;
        }

        .table-header {
            padding: 20px 16px 12px;
        }

        .premium-table tbody tr {
            padding: 16px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }

        .user-name {
            font-size: 14px;
        }

        .user-email {
            font-size: 12px;
        }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush
@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="header-text">
                    <h1>{{ __('Manage Plan Requests') }}</h1>
                    <p>{{ __('Review and approve company plan upgrade requests') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $plan_requests->count() }}</p>
                    <p class="stat-label text-light">{{ __('Total Requests') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ $plan_requests->where('status', 'pending')->count() }}</p>
                    <p class="stat-label text-light">{{ __('Pending') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Premium Table Card --}}
    <div class="premium-table-container">
        <div class="table-header">
            <div>
                <h2 class="table-title">{{ __('Plan Requests') }}</h2>
                <p class="table-subtitle">{{ __('Review and manage all plan upgrade requests') }}</p>
            </div>
        </div>
        
        @if($plan_requests->count() > 0)
            <div class="table-responsive">
                <table class="premium-table table">
                    <thead>
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Plan Details') }}</th>
                            <th>{{ __('Capacity') }}</th>
                            <th>{{ __('Duration') }}</th>
                            <th>{{ __('Request Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plan_requests as $prequest)
                            <tr class="font-style">
                                <td data-label="{{ __('User') }}">
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($prequest->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $prequest->user->name ?? 'Unknown User' }}</div>
                                            <div class="user-email">{{ $prequest->user->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="{{ __('Plan Details') }}">
                                    <div class="plan-badge">
                                        <i class="fas fa-crown"></i>
                                        {{ $prequest->plan->name }}
                                    </div>
                                </td>
                                <td data-label="{{ __('Capacity') }}">
                                    <div class="metric-value">{{ $prequest->plan->max_employees }}</div>
                                    <div class="metric-label">{{ __('Employees') }}</div>
                                    <div class="metric-value">{{ $prequest->plan->max_users }}</div>
                                    <div class="metric-label">{{ __('Users') }}</div>
                                </td>
                                <td data-label="{{ __('Duration') }}">
                                    <div class="duration-badge">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $prequest->duration }}
                                    </div>
                                </td>
                                <td data-label="{{ __('Request Date') }}">
                                    <div class="metric-value">{{ $prequest->created_at->format('M d, Y') }}</div>
                                    <div class="metric-label">{{ $prequest->created_at->format('h:i A') }}</div>
                                </td>
                                <td data-label="{{ __('Status') }}">
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i>
                                        {{ __('Pending') }}
                                    </span>
                                </td>
                                <td data-label="{{ __('Actions') }}" class="action">
                                    <a href="{{ route('plan_request.update', $prequest->id) }}"
                                        class="premium-btn btn-approve">
                                        <i class="fas fa-check"></i>
                                        {{ __('Approve') }}
                                    </a>
                                    <a href="#" class="premium-btn btn-reject" 
                                       data-toggle="tooltip"
                                       data-original-title="{{ __('Reject Request') }}"
                                       data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to reject this request?') }}"
                                       data-confirm-yes="document.getElementById('delete-form-{{ $prequest->id }}').submit();">
                                        <i class="fas fa-times"></i>
                                        {{ __('Reject') }}
                                    </a>
                                    <form method="POST"
                                          action="{{ route('plan_requests.destroy', $prequest->id) }}"
                                          id="delete-form-{{ $prequest->id }}"
                                          style="display: none;">
                                        @method('DELETE')
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>{{ __('No Plan Requests Found') }}</h3>
                <p>{{ __('There are currently no plan upgrade requests to review.') }}</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced hover effects for table rows

    // Enhanced confirmation dialog
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-confirm]')) {
            e.preventDefault();
            const confirmBtn = e.target.closest('[data-confirm]');
            const confirmData = confirmBtn.dataset.confirm.split('|');
            const title = confirmData[0];
            const message = confirmData[1];
            const confirmYes = confirmBtn.dataset.confirmYes;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, reject it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add loading state to row
                        const row = confirmBtn.closest('tr');
                        if (row) {
                            row.classList.add('loading-row');
                        }
                        
                        // Execute the action
                        eval(confirmYes);
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm(title + '\n' + message)) {
                    // Add loading state to row
                    const row = confirmBtn.closest('tr');
                    if (row) {
                        row.classList.add('loading-row');
                    }
                    
                    // Execute the action
                    eval(confirmYes);
                }
            }
        }
    });

    // Loading state for approve buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-approve')) {
            const row = e.target.closest('tr');
            if (row) {
                row.classList.add('loading-row');
            }
        }
    });

    // Button ripple effect
    const buttons = document.querySelectorAll('.premium-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Smooth animations on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeIn 0.6s ease';
            }
        });
    });

    document.querySelectorAll('.premium-table-container').forEach(el => {
        observer.observe(el);
    });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
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
`;
document.head.appendChild(style);
</script>
@endsection 